<?php


/**
 * This class defines the structure of the 'sf_asset_i18n' table.
 *
 *
 * This class was autogenerated by Propel 1.4.0 on:
 *
 * Thu Oct 14 14:01:16 2010
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.sfAssetsLibraryPlugin.lib.model.map
 */
class SfAssetI18nTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.sfAssetsLibraryPlugin.lib.model.map.SfAssetI18nTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('sf_asset_i18n');
		$this->setPhpName('SfAssetI18n');
		$this->setClassname('SfAssetI18n');
		$this->setPackage('plugins.sfAssetsLibraryPlugin.lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addForeignPrimaryKey('ID', 'Id', 'INTEGER' , 'sf_asset', 'ID', true, null, null);
		$this->addPrimaryKey('CULTURE', 'Culture', 'VARCHAR', true, 7, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('sfAsset', 'sfAsset', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

	/**
	 * 
	 * Gets the list of behaviors registered for this table
	 * 
	 * @return array Associative array (name => parameters) of behaviors
	 */
	public function getBehaviors()
	{
		return array(
			'symfony' => array('form' => 'true', 'filter' => 'true', ),
			'symfony_behaviors' => array(),
			'symfony_i18n_translation' => array('culture_column' => 'culture', ),
		);
	} // getBehaviors()

} // SfAssetI18nTableMap
