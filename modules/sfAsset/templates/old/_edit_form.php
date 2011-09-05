<?php use_helper('Object', 'Date', 'sfAsset', 'jQuery') ?>
<?php echo jq_form_remote_tag(array(
    'url' => 'sfAsset/edit',
    'success' => '$dialog.dialog("close");fitxersReload("'.$sf_asset->getFolderPath().'")'), array(
  'id'        => 'sf_admin_edit_form',
  'name'      => 'sf_admin_edit_form',
  'multipart' => false,
)) ?>

<?php echo object_input_hidden_tag($sf_asset, 'getId') ?>


<fieldset id="sf_fieldset_meta" class="">

  <div class="form-row">
    <?php echo label_for('sf_asset[description]', __('Description:', null, 'sfAsset'), '') ?>
    <div class="content<?php if ($sf_request->hasError('sf_asset{description}')): ?> form-error<?php endif; ?>">
      <?php if ($sf_request->hasError('sf_asset{description}')): ?>
        <?php echo form_error('sf_asset{description}', array('class' => 'form-error-msg')) ?>
      <?php endif; ?>
      <?php echo object_textarea_tag($sf_asset, 'getDescription', array(
        'size' => '30x3',
        'control_name' => 'sf_asset[description]',
      )) ?>
    </div>
  </div>


  <div class="form-row">
    <?php echo label_for('sf_asset[type]', __('Type:', null, 'sfAsset'), '') ?>
    <div class="content<?php if ($sf_request->hasError('sf_asset{type}')): ?> form-error<?php endif; ?>">
      <?php if ($sf_request->hasError('sf_asset{type}')): ?>
        <?php echo form_error('sf_asset{type}', array('class' => 'form-error-msg')) ?>
      <?php endif; ?>
      <?php foreach (sfConfig::get('app_sfAssetsLibrary_types', array('image', 'txt', 'archive', 'pdf', 'xls', 'doc', 'ppt')) as $type): ?>
        <?php $options[$type] = $type; ?>
      <?php endforeach; ?>
      <?php echo select_tag('sf_asset[type]', options_for_select($options, $sf_asset->getType())) ?>
    </div>
  </div>

  <?php include_partial('sfAsset/edit_form_custom', array('sf_asset' => $sf_asset)) ?>

</fieldset>

<?php include_partial('edit_actions', array('sf_asset' => $sf_asset)) ?>

</form>