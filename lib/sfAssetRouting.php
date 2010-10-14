<?php

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Francois Zaninotto <FRANCOIS.ZANINOTTO@symfony-project.com>
 */
class sfAssetRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();

    // preprend our routes
    $r->prependRoute('sf_guard_signin', new sfRoute('/login', array('module' => 'sfGuardAuth', 'action' => 'signin')));

    $r->prependRoute('sf_asset_library_dir', new sfRoute('/sfAsset/dir/:dir', array(
        'module'    => 'sfAsset',
        'action'    => 'list',
        'dir'       => sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media')
      ),
      array('dir' => '.*?'))
    );
     $r->prependRoute('sf_asset_mass_upload', new sfRoute('/sfAsset/massUpload/:parent_folder', array(
        'module'    => 'sfAsset',
        'action'    => 'massUpload',
        'parent_folder'       => sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media')
      ),
      array('parent_folder' => '.*?'))
    );
  }
}
