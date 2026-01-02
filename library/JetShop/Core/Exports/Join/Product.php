<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'exports_join_product',
	database_table_name: 'exports_join_product',
)]
abstract class Core_Exports_Join_Product extends EShopEntity_WithEShopRelation implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $export_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Alternative name:',
	)]
	protected string $aletrnative_name = '';
	
	protected ?Form $edit_form = null;
	
	public function getEditForm(): Form
	{
		if( !$this->edit_form ) {
			$this->edit_form = $this->createForm('export_product_join_edit_form');
		}
		
		return $this->edit_form;
	}
	
	
	public static function get( string $export_code, EShop $eshop, int $product_id  ) : static|null
	{
		$join = static::load( [
			'export_code' => $export_code,
			'AND',
			$eshop->getWhere(),
			'AND',
			'product_id' => $product_id
		] );
		
		if(!$join) {
			$join = new static();
			$join->setExportCode( $export_code );
			$join->setEshop( $eshop );
			$join->setProductId( $product_id );
			$join->save();
		}
		
		return $join;
		
	}
	
	public static function getProductIds( string $export_code, EShop $eshop) : array
	{
		return static::dataFetchCol(
			select: ['product_id'],
			where: [
				$eshop->getWhere(),
				'AND',
				'export_code' => $export_code,
			],
			raw_mode: true);
	}
	
	/**
	 * @param int $product_id
	 *
	 * @return static[]
	 */
	public static function getExports( int $product_id ) : array
	{
		return static::fetch( [''=>[
			'product_id' => $product_id
		]] );
		
	}
	
	public function setExportCode( string $value ) : void
	{
		$this->export_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}
	
	public function getExportCode() : string
	{
		return $this->export_code;
	}
	
	public function setProductId( int $value ) : void
	{
		$this->product_id = $value;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function getAletrnativeName(): string
	{
		return $this->aletrnative_name;
	}
	
	public function setAletrnativeName( string $aletrnative_name ): void
	{
		$this->aletrnative_name = $aletrnative_name;
	}
	
	
}
