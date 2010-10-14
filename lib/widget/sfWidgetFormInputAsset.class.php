<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

/**
 * sfWidgetFormInputFile represents an upload HTML input tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormInputFile.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class sfWidgetFormInputAsset extends sfWidgetForm
{
    /**
     * @param array $options     An array of options
     * @param array $attributes  An array of default HTML attributes
     *
     * @see sfWidgetFormInput
     */
    protected function configure($options = array(), $attributes = array())
    {
        $this->addOption('id', 'subfoto_content_foto');

    }

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        //return $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $name, 'value' => $value), $attributes));
        use_helper('Form', 'I18N', 'Javascript');
        use_javascript('/sfAssetsLibraryPlugin/js/main', 'last');
        //$options = _convert_options($options);
        //$type = 'all';
        /*if (isset($options['images_only']))
  {
    $type = 'image';
    unset($options['images_only']);
  }
  if(!isset($options['id']))
  {
    $options['id'] = get_id_from_name($name);
  }

  $form_name = 'this.previousSibling.previousSibling.form.name';
  if (isset($options['form_name']))
  {
    $form_name = "'".$options['form_name']."'";
    unset($options['form_name']);
  }
        */
        // The popup should open in the currently selected subdirectory
        $form_name = '$("form").attr("name")';
        $type = 'image';
        if ($value)
        {
            $html = '<div id=foto_preview>'.asset_image_tag(sfAssetPeer::retrieveByPK($value), 'small').'</div>';
        } else
        {
            $html = '<div id=foto_preview></div>';
        }
        $html .= input_hidden_tag($name, $value, array('id' => $this->getOption('id'))) . '&nbsp;';
        $html .= '<p class="enlinia">'.image_tag('/sfAssetsLibraryPlugin/images/folder_open', array(
                'alt' => __('Insert Image'),
                'style' => 'cursor: pointer; vertical-align: middle',
                'onclick' => "
    document.getElementById('foto_preview').innerHTML = '';
initialDir = document.getElementById('".$this->getOption('id')."').value.replace(/\/[^\/]*$/, '');
      if(!initialDir) initialDir = '".sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media')."';
      sfAssetsLibrary.openWindow({
        form_name: ".$form_name.",
        field_name: '".$name."',
        type: '".$type."',
        url: '".url_for('sfAsset/list?dir=PLACEHOLDER')."?popup=3'.replace('PLACEHOLDER', initialDir),
        scrollbars: 'yes'
      });"
                )).' Canviar o afegir -- ';

        $html .= link_to_function('Esborrar', sprintf("$('#%s').val(''); $('#foto_preview').hide('slow')",$this->getOption('id'))).'</p>';
        ;
        return $html;


    }
}
