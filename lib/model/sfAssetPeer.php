<?php

/**
 * Subclass for performing query and update operations on the 'sf_asset' table.
 *
 *
 *
 * @package plugins.sfAssetsLibraryPlugin.lib.model
 */
class sfAssetPeer extends BasesfAssetPeer
{
    public static function exists($folderId, $filename)
    {
        $c = new Criteria();
        $c->add(self::FOLDER_ID, $folderId);
        $c->add(self::FILENAME, $filename);

        return self::doCount($c) > 0 ? true : false;
    }

    /**
     * Retrieves a sfAsset object from a relative URL like
     *    /medias/foo/bar.jpg
     * i.e. the kind of URL returned by $sf_asset->getUrl()
     */
    public static function retrieveFromUrl($url)
    {
        $url = sfAssetFolderPeer::cleanPath($url);
        list($relPath, $filename) = sfAssetsLibraryTools::splitPath($url);

        $c = new Criteria();
        $c->add(sfAssetPeer::FILENAME, $filename);
        $c->addJoin(sfAssetPeer::FOLDER_ID, sfAssetFolderPeer::ID);
        $c->add(sfAssetFolderPeer::RELATIVE_PATH, $relPath ?  $relPath : null);

        return sfAssetPeer::doSelectOne($c);
    }
    public static function doOrder($order)
    {
        $con = Propel::getConnection(self::DATABASE_NAME);
        try
        {
            $con->beginTransaction();
            $order = array_reverse($order);
            foreach ($order as $rank => $id)
            {
                $item = sfAssetPeer::retrieveByPk($id);
                if($item->getRank() != $rank)
                {
                    $item->setRank($rank);
                    $item->save();
                }
            }

            $con->commit();
            return true;
        }
        catch (Exception $e)
        {
            $con->rollBack();
            return false;
        }
    }

    public static function doCountInFolderId($folder_id, $is_private)
    {
      $is_public = !$is_private;
      $c = new Criteria();
      $c->add(self::FOLDER_ID, $folder_id);
      $c->add(self::IS_PUBLIC, $is_public);
      $count = self::doCount($c);
      return $count;
    }


}
