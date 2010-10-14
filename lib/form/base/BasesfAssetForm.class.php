<?php

/**
 * sfAsset form base class.
 *
 * @method sfAsset getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 24051 2009-11-16 21:08:08Z Kris.Wallsmith $
 */
abstract class BasesfAssetForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'folder_id'         => new sfWidgetFormPropelChoice(array('model' => 'sfAssetFolder', 'add_empty' => false)),
      'filename'          => new sfWidgetFormInputText(),
      'description_antic' => new sfWidgetFormTextarea(),
      'author'            => new sfWidgetFormInputText(),
      'copyright'         => new sfWidgetFormInputText(),
      'type'              => new sfWidgetFormInputText(),
      'filesize'          => new sfWidgetFormInputText(),
      'rank'              => new sfWidgetFormInputText(),
      'is_public'         => new sfWidgetFormInputCheckbox(),
      'created_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorPropelChoice(array('model' => 'sfAsset', 'column' => 'id', 'required' => false)),
      'folder_id'         => new sfValidatorPropelChoice(array('model' => 'sfAssetFolder', 'column' => 'id')),
      'filename'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description_antic' => new sfValidatorString(array('required' => false)),
      'author'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'copyright'         => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'type'              => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'filesize'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'rank'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'is_public'         => new sfValidatorBoolean(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'sfAsset', 'column' => array('folder_id', 'filename')))
    );

    $this->widgetSchema->setNameFormat('sf_asset[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfAsset';
  }

  public function getI18nModelName()
  {
    return 'SfAssetI18n';
  }

  public function getI18nFormClass()
  {
    return 'SfAssetI18nForm';
  }

}
