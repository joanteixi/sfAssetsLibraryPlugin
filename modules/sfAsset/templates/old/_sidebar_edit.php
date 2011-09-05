<?php use_helper('JavascriptBase', 'sfAsset') ?>

<?php if (!$sf_asset->isNew()): ?>
<div class="thumbnail">
    <a href="<?php echo $sf_asset->getUrl('full') ?>"><?php echo asset_image_tag($sf_asset, 'large', array('title' => __('See full-size version', null, 'sfAsset')), null) ?></a>
<p><?php echo __('%weight%', array('%weight%' => $sf_asset->getFilesize('large')), 'sfAsset') ?></p>
<p><?php echo __('%mida% px', array('%mida%' => $sf_asset->getSize('large')), 'sfAsset') ?> <?php echo link_to('Retallar','sfAsset/cropImage?id='.$sf_asset->getId().'&type=large')?></p>
<p><a href="#" id="escalar">Escalar</a></p>
<p id="new_width_large" style="display:none"><input type="text" id="width_large" value="Escriu la nova amplada amb pixels" /><button id="envia_new_width_large">Escalar!</button></p>
</div>
<div class="thumbnail">
    <a href="<?php echo $sf_asset->getUrl('full') ?>"><?php echo asset_image_tag($sf_asset, 'small', array('title' => __('See full-size version', null, 'sfAsset')), null) ?></a>
<p><?php echo __('%weight%', array('%weight%' => $sf_asset->getFilesize('small')), 'sfAsset') ?></p>
<p><?php echo __('%mida% px', array('%mida%' => $sf_asset->getSize('small')), 'sfAsset') ?> <?php echo link_to('Retallar','sfAsset/cropImage?id='.$sf_asset->getId().'&type=small')?></p>
</div>
<p><?php echo auto_wrap_text($sf_asset->getFilename()) ?></p>
<p><?php echo __('%weight% Kb', array('%weight%' => $sf_asset->getFilesize()), 'sfAsset') ?></p>
<p><?php echo __('%mida% px', array('%mida%' => $sf_asset->getSize()), 'sfAsset') ?></p>
<p><?php echo __('Created on %date%', array('%date%' => format_date($sf_asset->getCreatedAt('U'))), 'sfAsset') ?></p>

    <?php echo form_tag('sfAsset/renameAsset', 'method=post') ?>
    <?php echo input_hidden_tag('id', $sf_asset->getId()) ?>
<div class="form-row">
    <label for="new_name">
            <?php echo image_tag('/sfAssetsLibraryPlugin/images/page_edit.png', 'align=top') ?>
            <?php echo link_to_function(__('Rename', null, 'sfAsset'), 'document.getElementById("input_new_name").style.display="block";document.getElementById("new_name").focus()') ?>
    </label>
    <div class="content" id="input_new_name" style="display:none">
            <?php echo input_tag('new_name', $sf_asset->getFilename(), 'style=width:160px') ?>
            <?php echo submit_tag(__('Ok', null, 'sfAsset')) ?>
    </div>
</div>
</form>

    <?php echo form_tag('sfAsset/moveAsset', 'method=post') ?>
    <?php echo input_hidden_tag('id', $sf_asset->getId()) ?>
<div class="form-row">
    <label for="new_folder">
            <?php echo image_tag('/sfAssetsLibraryPlugin/images/page_go.png', 'align=top') ?>
            <?php echo link_to_function(__('Move', null, 'sfAsset'), 'document.getElementById("input_new_folder").style.display="block"') ?>
    </label>
    <div class="content" id="input_new_folder" style="display:none">
            <?php echo select_tag('new_folder', options_for_select(sfAssetFolderPeer::getAllPaths(), $sf_asset->getFolderPath()), 'style=width:170px') ?>
            <?php echo submit_tag(__('Ok', null, 'sfAsset')) ?>
    </div>
</div>
</form>

    <?php echo form_tag('sfAsset/replaceAsset', 'method=post multipart=true') ?>
    <?php echo input_hidden_tag('id', $sf_asset->getId()) ?>
<div class="form-row">
    <label for="new_file">
            <?php echo image_tag('/sfAssetsLibraryPlugin/images/page_refresh.png', 'align=top') ?>
            <?php echo link_to_function(__('Replace', null, 'sfAsset'), 'document.getElementById("input_new_file").style.display="block"') ?>
    </label>
    <div class="content" id="input_new_file" style="display:none">
            <?php echo input_file_tag('new_file', 'size=10') ?>
            <?php echo submit_tag(__('Ok', null, 'sfAsset')) ?>
    </div>
</div>
</form>
<div class="form-row">
        <?php echo image_tag('/sfAssetsLibraryPlugin/images/edit.png', 'align=top') ?>

        <?php echo link_to('Editar miniatures', 'sfAsset/modifyImage?id='.$sf_asset->getId() )?>
</div>
<div class="form-row">
        <?php echo image_tag('/sfAssetsLibraryPlugin/images/page_delete.png', 'align=top') ?>
        <?php echo link_to(__('Delete', null, 'sfAsset'), 'sfAsset/deleteAsset?id='.$sf_asset->getId(), array(
        'post' => true,
        'confirm' => __('Are you sure?', null, 'sfAsset'),
        )) ?>
</div>

<?php endif; ?>

<script type="text/javascript">
    $('#escalar').click(function(){
        $('#new_width_large').show();
    })

    $('#envia_new_width_large').click(function(){
            
         escalarImatge("<?php echo url_for('sfAsset/scaleAssetAjax')?>", <?php echo $sf_asset->getId()?>, 'large', $('#width_large').val());
         
    })
</script>