<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\KindOfProduct;
use JetApplication\KindOfProduct_Property;
use JetApplication\KindOfProduct_PropertyGroup;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\Product_VirtualProductHandler;
use JetApplication\Property_EShopData;
use JetApplication\PropertyGroup_EShopData;
use JetApplication\EShop;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product_eshop_data',
	database_table_name: 'kind_of_product_eshop_data',
	parent_model_class: KindOfProduct::class,
)]
abstract class Core_KindOfProduct_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 64
	)]
	protected string $measure_unit = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $is_virtual_product = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 125
	)]
	protected string $virtual_product_handler = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description:',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_main = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Variant select description:',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $variant_select_description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Similar product select description:',
	)]
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $similar_product_select_description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_pictogram = '';
	
	public static function getNameMap( EShop $eshop ) : array
	{
		$where = $eshop->getWhere();
		
		return static::dataFetchPairs(
			select: [
				'entity_id',
				'name'
			],
			where: $where,
			raw_mode: true
		);
	}
	
	public function getMeasureUnit() : ?MeasureUnit
	{
		return MeasureUnits::get( $this->measure_unit );
	}
	
	public function setMeasureUnit( string $measure_unit ): void
	{
		$this->measure_unit = $measure_unit;
	}
	
	public function getIsVirtualProduct(): bool
	{
		return $this->is_virtual_product;
	}
	
	public function setIsVirtualProduct( bool $is_virtual_product ): void
	{
		$this->is_virtual_product = $is_virtual_product;
	}
	
	public function getVirtualProductHandler(): null|Product_VirtualProductHandler|Application_Module
	{
		if(
			!$this->virtual_product_handler ||
			!Application_Modules::moduleIsActivated( $this->virtual_product_handler )
		) {
			return null;
		}
		
		return Application_Modules::moduleInstance( $this->virtual_product_handler );
	}
	
	public function setVirtualProductHandler( string $virtual_product_handler ): void
	{
		$this->virtual_product_handler = $virtual_product_handler;
	}
	
	
	
	
	public function setName( string $name ) : void
	{
		$this->name = $name;
	}
	
	public function getName() : string
	{
		return $this->name;
	}
	
	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	
	public function getImageEntity() : string
	{
		return 'property';
	}
	
	public function setImageMain( string $image_main ) : void
	{
		$this->image_main = $image_main;
	}
	
	public function getImageMain() : string
	{
		return $this->image_main;
	}
	
	
	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->image_pictogram = $image_pictogram;
	}
	
	public function getImagePictogram() : string
	{
		return $this->image_pictogram;
	}
	
	public function getVariantSelectDescription(): string
	{
		return $this->variant_select_description;
	}
	
	public function setVariantSelectDescription( string $variant_select_description ): void
	{
		$this->variant_select_description = $variant_select_description;
	}
	
	public function getSimilarProductSelectDescription(): string
	{
		return $this->similar_product_select_description;
	}
	
	public function setSimilarProductSelectDescription( string $similar_product_select_description ): void
	{
		$this->similar_product_select_description = $similar_product_select_description;
	}
	
	/**
	 * @param PropertyGroup_EShopData[] &$groups
	 * @param Property_EShopData[] &$properties
	 * @param EShop|null $eshop
	 * @return array
	 */
	public function getProductDetailLayout( array &$groups=[], array &$properties=[], ?EShop $eshop=null ) : array
	{
		
		
		/**
		 * @var KindOfProduct_Property[] $properties
		 */
		$__properties = KindOfProduct_Property::fetchInstances(where: [
			'kind_of_product_id' => $this->getId()
		]);
		$_properties = [];
		foreach($__properties as $p) {
			$_properties[$p->getPropertyId()] = $p;
		}
		
		$__property_groups = KindOfProduct_PropertyGroup::fetchInstances(where: [
			'kind_of_product_id' => $this->getId()
		]);
		$_property_groups = [];
		foreach($__property_groups as $g) {
			$_property_groups[$g->getGroupId()] = $g;
		}
		
		$group_ids = [];
		$property_ids = [];
		
		
		foreach($_properties as $property) {
			if(!$property->getShowOnProductDetail()) {
				continue;
			}
			
			$group_id = $property->getGroupId();
			$property_id = $property->getPropertyId();
			
			if(
				$group_id &&
				!isset($_property_groups[$group_id])
			) {
				$group_id = 0;
			}
			
			if(
				$group_id &&
				!in_array($group_id, $group_ids)
			) {
				$group_ids[] = $group_id;
			}
			$property_ids[] = $property_id;
		}
		
		if(!$property_ids) {
			return [];
		}
		
		
		$properties = Property_EShopData::getActiveList( $property_ids, $eshop );
		$groups = PropertyGroup_EShopData::getActiveList( $group_ids, $eshop );
		
		
		$layout = [];
		$current_group = 0;
		foreach($_properties as $property_id=>$property) {
			$property_id = $property->getPropertyId();
			if(!isset($properties[$property_id])) {
				continue;
			}
			
			
			$group_id = $property->getGroupId();
			
			if(
				$group_id &&
				!isset($groups[$group_id])
			) {
				$group_id = 0;
			}
			
			if(!$group_id) {
				$layout[] = $property_id;
				continue;
			}
			
			if(!isset($layout[$group_id])) {
				$layout[$group_id] = [];
			}
			
			if(!is_array($layout[$group_id])) {
				$layout[] = $property_id;
			} else {
				$layout[$group_id][] = $property_id;
			}
		}
		
		return $layout;
	}
	
	
}
