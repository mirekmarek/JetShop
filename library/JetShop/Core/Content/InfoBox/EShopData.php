<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\Content_InfoBox_EShopData;
use JetApplication\EShop;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Content_InfoBox;
use JetApplication\EShops;


#[DataModel_Definition(
	name: 'content_info_box_eshop_data',
	database_table_name: 'content_info_box_eshop_data',
	parent_model_class: Content_InfoBox::class
)]
abstract class Core_Content_InfoBox_EShopData extends EShopEntity_WithEShopData_EShopData
{
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Text:'
	)]
	protected string $text = '';
	
	
	public function setText( string $value ) : void
	{
		$this->text = $value;
	}
	
	public function getText() : string
	{
		return $this->text;
	}
	
	protected static function getCacheFilePath( string $internal_code, EShop $eshop ) : string
	{
		$file_name = 'info_box_'.$eshop->getKey().'_'.$internal_code.'.html';
		$path = SysConf_Path::getCache().$file_name;
		
		return $path;
	}
	
	public static function show( string $internal_code, ?EShop $eshop = null ) : string
	{
		$eshop = $eshop??EShops::getCurrent();
		
		$tmp_path = static::getCacheFilePath( $internal_code, $eshop );
		if(IO_File::exists($tmp_path)) {
			return IO_File::read( $tmp_path );
		}
		
		$html =  Content_InfoBox_EShopData::getActiveByInternalCode($internal_code, $eshop)?->getText()??'';
		
		IO_File::write( $tmp_path, $html );
		
		return $html;
	}
	
	public function afterUpdate() : void
	{
		parent::afterUpdate();
		
		$tmp_path = static::getCacheFilePath( $this->internal_code, $this->getEshop() );
		if(IO_File::exists($tmp_path)) {
			IO_File::delete( $tmp_path );
		}
	}
	
	public function afterDelete() : void
	{
		parent::afterDelete();
		
		$tmp_path = static::getCacheFilePath( $this->internal_code, $this->getEshop() );
		if(IO_File::exists($tmp_path)) {
			IO_File::delete( $tmp_path );
		}
	}
	
}