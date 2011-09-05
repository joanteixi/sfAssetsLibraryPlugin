<?php

/**
 * sfAsset filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 */
abstract class BasesfAssetFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'folder_id'         => new sfWidgetFormPropelChoice(array('model' => 'sfAssetFolder', 'add_empty' => true)),
      'filename'          => new sfWidgetFormFilterInput(),
      'description_antic' => new sfWidgetFormFilterInput(),
      'author'            => new sfWidgetFormFilterInput(),
      'copyright'         => new sfWidgetFormFilterInput(),
      'type'              => new sfWidgetFormFilterInput(),
      'filesize'          => new sfWidgetFormFilterInput(),
      'rank'              => new sfWidgetFormFilterInput(),
      'is_public'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'folder_id'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'sfAssetFolder', 'column' => 'id')),
      'filename'          => new sfValidatorPass(array('required' => false)),
      'description_antic' => new sfValidatorPass(array('required' => false)),
      'author'            => new sfValidatorPass(array('required' => false)),
      'copyright'         => new sfValidatorPass(array('required' => false)),
      'type'              => new sfValidatorPass(array('required' => false)),
      'filesize'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rank'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_public'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('sf_asset_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfAsset';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'folder_id'         => 'ForeignKey',
      'filename'          => 'Text',
      'description_antic' => 'Text',
      'author'            => 'Text',
      'copyright'         => 'Text',
      'type'              => 'Text',
      'filesize'          => 'Number',
      'rank'              => 'Number',
      'is_public'         => 'Boolean',
      'created_at'        => 'Date',
    );
  }
}
