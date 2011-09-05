<?php

class BasesfAssetActions extends sfActions {

  public function executeIndex() {
    $this->getUser()->getAttributeHolder()->remove('popup', null, 'sf_admin/sf_asset/navigation');
    $this->redirect('sfAsset/list');
  }

  public function executeList() {
    $this->root_folder = sfAssetFolderPeer::retrieveByPath();

    $folder = sfAssetFolderPeer::retrieveByPath($this->getRequestParameter('dir'));
    if (!$folder) {
      if ($this->getUser()->getFlash('sfAsset_folder_not_found')) {
        throw new sfException('You must create a root folder. Use the `php symfony asset:create-root` command for that.');
      } else {
        if ($popup = $this->getRequestParameter('popup')) {
          $this->getUser()->setAttribute('popup', $popup, 'sf_admin/sf_asset/navigation');
        } else {
          $this->getUser()->getAttributeHolder()->remove('popup', null, 'sf_admin/sf_asset/navigation');
        }
        $this->getUser()->setFlash('sfAsset_folder_not_found', true);
        $this->redirect('sfAsset/list');
      }
    }

    $dirs = $folder->getChildren();
    $c = new Criteria();
    $c->add(sfAssetPeer::FOLDER_ID, $folder->getId());
    $this->processSort();
    $sortOrder = $this->getUser()->getAttribute('sort', 'name', 'sf_admin/sf_asset/sort');
    switch ($sortOrder) {
      case 'date':
        $dirs = sfAssetFolderPeer::sortByDate($dirs);
        $c->addDescendingOrderByColumn(sfAssetPeer::CREATED_AT);
        break;
      default:
        $dirs = sfAssetFolderPeer::sortByName($dirs);
        $c->addDescendingOrderByColumn(sfAssetPeer::RANK);
        break;
    }
    $this->files = sfAssetPeer::doSelect($c);
    $this->nb_files = count($this->files);
    if ($this->nb_files) {
      $total_size = 0;
      foreach ($this->files as $file) {
        $total_size += $file->getFilesize();
      }
      $this->total_size = $total_size;
    }
    $this->dirs = $dirs;
    $this->nb_dirs = count($dirs);
    $this->folder = $folder;

    $this->removeLayoutIfPopup();

    return sfView::SUCCESS;
  }

  protected function processSort() {
    if ($this->getRequestParameter('sort')) {
      $this->getUser()->setAttribute('sort', $this->getRequestParameter('sort'), 'sf_admin/sf_asset/sort');
    }
  }

  public function executeSearch() {
    // We keep the search params in the session for easier pagination
    if ($this->getRequest()->hasParameter('search_params')) {
      $search_params = $this->getRequestParameter('search_params');
      if (isset($search_params['created_at']['from']) && $search_params['created_at']['from'] !== '') {
        $search_params['created_at']['from'] = sfI18N::getTimestampForCulture($search_params['created_at']['from'], $this->getUser()->getCulture());
      }
      if (isset($search_params['created_at']['to']) && $search_params['created_at']['to'] !== '') {
        $search_params['created_at']['to'] = sfI18N::getTimestampForCulture($search_params['created_at']['to'], $this->getUser()->getCulture());
      }

      $this->getUser()->getAttributeHolder()->removeNamespace('sf_admin/sf_asset/search_params');
      $this->getUser()->getAttributeHolder()->add($search_params, 'sf_admin/sf_asset/search_params');
    }

    $this->search_params = $this->getUser()->getAttributeHolder()->getAll('sf_admin/sf_asset/search_params');

    $c = $this->processSearch();

    $pager = new sfPropelPager('sfAsset', sfConfig::get('app_sfAssetsLibrary_search_pager_size', 20));
    $pager->setCriteria($c);
    $pager->setPage($this->getRequestParameter('page', 1));
    $pager->setPeerMethod('doSelectJoinsfAssetFolder');
    $pager->init();

    $this->pager = $pager;

    $this->removeLayoutIfPopup();
  }

  protected function processSearch() {
    $search_params = $this->search_params;
    $c = new Criteria();

    if (isset($search_params['path']) && $search_params['path'] !== '') {
      $folder = sfAssetFolderPeer::retrieveByPath($search_params['path']);
      $c->addJoin(sfAssetPeer::FOLDER_ID, sfAssetFolderPeer::ID);
      $c->add(sfAssetFolderPeer::TREE_LEFT, $folder->getTreeLeft(), Criteria::GREATER_EQUAL);
      $c->add(sfAssetFolderPeer::TREE_RIGHT, $folder->getTreeRIGHT(), Criteria::LESS_EQUAL);
    }
    if (isset($search_params['name_is_empty'])) {
      $criterion = $c->getNewCriterion(sfAssetPeer::FILENAME, '');
      $criterion->addOr($c->getNewCriterion(sfAssetPeer::FILENAME, null, Criteria::ISNULL));
      $c->add($criterion);
    } else if (isset($search_params['name']) && $search_params['name'] !== '') {
      $c->add(sfAssetPeer::FILENAME, '%' . trim($search_params['name'], '*%') . '%', Criteria::LIKE);
    }
    if (isset($search_params['author_is_empty'])) {
      $criterion = $c->getNewCriterion(sfAssetPeer::AUTHOR, '');
      $criterion->addOr($c->getNewCriterion(sfAssetPeer::AUTHOR, null, Criteria::ISNULL));
      $c->add($criterion);
    } else if (isset($search_params['author']) && $search_params['author'] !== '') {
      $c->add(sfAssetPeer::AUTHOR, '%' . trim($search_params['author'], '*%') . '%', Criteria::LIKE);
    }
    if (isset($search_params['copyright_is_empty'])) {
      $criterion = $c->getNewCriterion(sfAssetPeer::COPYRIGHT, '');
      $criterion->addOr($c->getNewCriterion(sfAssetPeer::COPYRIGHT, null, Criteria::ISNULL));
      $c->add($criterion);
    } else if (isset($search_params['copyright']) && $search_params['copyright'] !== '') {
      $c->add(sfAssetPeer::COPYRIGHT, '%' . trim($search_params['copyright'], '*%') . '%', Criteria::LIKE);
    }
    if (isset($search_params['created_at'])) {
      if (isset($search_params['created_at']['from']) && $search_params['created_at']['from'] !== '') {
        $criterion = $c->getNewCriterion(sfAssetPeer::CREATED_AT, $search_params['created_at']['from'], Criteria::GREATER_EQUAL);
      }
      if (isset($search_params['created_at']['to']) && $search_params['created_at']['to'] !== '') {
        if (isset($criterion)) {
          $criterion->addAnd($c->getNewCriterion(sfAssetPeer::CREATED_AT, $search_params['created_at']['to'], Criteria::LESS_EQUAL));
        } else {
          $criterion = $c->getNewCriterion(sfAssetPeer::CREATED_AT, $search_params['created_at']['to'], Criteria::LESS_EQUAL);
        }
      }
      if (isset($criterion)) {
        $c->add($criterion);
      }
    }
    if (isset($search_params['description_is_empty'])) {
      $criterion = $c->getNewCriterion(sfAssetPeer::DESCRIPTION, '');
      $criterion->addOr($c->getNewCriterion(sfAssetPeer::DESCRIPTION, null, Criteria::ISNULL));
      $c->add($criterion);
    } else if (isset($search_params['description']) && $search_params['description'] !== '') {
      $c->add(sfAssetPeer::DESCRIPTION, '%' . trim($search_params['description'], '*%') . '%', Criteria::LIKE);
    }

    $this->processSort();
    $sortOrder = $this->getUser()->getAttribute('sort', 'name', 'sf_admin/sf_asset/sort');
    switch ($sortOrder) {
      case 'date':
        $c->addDescendingOrderByColumn(sfAssetPeer::CREATED_AT);
        break;
      default:
        $c->addAscendingOrderByColumn(sfAssetPeer::FILENAME);
        break;
    }

    return $c;
  }

  public function validateCreateFolder() {
    $valid = true;
    $parentFolder = sfAssetFolderPeer::retrieveByPK($this->getRequestParameter('parent_folder'));
    if (!$parentFolder) {
      $this->getRequest()->setError('parent_folder', 'You must provide a valid parent folder');
      $this->error = 'parent_folder';
      $valid = false;
    }
    $name = $this->getRequestParameter('name');
    $children = $parentFolder->getChildren();
    foreach ($children as $dir) {
      if (sfConfig::get('app_sfAssetsLibrary_case_sensitive_filesystem', true)) {
        $test = (strtolower($dir->getName()) == strtolower($name));
      } else {
        $test = ($dir->getName() == $name);
      }
      if ($test) {
        $this->getRequest()->setError('name', '<h1 class="error">Error</h1><p>Ja existeix un directori amb aquest nom</p>');
        $this->error = 'name';
        $valid = false;
      }
    }
    $this->parentFolder = $parentFolder;
    return $valid;
  }

  public function handleErrorCreateFolder() {
    if ($this->getRequest()->getError('match_error'))
      $this->error = 'match_error';
    if (is_array($a = $this->getRequest()->getError($this->error))) {
      $error = $a['name'];
    } else {
      $error = $a;
    }
    iogAjaxUtil::decorateJsonResponse($this->response);
    $json = array('error' => 1, 'error_msg' => $error);
    return $this->renderText(json_encode($json));
  }

  public function executeCreateFolder() {
    if ($this->getRequest()->getMethod() == sfRequest::POST) {
      // Handle the form submission
      // $parentFolder = sfAssetFolderPeer::retrieveByPath($this->getRequestParameter('parent_folder','uploads'));
      $parentFolder = sfAssetFolderPeer::retrieveByPK($this->getRequestParameter('parent_folder'));
      if (!$parentFolder) {
        iogAjaxUtil::decorateJsonResponse($this->response);
        $json = array('error' => 1, 'error_msg' => 'Error en la creació de la carpeta: No es pot trobar el directori pare.');
        return $this->renderText(json_encode($json));
      }
      $folder = new sfAssetFolder();
      $folder->setName(sfAssetsLibraryTools::sanitizeFolderName($this->getRequestParameter('name')));
      $folder->insertAsLastChildOf($parentFolder);
      $folder->save();

      // return ok
      //retorn json
      iogAjaxUtil::decorateJsonResponse($this->response);
      $json = array('id' => $folder->getId());
      return $this->renderText(json_encode($json));
    }

    return sfView::ERROR;
  }

  public function executeMoveFolder() {
    $this->forward404Unless($this->getRequest()->getMethod() == sfRequest::POST);
    $folder = sfAssetFolderPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($folder);
    $targetFolder = sfAssetFolderPeer::retrieveByPath($this->getRequestParameter('new_folder'));

    try {
      $folder->move($targetFolder);
      $this->getUser()->setFlash('notice', 'The folder has been moved');
    } catch (sfAssetException $e) {
      $this->getUser()->setFlash('warning_message', $e->getMessage());
      $this->getUser()->setFlash('warning_params', $e->getMessageParams());
    }

    return $this->redirectToPath('sfAsset/list?dir=' . $folder->getRelativePath());
  }


  public function executeDeleteFolder() {
    if ($this->getRequest()->getMethod() != sfRequest::POST) {
      return $this->returnJsonError('Hi ha un error i no es pot esborrar la carpeta');
    }

    $folder = sfAssetFolderPeer::retrieveByPk($this->getRequestParameter('id'));
    if (!$folder) {
      return $this->returnJsonError('Hi ha un error i no es pot esborrar la carpeta');
    }

    try {
      $folder->delete();
      iogAjaxUtil::decorateJsonResponse($this->getResponse());
      return $this->renderText("[ok]");
    } catch (sfAssetException $e) {
           return $this->returnJsonError(strtr($e->getMessage(),$e->getMessageParams()));

      }
  }

  public function executeAddQuick() {
    $folder = sfAssetFolderPeer::retrieveByPath($this->getRequestParameter('parent_folder'));
    $this->forward404Unless($folder);
    try {
      $asset = new sfAsset();
      $asset->setsfAssetFolder($folder);
      $asset->setDescription($this->getRequest()->getFileName('new_file'));
      try {
        $asset->setAuthor($this->getUser()->getUsername());
      } catch (sfException $e) {
        // no getUsername() method in sfUser, all right: do nothing
      }
      $asset->setFilename($this->getRequest()->getFileName('new_file'));
      $asset->create($this->getRequest()->getFilePath('new_file'));
      $asset->save();
    } catch (sfAssetException $e) {
      $this->getUser()->setFlash('warning_message', $e->getMessage());
      $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      $this->redirectToPath('sfAsset/list?dir=' . $folder->getRelativePath());
    }

    if ($this->getUser()->hasAttribute('popup', 'sf_admin/sf_asset/navigation')) {
      if ($this->getUser()->getAttribute('popup', null, 'sf_admin/sf_asset/navigation') == 1) {
        $this->redirect('sfAsset/tinyConfigMedia?id=' . $asset->getId());
      } else {
        $this->redirectToPath('sfAsset/list?dir=' . $folder->getRelativePath());
      }
    }
    $this->redirect('sfAsset/edit?id=' . $asset->getId());
  }

  /**
   * Retorna un error per AJAX al client
   * @param String $error_msg
   * @return json
   */
  public function returnJsonError($error_msg = "Hi ha un error") {
    iogAjaxUtil::decorateJsonResponse($this->getResponse());
    $json = array('error' => 1, 'error_msg' => $error_msg);
    return $this->renderText(json_encode($json));
  }

  public function executeMassUpload($request) {

    if ($this->getRequest()->getMethod() == sfRequest::POST) {
      $folder = sfAssetFolderPeer::retrieveByPath($this->getRequestParameter('parent_folder'));
      $this->forward404Unless($folder);
      try {
        $nbFiles = 0;
        for ($i = 0; $i < intval($request->getParameter('uploader_count')); $i++) {
          $filename = $this->getRequest()->getParameter('uploader_' . $i . '_name');
          $asset = new sfAsset();
          $asset->setsfAssetFolder($folder);
          $asset->setDescription($filename);
          try {
            $asset->setAuthor($this->getUser()->getUsername());
          } catch (sfException $e) {
            // no getUsername() method in sfUser, all right: do nothing
          }
          $asset->setFilename($filename);
          $asset->create(sfConfig::get('sf_web_dir') . "/uploads/temp/" . $filename);
          $asset->save();
          $nbFiles++;
        }
      } catch (sfAssetException $e) {
        $this->getUser()->setFlash('warning_message', $e->getMessage());
        $this->getUser()->setFlash('warning_params', $e->getMessageParams());
        $this->redirectToPath('sfAsset/list?dir=' . $folder->getRelativePath());
      }
      $this->getUser()->setFlash('notice', 'Files successfully uploaded');
      $this->redirectToPath('sfAsset/list?dir=' . $folder->getRelativePath());
    }
  }

  public function executeDeleteAsset() {
    //id ve de la forma asset_id_54...
    $id = substr(strrchr($this->getRequestParameter('id'), '_'), 1);
    $sf_asset = sfAssetPeer::retrieveByPk($id);
    $this->forward404Unless($sf_asset);
    $folderPath = $sf_asset->getFolderPath();
    try {
      $sf_asset->delete();
    } catch (PropelException $e) {
      return $this->renderText('0');
      //$this->getRequest()->setError('delete', 'Impossible to delete asset, probably due to related records');
      //return $this->forward('sfAsset', 'edit');
    }
    return $this->renderText('1');
    //return $this->redirectToPath('sfAsset/list?dir='.$folderPath);
  }

  public function executeCreate() {
    return $this->forward('sfAsset', 'edit');
  }

  public function executeSave(sfWebRequest $request) {
    return $this->forward('sfAsset', 'edit');
  }

  public function handleErrorEdit() {
    $this->preExecute();
    $this->sf_asset = $this->getsfAssetOrCreate();
    $this->updatesfAssetFromRequest();

    $this->labels = $this->getLabels();

    return sfView::SUCCESS;
  }

  public function executeEdit(sfWebRequest $request) {
    $this->sf_asset = $this->getsfAssetOrCreate();

    if ($this->getRequest()->getMethod() == sfRequest::POST) {
      $this->updatesfAssetFromRequest();

      $this->sf_asset->save();

      $this->getUser()->setFlash('notice', 'Your modifications have been saved');
      if ($request->isXmlHttpRequest()) {
        return $this->renderText('S\'han grabat les teves modificacions');
      } else {
        return $this->redirectToPath('sfAsset/list?dir=' . $this->sf_asset->getFolderPath());
      }
    }
  }

  public function executeMoveAsset() {
    sfLoader::loadHelpers(array('I18N'));
    $this->forward404Unless($this->getRequest()->getMethod() == sfRequest::POST);
    $id = substr(strrchr($this->getRequestParameter('id'), '_'), 1);
    $sf_asset = sfAssetPeer::retrieveByPk($id);
//        $this->forward404Unless($sf_asset);
    $folder = sfAssetFolderPeer::retrieveByPath($this->getRequestParameter('new_folder'));
//        $this->forward404Unless($folder);
    if ($folder->getId() != $sf_asset->getFolderId()) {
      try {
        $sf_asset->move($folder);
        $sf_asset->save();
        return $this->renderText('success');
      } catch (sfAssetException $e) {
        $error = __($e->getMessage(), $e->getMessageParams());
      }
    } else {
      $error = "La carpeta de destí és la mateixa que la carpeta original. No s'ha mogut el fitxer.";
    }

    return $this->renderText('<h2 class="error">Error: ' . $error . '</h2>');
  }

  /**
   * Returns a list of folder structure.
   * Called via AJAX
   *
   * @param sfWebRequest $request
   */
  public function executeGetFolderList(sfWebRequest $request) {
    $folders = sfAssetFolderPeer::getAllPaths();
    $option = '';
    foreach ($folders as $folder) {
      $option .= sprintf('<option value = "%s">%s</option>', $folder, $folder);
    }
    return $this->renderText($option);
  }

  /**
   * Graba la descripcio en la cultura corresponent.
   *
   * @param <type> $request arriba el id i la cultura en mateixa variable: id_culture
   * @return <type>
   */
  public function executeSaveDescription($request) {
    $this->forward404Unless($this->getRequest()->getMethod() == sfRequest::POST);
    $params = explode('-', $this->getRequestParameter('id'));

    $sf_asset = sfAssetPeer::retrieveByPk($params[0]);
    $this->forward404Unless($sf_asset);
    $sf_asset->setCulture($params[1]);
    $sf_asset->setDescription($request->getParameter('value'));
    $sf_asset->save();
    return $this->renderText($request->getParameter('value'));
  }

  public function executeRenameAsset() {
    $id = substr(strrchr($this->getRequestParameter('id'), '_'), 1);
    $sf_asset = sfAssetPeer::retrieveByPk($id);
    $this->forward404Unless($sf_asset);
    $name = sfAssetsLibraryTools::sanitizeName($this->getRequestParameter('new_name'));
    $this->forward404Unless($name);
    //afegeix extensió antiga
    $extension = strrchr($sf_asset->getFilename(),'.');
    $name = $name.$extension;
    if ($sf_asset->getFilename() != $name) {
      try {
        $sf_asset->move($sf_asset->getsfAssetFolder(), $name);
        $sf_asset->save();
        $this->getUser()->setFlash('notice', 'The file has been renamed');
      } catch (sfAssetException $e) {
        iogAjaxUtil::decorateJsonResponse($this->getResponse());
        return $this->renderText('{The target folder}');
      }
    } else {
      $this->getUser()->setFlash('notice', 'The target name is the same as the original name. The asset has not been renamed.');
    }
    return $this->renderText($name);
  }

  public function executeReplaceAsset() {
    $this->forward404Unless($this->getRequest()->getMethod() == sfRequest::POST);
    $sf_asset = sfAssetPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($sf_asset);
    if ($uploaded_filename = $this->getRequest()->getFileName('new_file')) {
      // physically replace asset
      $sf_asset->destroy();
      $sf_asset->create($this->getRequest()->getFilePath('new_file'), true, false);
    }

    $this->getUser()->setFlash('notice', 'The file has been replaced');

    return $this->redirect('sfAsset/edit?id=' . $sf_asset->getId());
  }

  protected function updatesfAssetFromRequest() {
    $sf_asset = $this->getRequestParameter('sf_asset');
    if (isset($sf_asset['description'])) {
      $this->sf_asset->setDescription($sf_asset['description']);
    }
    if (isset($sf_asset['author'])) {
      $this->sf_asset->setAuthor($sf_asset['author']);
    }
    if (isset($sf_asset['copyright'])) {
      $this->sf_asset->setCopyright($sf_asset['copyright']);
    }
    if (isset($sf_asset['type'])) {
      $this->sf_asset->setType($sf_asset['type']);
    }
  }

  protected function removeLayoutIfPopup() {
    if ($popup = $this->getRequestParameter('popup')) {
      $this->getUser()->setAttribute('popup', $popup, 'sf_admin/sf_asset/navigation');
      $this->getUser()->setAttribute('CKEditorFuncNum', $this->getRequestParameter('CKEditorFuncNum'), 'sf_admin/sf_asset/navigation');
    } else {
      $this->getUser()->getAttributeHolder()->remove('popup', null, 'sf_admin/sf_asset/navigation');
    }
    if ($this->getUser()->hasAttribute('popup', 'sf_admin/sf_asset/navigation')) {
      $this->setLayout($this->getContext()->getConfiguration()->getTemplateDir('sfAsset', 'popupLayout.php') . DIRECTORY_SEPARATOR . 'popupLayout');
      $this->popup = true;
    } else {
      $this->popup = false;
    }
  }

  protected function getsfAssetOrCreate($id = 'id') {
    if (!$this->getRequestParameter($id)) {
      $sf_asset = new sfAsset();
    } else {
      $sf_asset = sfAssetPeer::retrieveByPk($this->getRequestParameter($id));

      $this->forward404Unless($sf_asset);
    }

    return $sf_asset;
  }

  protected function redirectToPath($path, $statusCode = 302) {
    $url = $this->getController()->genUrl($path, true);
    $url = str_replace('%2F', '/', $url);

    if (sfConfig::get('sf_logging_enabled')) {
      $this->getContext()->getLogger()->info('{sfAction} redirect to "' . $url . '"');
    }

    $this->getController()->redirect($url, 0, $statusCode);

    throw new sfStopException();
  }

  public function executeTinyConfigMedia() {
    $this->forward404Unless($this->hasRequestParameter('id'));
    $this->sf_asset = sfAssetPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->sf_asset);

    $this->setLayout($this->getContext()->getConfiguration()->getTemplateDir('sfAsset', 'popupLayout.php') . DIRECTORY_SEPARATOR . 'popupLayout');

    return sfView::SUCCESS;
  }

}