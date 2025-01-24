<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Query;

use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Product;
use JetApplication\Availabilities;
use JetApplication\Category;
use JetApplication\Delivery_Class;
use JetApplication\Entity_HasPrice_Interface;
use JetApplication\Entity_WithEShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\JetShopEntity_Definition;
use JetApplication\KindOfProduct;

use JetApplication\Managers;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;
use JetApplication\Pricelists;
use JetApplication\Product;
use JetApplication\Product_Availability;
use JetApplication\Product_Price;
use JetApplication\Product_EShopData;
use JetApplication\Product_Trait_Accessories;
use JetApplication\Product_Trait_Availability;
use JetApplication\Product_Trait_Images;
use JetApplication\Product_Trait_Files;
use JetApplication\Product_Trait_Price;
use JetApplication\Product_Trait_Set;
use JetApplication\Product_Trait_Variants;
use JetApplication\Product_Trait_Categories;
use JetApplication\Product_Trait_Parameters;
use JetApplication\Product_Trait_Stickers;
use JetApplication\Product_Trait_Similar;
use JetApplication\Product_Trait_Boxes;
use JetApplication\Product_VirtualProductHandler;
use JetApplication\EShop_Managers;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Brand;
use JetApplication\Supplier;


#[DataModel_Definition(
	name: 'product',
	database_table_name: 'products',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	relation: [
		'related_to_class_name' => Product_Price::class,
		'join_by_properties' => [
			'id' => 'entity_id'
		],
		'join_type' => DataModel_Query::JOIN_TYPE_LEFT_JOIN
	]
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_Product::class
)]
abstract class Core_Product extends Entity_WithEShopData implements
	FulltextSearch_IndexDataProvider,
	Entity_HasPrice_Interface,
	Admin_Entity_WithEShopData_Interface
{
	
	use Product_Trait_Availability;
	use Product_Trait_Price;
	use Product_Trait_Images;
	use Product_Trait_Files;
	use Product_Trait_Set;
	use Product_Trait_Variants;
	use Product_Trait_Categories;
	use Product_Trait_Parameters;
	use Product_Trait_Stickers;
	use Product_Trait_Similar;
	use Product_Trait_Boxes;
	use Product_Trait_Accessories;
	
	use Admin_Entity_WithEShopData_Trait;
	

	public const PRODUCT_TYPE_REGULAR        = 'regular';
	public const PRODUCT_TYPE_VARIANT_MASTER = 'variant_master';
	public const PRODUCT_TYPE_VARIANT        = 'variant';
	public const PRODUCT_TYPE_SET            = 'set';
	
	public const SET_DISCOUNT_NONE    = '';
	public const SET_DISCOUNT_NOMINAL = 'nominal';
	public const SET_DISCOUNT_PERCENT = 'percent';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $type = Product::PRODUCT_TYPE_REGULAR;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $kind_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'EAN:'
	)]
	protected string $ean = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $erp_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Brand:',
		select_options_creator: [Brand::class,'getOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected int $brand_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Supplier:',
		select_options_creator: [Supplier::class,'getOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected int $supplier_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Supplier code:',
	)]
	protected string $supplier_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Delivery class:',
		select_options_creator: [Delivery_Class::class,'getOptionsScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	)]
	protected int $delivery_class_id = 0;
	
	
	/**
	 * @var Product_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_EShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $eshop_data = [];
	
	
	
	public static function getProductTypes() : array
	{
		return [
			static::PRODUCT_TYPE_REGULAR        => Tr::_('Regular'),
			static::PRODUCT_TYPE_VARIANT_MASTER => Tr::_('Variant master'),
			static::PRODUCT_TYPE_VARIANT        => Tr::_('Variant'),
			static::PRODUCT_TYPE_SET            => Tr::_('Set of products'),
		];
		
	}
	
	
	public function activate(): void
	{
		parent::activate();
		$id = $this->isVariant() ? $this->getVariantMasterProductId() : $this->getId();
		Product::actualizeReferences( product_id: $id );
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		$id = $this->isVariant() ? $this->getVariantMasterProductId() : $this->getId();
		Product::actualizeReferences( product_id: $id );
	}
	
	public function delete(): void
	{
		parent::delete();
		Category::productDeleted( $this->id );
	}
	
	public function getType() : string
	{
		return $this->type;
	}
	
	public function isVirtual() : bool
	{
		return (bool)$this->getKind()?->getIsVirtualProduct();
	}
	
	public function isSet() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_SET;
	}
	
	public function isVariant() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_VARIANT;
	}
	
	public function isVariantMaster() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_VARIANT_MASTER;
	}
	
	public function isRegular() : bool
	{
		return $this->type==Product::PRODUCT_TYPE_REGULAR;
	}
	
	public function isPhysicalProduct() : bool
	{
		if(
			$this->isVirtual() ||
			$this->isVariantMaster() ||
			$this->isSet()
		) {
			return false;
		}
		
		return true;
	}
	
	public static function getProductType( int $product_id ) : ?string
	{
		$product_type = static::dataFetchOne(select:['type'], where:['id'=>$product_id]);
		
		return $product_type?:null;
	}

	public function setType( string $type ) : void
	{
		$this->type = $type;
		
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setType( $this->type );
		}
	}
	
	public function getKindId(): int
	{
		return $this->kind_id;
	}
	
	public function getKind() : ?KindOfProduct
	{
		return $this->getKindOfProduct();
	}
	
	public function getKindOfProduct() : ?KindOfProduct
	{
		return KindOfProduct::load($this->getKindId());
	}
	
	
	public function setKindId( int $kind_id ): void
	{
		$this->kind_id = $kind_id;
		
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setKindId( $this->kind_id );
		}
		
	}
	
	public function getDeliveryClassId(): int
	{
		return $this->delivery_class_id;
	}
	
	public function setDeliveryClassId( int $delivery_class_id ): void
	{
		$this->delivery_class_id = $delivery_class_id;
		
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setDeliveryClassId( $this->delivery_class_id );
		}
	}
	
	

	public function getEan() : string
	{
		return $this->ean;
	}

	public function setEan( string $ean ) : void
	{
		$this->ean = $ean;
		
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setEan( $this->ean );
		}
		
	}

	public function getErpId() : string
	{
		return $this->erp_id;
	}

	public function setErpId( string $erp_id ) : void
	{
		$this->erp_id = $erp_id;
		
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setErpId( $this->erp_id );
		}
	}

	public function getBrandId() : int
	{
		return $this->brand_id;
	}

	public function setBrandId( int $brand_id ) : void
	{
		$this->brand_id = $brand_id;
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setBrandId( $this->brand_id );
		}
	}

	public function getSupplierId() : int
	{
		return $this->supplier_id;
	}

	public function setSupplierId( int $supplier_id ) : void
	{
		$this->supplier_id = $supplier_id;
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setSupplierId( $this->supplier_id );
		}
	}
	
	public function getSupplierCode(): string
	{
		return $this->supplier_code;
	}
	
	public function setSupplierCode( string $supplier_code ): void
	{
		$this->supplier_code = $supplier_code;
		foreach( EShops::getList() as $eshop) {
			$this->eshop_data[$eshop->getKey()]->setSupplierCode( $this->supplier_id );
		}
	}
	
	


	public function getInternalName() : string
	{
		return $this->internal_name;
	}

	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	
	public function getEshopData( ?EShop $eshop=null ) : Product_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	
	public static function getIdsByKind( KindOfProduct $kind ) : array
	{
		$_ids = static::dataFetchCol(['id'], ['kind_id'=>$kind->getId()]);
		$ids = [];
		
		foreach($_ids as $id) {
			$ids[] = (int)$id;
		}

		return $ids;
	}
	
	public function getAdminTitle() : string
	{
		$codes = [];
		if($this->getInternalCode()) {
			$codes[] = $this->getInternalCode();
		}
		if($this->getEan()) {
			$codes[] = $this->getEan();
		}
		
		if($codes) {
			$codes = ' ('.implode(', ', $codes).')';
		} else {
			$codes = '';
		}
		
		$internal_name = $this->internal_name;
		
		if($this->internal_name_of_variant) {
			$internal_name .= ' / '.$this->internal_name_of_variant;
		}
		
		return $internal_name.$codes;
	}
	
	public function afterAdd(): void
	{
		foreach( EShops::getList() as $eshop ) {
			$eshop_key = $eshop->getKey();
			
			$this->eshop_data[$eshop_key]->generateURLPathPart();
			$this->eshop_data[$eshop_key]->save();
		}
		
		parent::afterAdd();
	}
	
	public function afterUpdate(): void
	{
		switch($this->getType()) {
			case Product::PRODUCT_TYPE_REGULAR:         $this->actualizeSetItem(); break;
			case Product::PRODUCT_TYPE_VARIANT_MASTER:  $this->actualizeVariantMaster(); break;
			case Product::PRODUCT_TYPE_SET:             $this->actualizeSet(); break;
		}
		
		parent::afterUpdate();
	}
	
	
	public function getFulltextObjectType(): string
	{
		return $this->getType();
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->internal_name, $this->internal_code, $this->ean, $this->internal_name_of_variant];
	}
	
	public function getShopFulltextTexts( EShop $eshop ) : array
	{
		$eshop_data = $this->getEshopData( $eshop );
		if(
			!$eshop_data->isActiveForShop() ||
			$eshop_data->isVariant()
		) {
			return [];
		}
		
		$texts = [];
		$texts[] = $eshop_data->getName();
		$texts[] = $eshop_data->getInternalCode();
		$texts[] = $eshop_data->getEan();
		
		return $texts;
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
		EShop_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
		EShop_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	public static function updateReviews( int $product_id, int $count, int $rank ) : void
	{
		Product_EShopData::updateData(
			data: [
				'review_count' => $count,
				'review_rank' => $rank
			],
			where: [
				'entity_id' => $product_id
			]
		);
	}
	
	public static function updateQuestions( int $product_id, int $count ) : void
	{
		Product_EShopData::updateData(
			data: [
				'question_count' => $count,
			],
			where: [
				'entity_id' => $product_id
			]
		);
	}
	
	public static function actualizeReferences( int $product_id ) : void
	{
		foreach(Availabilities::getList() as $availability) {
			Product_Availability::get( $availability, $product_id )->actualizeReferences();
		}
		
		foreach(Pricelists::getList() as $pricelist) {
			Product_Price::get( $pricelist, $product_id )->actualizeReferences();
		}
		
		Category::actualizeProductAssoc( product_id: $product_id );
	}
	
	public static function getProductMeasureUnit( int $product_id ) : ?MeasureUnit
	{
		$kind_id = static::dataFetchOne(['kind_id'], where: ['id'=>$product_id]);
		if(!$kind_id) {
			return null;
		}
		
		$measure_unit = KindOfProduct::dataFetchOne( ['measure_unit'], where: ['id'=>$kind_id] );
		if(!$measure_unit) {
			return null;
		}
		
		return MeasureUnits::get( $measure_unit );
	}
	
	
	/**
	 * @return Product_VirtualProductHandler[]
	 */
	public static function getVirtualProductHandlers() : array
	{
		return Managers::findManagers(Product_VirtualProductHandler::class, 'VirtualProductHandler.');
	}
	
	public static function getVirtualProductHandlersScope() : array
	{
		$scope = [];
		
		foreach(static::getVirtualProductHandlers() as $module) {
			$manifest = $module->getModuleManifest();
			
			$scope[$manifest->getName()] = $manifest->getLabel().' ('.$manifest->getName().')';
		}
		
		return $scope;
	}
	
	public static function getVirtualProductHandlersOptionsScope() : array
	{
		return [''=>'']+static::getVirtualProductHandlersScope();
	}
	
	
	public function defineImages(): void
	{
	}
	
	
	protected function _setupForm( Form $form ) : void
	{
		
		$this->_setupForm_regular( $form );
		$this->_setupForm_set( $form );
		$this->_setupForm_variant( $form );
		$this->_setupForm_variantMaster( $form );
		
	}
	
	protected function _setupForm_regular( Form $form ) : void
	{
		if(!$this->isRegular()) {
			return;
		}
		$form->removeField('internal_name_of_variant');
		$form->addField( $this->generateKindOfProductField() );
		
		foreach( EShops::getList() as $eshop ) {
			$form->removeField( '/eshop_data/'.$eshop->getKey().'/variant_name' );
		}
	}
	
	
	protected function generateKindOfProductField() : Form_Field
	{
		$kind_id_field = new Form_Field_Input( 'kind_of_product_id', 'Kind of product:' );
		$kind_id_field->setDefaultValue( $this->kind_id );
		$kind_id_field->setErrorMessages([
			Form_Field::ERROR_CODE_EMPTY => 'Please select kind of product',
		]);
		$kind_id_field->setFieldValueCatcher( function( $value ) {
			$this->setKindId( $value );
		} );
		$kind_id_field->setValidator( function() use ($kind_id_field) {
			$kind_id = $kind_id_field->getValue();
			if(
				!$kind_id ||
				!KindOfProduct::exists( $kind_id )
			) {
				$kind_id_field->setError(Form_Field::ERROR_CODE_EMPTY);
				return false;
			}
			
			
			return true;
		} );
		
		return $kind_id_field;
	}
	
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			
			
			$this->_add_form->addField( $this->generateKindOfProductField() );
			
			$description_edit_form = $this->getDescriptionEditForm();
			foreach($description_edit_form->getFields() as $f) {
				$this->_add_form->addField( $f );
			}
			
			$this->_setupForm( $this->_add_form );
		}
		
		return $this->_add_form;
	}
	
	public function catchAddForm() : bool
	{
		$add_form = $this->getAddForm();
		if( !$add_form->catch() ) {
			return false;
		}
		
		return true;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			
			$this->_setupForm( $this->_edit_form );
		}
		
		return $this->_edit_form;
	}
	
	public function catchEditForm() : bool
	{
		$edit_form = $this->getEditForm();
		if( !$edit_form->catch() ) {
			return false;
		}
		
		return true;
	}
	
	protected ?Form $_description_edit_form = null;
	
	public function getProductDescriptionEditForm() : Form
	{
		if( !$this->_description_edit_form ) {
			if($this->getDescriptionMode()) {
				$this->_description_edit_form = $this->getDescriptionEditForm();
				
				if($this->isVariant()) {
					foreach( $this->_description_edit_form->getFields() as $f ) {
						if( !str_ends_with($f->getName(), '/variant_name') ) {
							$f->setIsReadonly( true );
						}
					}
				} else {
					foreach( $this->_description_edit_form->getFields() as $f ) {
						if( str_ends_with($f->getName(), '/variant_name') ) {
							$this->_description_edit_form->removeField( $f->getName() );
						}
					}
				}
				
			} else {
				$this->_description_edit_form = $this->getEditForm();
				
				foreach( $this->_description_edit_form->getFields() as $f ) {
					if( !str_starts_with($f->getName(), '/eshop_data/') ) {
						$this->_description_edit_form->removeField( $f->getName() );
					}
				}
			}
			
		}
		
		return $this->_description_edit_form;
	}
	
	public function catchDescriptionEditForm() : bool
	{
		if(!$this->getProductDescriptionEditForm()->catch()) {
			return false;
		}
		
		$this->save();
		return true;
	}
	
	public function getDescriptionMode() : bool
	{
		return true;
	}
	

}