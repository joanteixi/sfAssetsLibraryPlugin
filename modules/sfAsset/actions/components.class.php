<?php

class sfAssetComponents extends sfComponents
{
    public function executeTopFotos()
    {
        //recull les fotos dintre de l'apartat /fotos/:slug
        $c = new Criteria();
        $c->add(sfAssetPeer::FOLDER_ID, $this->folder_id);
        $this->fotos = sfAssetPeer::doSelect($c);
    }
}
