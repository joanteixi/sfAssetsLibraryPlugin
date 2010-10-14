<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class galleryActions extends sfActions
{
    public function executeIndex()
    {
        $this->dir = 'Albums';
        $this->content = ContingutPeer::getAlbumContent();

    }

    public function executeGetAlbum($request)
    {
// condició per garantir la seguretat del sistema
        if (!strpos($request->getParameter('dir'), 'Albums'))
        {
//            return $this->renderText('<h3>Aquesta carpeta que busques no existeix</h3>');
        }
        $folder = sfAssetFolderPeer::retrieveByPk(trim(strstr($request->getParameter('id'),'_'),'_'));
        $dirs = $folder->getChildren();
        $c = new Criteria();
        $c->add(sfAssetPeer::FOLDER_ID, $folder->getId());
        $this->files = sfAssetPeer::doSelect($c);
        $this->folder = $folder;

        return sfView::SUCCESS;
    }

    public function executeGallery()
    {
        
    }
}
?>
