<?php use_helper('I18N', 'Date','sfAsset');?>
<?php if($sf_asset->isImage()) : ?>
    <?php include_partial('edit_asset', array('sf_asset' => $sf_asset)); ?>
<?php else : ?>
<div class="foto_details">
  <div class="asset_left">
    <?php echo asset_image_tag($sf_asset, 'large',array('id' => 'original'),null, true) ?>
  </div>

  <div class="descripcio_block">
    <p><strong>Fitxer:</strong> <?php echo auto_wrap_text($sf_asset->getFilename())?></p>

 <?php include_partial('edit_descripcio', array('sf_asset' => $sf_asset));?>
    <div class="clear"></div>
    <p><?php echo link_to('Obre el document',$sf_asset->getUrl());?></p>
    <p><?php echo __('Created on %date%', array('%date%' => format_date($sf_asset->getCreatedAt('U'))), 'sfAsset') ?></p>
</div>
</div>

<?php endif ?>