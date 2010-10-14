<?php

require_once(sfConfig::get('sf_plugins_dir') . '/sfAssetsLibraryPlugin/modules/sfAsset/lib/BasesfAssetActions.class.php');

class sfAssetActions extends BasesfAssetActions {

  public function executeAjaxSort() {
    $order = $this->getRequestParameter('asset_id');

    $flag = sfAssetPeer::doOrder($order);
    return $flag ? sfView::NONE : $this->renderText('Error');
  }

  /**
   * Retallar la imatge.
   * Options:
   *   * size: small, large, full
   * @param Array mides conté els elements:
   *      ** width
   *      ** height
   *      ** x -> coordenada x on tallar
   *      ** y -> coordenada y on tallar
   *      ** type -> imatge sobre la que sobreescriure la imatge tallada.
   */
  public function executeCropImage(sfWebRequest $request) {

    $this->asset = sfAssetPeer::retrieveByPK($request->getParameter('id'));

    if ($request->isMethod('POST')) {
      $params = $request->getParameter('mides');
      $folder = $this->asset->getFolderPath();
      $filename = $this->asset->getFilename();
      $source = sfAssetsLibraryTools::getThumbnailPath($folder, $filename, $params['type']);

      //crop image
      sfAssetsLibraryTools::createImageCut($source, sfAssetsLibraryTools::getThumbnailPath($folder, $filename, $params['type']), $params['width'], $params['height'], $params['x'], $params['y'], $params['type'] == 'small' ? true : false);
      return $this->renderPartial('show_asset', array('sf_asset' => $this->asset));
    }
  }

  /**
   *
   * Escala la imatge.
   *
   * @param params $request
   * @param int id -> asset id
   * @param int w -> width to scale
   * @param int h -> height to scale
   */
  public function executeScaleAssetAjax($request) {
    $this->asset = sfAssetPeer::retrieveByPK($request->getParameter('id'));
    $this->scaleAsset($request->getParameter('w'), $request->getParameter('type', 'large'), $request->getParameter('original_type', 'full'));

    return $this->renderPartial('show_asset', array('sf_asset' => $this->asset));
  }

  /**
   * Escala el asset amb type a la mida enviada
   * @param integer $width width of new image in px
   * @param string $type type of asset: small, large, full (or another that has been defined)
   */
  protected function scaleAsset($width, $dest_type = 'large', $original_type='full') {
    $folder = $this->asset->getFolderPath();
    $filename = $this->asset->getFilename();
    $source = sfAssetsLibraryTools::getThumbnailPath($folder, $filename, $original_type);
    $dest = sfAssetsLibraryTools::getThumbnailPath($folder, $filename, $dest_type);
    sfAssetsLibraryTools::scaleImage($source, $dest, $width, false);
  }

  public function executeActualitzarDescripcio($request) {
    $id = $request->getParameter('id');
    $asset = sfAssetPeer::retrieveByPK($id);
    $asset->setDescription($request->getParameter('value'));
    $asset->save();
    return $this->renderText($request->getParameter('value'));
  }

  /**
   * Funció per AJAX per retornar el thumbnail d'un asset. Això ve de la versió anterior de sfAsset. Crec q no serveix.
   */
  public function executeReturnAssetTag($request) {
    $img = sfAssetPeer::retrieveByPK($request->getParameter('value'));
    return $this->renderText($img->getUrl('small'));
  }

  /**
   * !!
   * Retorna llista de subdirectoris sota el directori enviat
   *
   * @param sfWebRequest $request
   * @return template
   */
  public function executeDirList(sfWebRequest $request) {
    $is_root = false;
    if ($request->hasParameter('id') && $request->getParameter('id') != 0) {
      $folder = sfAssetFolderPeer::retrieveByPK($request->getParameter('id'));
    } else {
      $is_root = true;
      $folder = sfAssetFolderPeer::retrieveRoot();
    }
    $dirs = $folder->getChildren();
    $children = sfAssetFolderPeer::sortByName($dirs);
    $children_nodes = array();
    foreach ($children as $dir) {
      $children_nodes[] = array(
          'data' => array('title' => $dir->getName(), 'attr' => array('class' => 'directory')),
          'attr' => array('id' => $dir->getId(), 'class' => ''),
          'state' => $dir->hasChildren() ? 'closed' : '',
      );
    }
    if ($is_root) {
      $nodes = array('data' => 'Mediateca', 'attr' => array('id' => 1), 'state' => 'open', 'children' => $children_nodes);
    } else {
      $nodes = $children_nodes;
    }
    /**
      <!--  <img id="dir_<?php echo $dir->getId()?>" rel="/<?php echo $dir->getRelativePath()?>/" width="18" height="16" class="clickable tree <?php echo $dir->hasChildren() ? 'tree_plus' : ''?> folder_actions" src="/sfAssetsLibraryPlugin/images/spacer.gif" />-->
      <!-- <img width="18" height="16" class="tree tree_folder" rel="/<?php echo $dir->getRelativePath()?>/" src="/sfAssetsLibraryPlugin/images/spacer.gif" />-->
      <a id="id_<?php echo $dir->getId()?>" href="#<?php echo $dir->getRelativePath()?>" class="folder_editable clickable context" rel="/<?php echo $dir->getRelativePath()?>/"><?php echo $dir->getName()?></a>
     * */
    //retorna json,
    iogAjaxUtil::decorateJsonResponse($this->getResponse());
    $array = array(array(
            'data' => '1r node',
            'attr' => array('id' => 1),
            'children' => array('fill1', 'fill2')
        ),
        array(
            'data' => '2nnode',
            'state' => 'closed',
            'attr' => array('id' => 2)
        )
    );


    return $this->renderText(json_encode($nodes));

    return sfView::SUCCESS;
  }

  /**
   * Returns list of files into a especific dir
   * @param <type> $request
   */
  public function executeFilesList($request) {
    //$folder = sfAssetFolderPeer::retrieveByPath($request->getParameter('dir'));
    $folder = sfAssetFolderPeer::retrieveByPK($request->getParameter('id'));
    $c = new Criteria();
    $c->add(sfAssetPeer::FOLDER_ID, $folder->getId());
    $this->files = sfAssetPeer::doSelect($c);

    if ($request->getParameter('popup') == 0) {
//            $this->getUser()->getAttributeHolder()->remove('popup', null, 'sf_admin/sf_asset/navigation');
    } else {
      $this->getUser()->setAttribute('popup', $request->getParameter('popup'), 'sf_admin/sf_asset/navigation');
    }
  }

  /**
   * Si petició arriba per "post" guarda la imatge.
   *
   * Sinó, només template que carrega el diàleg d'upload. Rep un id del folder on ha d'anar el fitxer.
   * @param <type> $request
   */
  public function executeUploadFile($request) {

    if ($this->getRequest()->getMethod() == sfRequest::POST) {
      $folder = sfAssetFolderPeer::retrieveById($this->getRequestParameter('parent_folder_id'));
      return $this->returnJsonError('No existeix aquesta carpeta');
      try {

        $filename = $this->getRequest()->getParameter('uploader_' . $i . '_name');
        $asset = new sfAsset();
        $asset->setsfAssetFolder($folder);
        $asset->setDescription($filename);
        $asset->setFilename($filename);
        $asset->create(sfConfig::get('sf_web_dir') . "/" . sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media') . "/temp/" . $filename);
        $asset->save();
        $nbFiles++;
      } catch (sfException $e) {
        return $this->returnJsonError($e->getMessage());
      }

      iogAjaxUtil::decorateJsonResponse($this->getResponse());
      return $this->renderText('{ok}');
    }
    /*
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
      $asset->create(sfConfig::get('sf_web_dir') . "/" . sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media') . "/temp/" . $filename);
      $asset->save();
      $nbFiles++;
      }
      } catch (sfAssetException $e) {
      $this->getUser()->setFlash('warning_message', $e->getMessage());
      $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      return $this->renderText('Error: ' . $e->getMessage());
      }
      $this->getUser()->setFlash('notice', 'Files successfully uploaded');
      $this->renderText('El fitxer s\'ha grabat correctament');
      }

     */
  }

  /**
   * Recull el document enviat per swfupload.
   * Cal fer diverses coses per fer-lo funcionar en el cas que haguem de guardar la sessió:
   * 1. Fer servir el iogSessionStorage
   * 2. A factories.yml afegir:
   *       storage:
   *         class: iogSessionStorage
   *         param:
   *          session_name: my_session
   *
   * 3. En el post params de l'objecte swfupload:    post_params: {"swfaction" : "<?php echo session_id(); ?>"},
   *
   * @param <type> $request
   * @return <type>
   */
  public function executeUploadSwf($request) {

    //si la carpeta on s'ha de copiar no existeix, torna un error
    $folder = sfAssetFolderPeer::retrieveByPK($this->getRequestParameter('parent_folder_id'));
    if (!$folder) {
      return $this->returnJsonError('No existeix aquesta carpeta');
    }
    //procesa el upload
    $valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';    // Characters allowed in the file name (in a Regular Expression format)
    $upload_name = "Filedata";
    $filename = preg_replace('/[^' . $valid_chars_regex . ']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
    $save_path = sfConfig::get('sf_web_dir') . "/media/temp/";


    //graba la imatge en la BD

    $asset = new sfAsset();
    $asset->setsfAssetFolder($folder);
    $asset->setDescription($filename);
    $asset->setFilename($filename);
    $asset->create($_FILES[$upload_name]["tmp_name"]);
    $asset->save();
    iogAjaxUtil::decorateJsonResponse($this->getResponse());
    //retorna la ruta al thumbnail per mostrar-lo en el upload.

    return $this->renderText($asset->getUrl("small"));
  }

  /**
   * Acció per gestionar la pujada de fitxers a través de plupload
   *
   */
  public function executePlupload() {
    $targetDir = sfConfig::get('sf_web_dir') . "/" . sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media') . "/temp";
    $cleanupTargetDir = false; // Remove old files
    $maxFileAge = 60 * 60; // Temp file age in seconds
    // 5 minutes execution time
    @set_time_limit(5 * 60);
    // usleep(5000);
    // Get parameters
    $chunk = $this->getRequestParameter('chunk', 0);
    $chunks = $this->getRequestParameter('chunk', 0);
    $fileName = $this->getRequestParameter('name', '');

    // Clean the fileName for security reasons
    $fileName = preg_replace('/[^\w\._ -]+/', '', $fileName);

    // Create target dir
    if (!file_exists($targetDir))
      if (!mkdir($targetDir)) {
        throw new sfException(sprintf('No es pot crear el directori "%s"', $targetDir));
      }

    // Remove old temp files
//        if (is_dir($targetDir) && ($dir = opendir($targetDir)))
//        {
//            while (($file = readdir($dir)) !== false)
//            {
//                $filePath = $targetDir . DIRECTORY_SEPARATOR . $file;
//
//                // Remove temp files if they are older than the max age
//                if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
//                    @unlink($filePath);
//            }
//
//            closedir($dir);
//        } else
//            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
//
    // Look for the content type header
    $response = $this->getResponse();
    if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
      $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

    if (isset($_SERVER["CONTENT_TYPE"]))
      $contentType = $_SERVER["CONTENT_TYPE"];

    if (strpos($contentType, "multipart") !== false) {
      if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
        // Open temp file
        $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
        if ($out) {
          // Read binary input stream and append it to temp file
          $in = fopen($_FILES['file']['tmp_name'], "rb");

          if ($in) {
            while ($buff = fread($in, 4096))
              fwrite($out, $buff);
          } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

          fclose($out);
          unlink($_FILES['file']['tmp_name']);
        } else
        // caldria retornar error http 500 o 102??
          return $this->renderText('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
      } else
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    } else {
      // Open temp file
      $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
      if ($out) {
        // Read binary input stream and append it to temp file
        $in = fopen("php://input", "rb");

        if ($in) {
          while ($buff = fread($in, 4096))
            fwrite($out, $buff);
        } else
          die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

        fclose($out);
      } else
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    }

    // Return JSON-RPC response
    die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
  }

  /**
   * Canvia el nom de la carpeta.
   * @return json
   */
  public function executeRenameFolder() {

    $this->forward404Unless($this->getRequest()->getMethod() == sfRequest::POST);
    $folder = sfAssetFolderPeer::retrieveByPk($this->getRequestParameter('id'));
    if (!$folder) {
      $this->returnJsonError('No existeix aquesta carpeta');
    }
    $newName = $this->getRequestParameter('value');
    try {
      $folder->rename($newName);
    } catch (sfAssetException $e) {
      return $this->returnJsonError('No es pot canviar el nom d\'aquesta carpeta');
    }
    iogAjaxUtil::decorateJsonResponse($this->getResponse());
    return $this->renderText("[{ok}]");
  }

  /**
   * Crida per AJAX
   * Canvia l'estat de la propietat is_public del asset
   * @param sfWebRequest $request
   */
  public function executeToogleIsPublic(sfWebRequest $request) {
    $asset = sfAssetPeer::retrieveByPK($request->getParameter('id'));
    if (!$asset)
      return $this->renderText('error');

    $asset->setIsPublic($request->getParameter('value'));
    $asset->save();

    return $this->renderText('ok');
  }

}