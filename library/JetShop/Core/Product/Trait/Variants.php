<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use JetApplication\Product;
use JetApplication\Product_Parameter_Value;
use JetApplication\EShops;


trait Core_Product_Trait_Variants
{
	/**
	 * @var Product[]
	 */
	protected array|null $_variants = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name of variant:'
	)]
	protected string $internal_name_of_variant = '';
	

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $variant_master_product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $variant_priority = 0;
	
	protected ?Form $_variant_setup_form = null;
	
	protected ?Form $_variant_add_form = null;
	
	protected ?Form $_update_variants_form = null;
	
	
	public static function getProductActiveVariantIds( int $product_id ) : array
	{
		$where = [];
		$where['variant_master_product_id'] = $product_id;
		$where[] = 'AND';
		$where['is_active'] = true;
		
		
		return static::dataFetchCol(['id'], $where);
	}
	
	public static function getProductVariantMasterProductId( int $product_id ) : int
	{
		return (int)static::dataFetchOne(['variant_master_product_id'], ['id'=>$product_id]);
	}
	

	public function getInternalNameOfVariant(): string
	{
		return $this->internal_name_of_variant;
	}
	
	public function setInternalNameOfVariant( string $internal_name_of_variant ): void
	{
		$this->internal_name_of_variant = $internal_name_of_variant;
	}
	
	public function getVariantMasterProduct() : ?static
	{
		return static::load( $this->variant_master_product_id );
	}
	
	
	
	public function getVariantMasterProductId() : int
	{
		return $this->variant_master_product_id;
	}
	
	public function setVariantMasterProductId( int $variant_master_product_id ) : void
	{
		if($this->isVariant()) {
			$this->variant_master_product_id = $variant_master_product_id;
			
			foreach( EShops::getList() as $eshop) {
				$this->getEshopData( $eshop )->setVariantMasterProductId( $variant_master_product_id );
			}
		}
	}
	
	public function getVariantPriority(): int
	{
		return $this->variant_priority;
	}
	
	public function setVariantPriority( int $variant_priority ): void
	{
		if($this->isVariant()) {
			$this->variant_priority = $variant_priority;
			foreach( EShops::getList() as $eshop) {
				$this->getEshopData( $eshop )->setVariantPriority( $variant_priority );
			}
		}
	}
	
	
	
	
	/**
	 * @return static[]
	 */
	public function getVariants() : array
	{
		if($this->_variants===null) {
			/**
			 * @var DataModel_Fetch_Instances $_variants
			 */
			$_variants = static::fetchInstances(
				['variant_master_product_id'=>$this->id]
			);
			$_variants->getQuery()->setOrderBy(['variant_priority']);
			
			$this->_variants=[];
			foreach($_variants as $variant) {
				$this->_variants[$variant->getId()] = $variant;
			}
		}

		return $this->_variants;
	}

	public function addVariant( Product $variant ) : void
	{
		if(
			!$this->isVariantMaster() ||
			!$variant->isVariant()
		) {
			return;
		}
		
		$variants = $this->getVariants();
		
		$variant->setVariantPriority( count($variants)+1 );
		
		
		$this->syncVariant( $variant );
		$this->_variants[$variant->getId()] = $variant;
	}

	public function actualizeVariantMaster() : void
	{
		if($this->type!=static::PRODUCT_TYPE_VARIANT_MASTER) {
			return;
		}

		foreach($this->getVariants() as $v) {
			$this->syncVariant( $v );
		}
		
		/** @noinspection PhpParamsInspection */
		Product_Parameter_Value::syncVariants(
			$this
		);
	}

	public function syncVariant( Product $variant ) : void
	{
		$variant->variant_master_product_id = $this->getId();
		$variant->type = static::PRODUCT_TYPE_VARIANT;
		
		$variant->kind_id = $this->kind_id;
		$variant->brand_id = $this->brand_id;
		$variant->supplier_id = $this->supplier_id;
		$variant->internal_name = $this->internal_name;
		$variant->delivery_class_id = $this->delivery_class_id;
		
		foreach( EShops::getList() as $eshop ) {
			$variant->getEshopData( $eshop )->setVariantMasterProductId( $this->id );
			
			$this->getEshopData( $eshop )->syncVariant(
				$variant->getEshopData( $eshop )
			);
		}

		$variant->save();
	}
	
	
	public function createNewVariantInstance() : static
	{
		$variant = new static();
		
		$variant->setType( Product::PRODUCT_TYPE_VARIANT );
		$variant->is_active = true;
		$variant->setKindId( $this->getKindId() );
		$variant->setVariantMasterProductId( $this->getId() );
		$variant->setInternalName( $this->getInternalName() );
		$variant->setInternalCode( $this->getInternalCode() );
		
		return $variant;
	}
	
	protected function _setupForm_variantMaster( Form $form ) : void
	{
		if(!$this->isVariantMaster()) {
			return;
		}
		$form->removeField('internal_name_of_variant');
		$form->addField( $this->generateKindOfProductField() );
		
		foreach( EShops::getList() as $eshop ) {
			$form->removeField('/eshop_data/'.$eshop->getKey().'/variant_name');
		}
	}
	
	
	protected function _setupForm_variant( Form $form ) : void
	{
		if($this->type!=static::PRODUCT_TYPE_VARIANT) {
			return;
		}
		
		$form->setIsReadonly();
	}
	
	
	public function getAddVariantForm() : Form
	{
		
		if(!$this->_variant_add_form) {
			$this->_variant_add_form = $this->createForm('variant_add_form', [
				'ean',
				'internal_code',
				'internal_name_of_variant',
				'/eshop_data/*/variant_name',
			]);
			
			
		}
		
		return $this->_variant_add_form;
	}
	
	public function catchAddVariantForm( Product $new_variant ) : bool
	{
		$edit_form = $new_variant->getAddVariantForm();
		if( $edit_form->catch() ) {
			$this->addVariant( $new_variant );
			
			return true;
		}
		
		return false;
	}
	
	public function getUpdateVariantsForm() : Form
	{
		if(!$this->_update_variants_form) {
			$fields = [];
			
			foreach($this->getVariants() as $variant) {
				/**
				 * @var Product $variant
				 */
				$_form = $variant->createForm('', [
					'ean',
					'internal_code',
					'internal_name_of_variant',
					'supplier_code',
					'/eshop_data/*/variant_name',
				]);
				
				
				
				foreach($_form->getFields() as $field) {
					$field_name = $field->getName();
					if($field_name[0]=='/') {
						$field_name = substr($field_name, 1);
					}
					
					$field_name = '/'.$variant->getId().'/'.$field_name;
					
					$field->setName( $field_name );
					
					$fields[] = $field;
				}
				
				$priority_field = new Form_Field_Int('/'.$variant->getId().'/variant_priority', 'Priority:');
				$priority_field->setDefaultValue( $variant->getVariantPriority() );
				$priority_field->setFieldValueCatcher( function( $value ) use ($variant) {
					$variant->setVariantPriority( (int)$value );
				} );
				$fields[] = $priority_field;
				
				
				$variant_control_properties = $this->getKindOfProduct()?->getVariantSelectorProperties() ? : [];
				
				foreach($variant_control_properties as $property) {
					
					$property->assocToProduct( $variant->getId() );
					
					foreach( $property->getValueEditForm()->getFields() as $field ) {
						$field->setName('/'.$variant->getId().'/'.$property->getId().'/'.$field->getName());
						$fields[] = $field;
					}
				}
				
				foreach( EShops::getListSorted() as $eshop ) {
					
					$name_field = new Form_Field_Input('/'.$variant->getId().'/'.$eshop->getKey().'/variant_name', 'Name:');
					$name_field->setDefaultValue( $variant->getEshopData( $eshop )->getVariantName() );
					$name_field->setFieldValueCatcher( function( $value ) use ($variant, $eshop) {
						$variant->getEshopData( $eshop )->setVariantName( $value );
					} );
					
					$fields[] = $name_field;
					
				}
				
			}
			
			$this->_update_variants_form = new Form('update_variants_form', $fields);
			if(!$this->isEditable()) {
				$this->_update_variants_form->setIsReadonly();
			}
		}
		
		return $this->_update_variants_form;
	}
	
	
	public function catchUpdateVariantsForm(): bool
	{
		$edit_form = $this->getUpdateVariantsForm();
		if( $edit_form->catch() ) {
			foreach($this->getVariants() as $variant) {
				$variant->save();
			}
			
			$this->actualizeVariantMaster();
			
			return true;
		}
		
		return false;
	}
	
}