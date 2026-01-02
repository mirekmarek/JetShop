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
use Jet\Form_Field_Select;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\KindOfProduct;
use JetApplication\MarketplaceIntegration_Entity_Interface;
use JetApplication\MarketplaceIntegration_Entity_Trait;
use JetApplication\MarketplaceIntegration_Join_KindOfProduct;
use JetApplication\MarketplaceIntegration_Marketplace;
use JetApplication\MarketplaceIntegration_MarketplaceCategory;


#[DataModel_Definition(
	name: 'marketplace_join_product',
	database_table_name: 'marketplace_join_product',
)]
abstract class Core_MarketplaceIntegration_Join_Product extends EShopEntity_WithEShopRelation implements
	MarketplaceIntegration_Entity_Interface, Form_Definition_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	use Form_Definition_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Alternative kind of product:',
		select_options_creator: [
			KindOfProduct::class,
			'getOptionsScope'
		],
	)]
	protected int $alternative_kind_of_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 150
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
			$this->edit_form = $this->createForm('mp_product_join_edit_form');
			
			$kinds_of_product = [
				0 => ''
			];
			
			foreach( MarketplaceIntegration_Join_KindOfProduct::getList() as $kinds_of_product_join ) {
				if(!isset(KindOfProduct::getScope()[$kinds_of_product_join->getKindOfProductId()])) {
					continue;
				}
				
				$mp_category = MarketplaceIntegration_MarketplaceCategory::get( $this->getMarketplace(), $kinds_of_product_join->getMarketplaceCategoryId() );
				if(!$mp_category) {
					continue;
				}
				
				$kinds_of_product[ $kinds_of_product_join->getKindOfProductId() ] =
					KindOfProduct::getScope()[$kinds_of_product_join->getKindOfProductId()]
					.' - '.$mp_category->getName().' ('. $mp_category->getCategoryId() .')';
			}
			
			/**
			 * @var Form_Field_Select $f
			 */
			$f = $this->edit_form->getField('alternative_kind_of_product_id');
			$f->setSelectOptions( $kinds_of_product );
		}
		
		return $this->edit_form;
	}
	
	
	public static function get( MarketplaceIntegration_Marketplace $marketplace, int $product_id  ) : static|null
	{
		return static::load( [
			$marketplace->getWhere(),
			'AND',
			'product_id' => $product_id
		] );

	}
	
	public static function getProductIds( MarketplaceIntegration_Marketplace $marketplace ) : array
	{
		return static::dataFetchCol(
			select: ['product_id'],
			where: $marketplace->getWhere(),
			raw_mode: true);
	}
	
	/**
	 * @param int $product_id
	 *
	 * @return static[]
	 */
	public static function getMarketplaces( int $product_id ) : array
	{
		return static::fetch( [''=>[
			'product_id' => $product_id
		]] );
		
	}

	
	public function setProductId( int $value ) : void
	{
		$this->product_id = $value;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function getAlternativeKindOfProductId(): int
	{
		return $this->alternative_kind_of_product_id;
	}
	
	public function setAlternativeKindOfProductId( int $alternative_kind_of_product_id ): void
	{
		$this->alternative_kind_of_product_id = $alternative_kind_of_product_id;
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
