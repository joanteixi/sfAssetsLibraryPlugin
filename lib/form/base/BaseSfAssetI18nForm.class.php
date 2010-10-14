<?php

/**
 * SfAssetI18n form base class.
 *
 * @method SfAssetI18n getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 24051 2009-11-16 21:08:08Z Kris.Wallsmith $
 */
abstract class BaseSfAssetI18nForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'description' => new sfWidgetFormTextarea(),
      'id'          => new sfWidgetFormInputHidden(),
      'culture'     => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'description' => new sfValidatorString(array('required' => false)),
      'id'          => new sfValidatorPropelChoice(array('model' => 'sfAsset', 'column' => 'id', 'required' => false)),
      'culture'     => new sfValidatorPropelChoice(array('model' => 'SfAssetI18n', 'column' => 'culture', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_asset_i18n[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SfAssetI18n';
  }


}
