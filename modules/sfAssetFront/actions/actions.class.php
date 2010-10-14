<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class sfAssetFrontActions extends sfActions {

  /**
   * Download an asset previous verification of credentials.
   */
  public function executeDownload(sfWebRequest $request) {
    $this->forward404Unless($asset = sfAssetPeer::retrieveByPK($request->getParameter('id')));

    // si asset és privat només es baixa si user té credencials
    if (!$asset->getIsPublic() && !$this->getUser()->isAuthenticated())
    {
     return  $this->redirect404();
    }
      $response = $this->getResponse();
      $finfo = new finfo(FILEINFO_MIME); // return mime type ala mimetype extension
      $mimetype = $finfo->file($asset->getFullPath());
//      $mimetype = mime_content_type($asset->getFullPath());
      $response->setHttpHeader('Content-Type', "$mimetype");
      $response->setHttpHeader('Content-Disposition', 'attachment; filename="'.$asset->getFilename().'"');
      return $this->renderText(file_get_contents($asset->getFullPath()));
  }

}

?>
