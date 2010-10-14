<?php
class galleryComponents extends sfComponents
{
    /**
     * Retorna un llistat d'albums.
     * @param <type> $request
     */
    public function executeLlistatAlbums($request)
    {
        $folder = sfAssetFolderPeer::retrieveByPath('Albums');
        $dirs = $folder->getChildren();
        $dirs = sfAssetFolderPeer::sortByName($dirs);
        $this->dirs = $dirs;
    }
}


?>
