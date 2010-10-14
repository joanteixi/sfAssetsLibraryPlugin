<?php use_helper('sfAsset','jQuery','Text') ?>
<div class="assetImage" id="asset_id_<?php echo $sf_asset->getId() ?>" >
    <div class="thumbnails">
        <?php echo link_to_asset_action(asset_image_tag($sf_asset, 'small', array('width' => 84), isset($folder) ? $folder->getRelativePath() : null), $sf_asset) ?>
    </div>

    <div class="assetComment">
 <div class='filename'><?php echo auto_wrap_text($sf_asset->getFilename()) ?></div>
        <div class="details">
                <?php echo $sf_asset->getFilesize() ?> Ko
        </div>
    </div>
</div>
