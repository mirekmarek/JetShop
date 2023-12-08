<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field_Input;

use JetApplication\Product;
use JetApplication\Shops;
use JetApplication\Product_Parameter;


trait Core_Product_Trait_Variants
{
	/**
	 * @var Product[]
	 */
	protected array|null $_variants = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $variant_master_product_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
	)]
	protected string $variant_ids = '';


	protected ?Form $_variant_setup_form = null;

	protected ?Form $_variant_add_form = null;

	protected ?Form $_update_variants_form = null;


	public function getVariantMasterProductId() : int
	{
		return $this->variant_master_product_id;
	}
	
	public function getVariantMasterProduct() : ?Product
	{
		return static::get( $this->variant_master_product_id );
	}

	public function setVariantMasterProductId( int $variant_master_product_id ) : void
	{
		$this->variant_master_product_id = $variant_master_product_id;
	}
	

	public function getAddVariantForm() : Form
	{

		if(!$this->_variant_add_form) {
			$this->_variant_add_form = $this->createForm('variant_add_form');

			foreach($this->_variant_add_form->getFields() as $field) {
				$keep = false;

				if(
					$field->getName()=='ean' ||
					$field->getName()=='internal_code'
				) {
					$keep = true;
				}

				if(!$keep) {
					foreach( Shops::getList() as $shop ) {
						$shop_key = $shop->getKey();

						if( $field->getName()=='/shop_data/'.$shop_key.'/variant_name' ) {
							$keep = true;
							break;
						}
					}
				}

				if(!$keep) {
					$this->_variant_add_form->removeField($field->getName());
				}
			}
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
		
		return true;
	}

	public function getUpdateVariantsForm() : Form
	{
		if(!$this->_update_variants_form) {
			$fields = [];
			
			$variant_control_properties = $this->getKind()?->getVariantSelectorPropertyIds();

			foreach($this->getVariants() as $variant) {

				foreach(Shops::getList() as $shop) {
					$variant_name = new Form_Field_Input('/'.$variant->getId().'/'.$shop->getKey().'/variant_name', 'Variant name' );
					$variant_name->setDefaultValue( $variant->getShopData($shop)->getVariantName() );

					$variant_name->setFieldValueCatcher( function( $value ) use ($variant, $shop) {
						$variant->getShopData(  $shop )->setVariantName( $value );
					} );

					$fields[] = $variant_name;

				}
				
				foreach($variant_control_properties as $property) {
					$property_id = $property->getId();
					
					
					if(!isset($variant->parameters[$property_id])) {
						$param = new Product_Parameter();
						$variant->parameters[$property_id] = $param;
					} else {
						$param = $variant->parameters[$property_id];
					}
					
					$param->setProperty( $property );
					
					foreach( $param->getValueEditForm()->getFields() as $field ) {
						$field->setName('/'.$variant->getId().'/'.$property->getId().'/'.$field->getName());
						$fields[] = $field;
					}
				}
				
			}

			$this->_update_variants_form = new Form('update_variants_form', $fields);
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
			
			return true;
		}

		return false;
	}


	public function getVariantIds() : array
	{
		if(!$this->variant_ids) {
			return [];
		} else {
			return explode(',', $this->variant_ids);
		}
	}

	public function setVariantIds( array $ids ) : void
	{
		$this->variant_ids = implode(',', $ids);
		$this->_variants = null;
	}

	/**
	 * @return Product[]
	 */
	public function getVariants() : array
	{
		if($this->_variants===null) {
			$this->_variants=[];
			foreach($this->getVariantIds() as $id) {
				$p = static::get($id);
				if($p) {
					$this->_variants[$p->getId()] = $p;
				}
			}
		}

		return $this->_variants;
	}

	public function addVariant( Product $variant ) : void
	{

		$variant_ids = $this->getVariantIds();
		if(
			$variant->getId()>0 &&
			in_array($variant->getId(), $variant_ids)
		) {
			return;
		}

		$this->type = Product::PRODUCT_TYPE_VARIANT_MASTER;

		$this->syncVariant( $variant );

		$variant_ids[] = $variant->getId();


		$this->setVariantIds($variant_ids);
	}

	public function syncVariants() : void
	{
		if($this->type==Product::PRODUCT_TYPE_VARIANT_MASTER) {
			foreach($this->getVariants() as $v) {
				$this->syncVariant( $v );
			}
		}
	}

	public function syncVariant( Product $variant ) : void
	{
		$variant->variant_master_product_id = $this->getId();

		$variant->type = Product::PRODUCT_TYPE_VARIANT;

		$variant->brand_id = $this->brand_id;
		$variant->supplier_id = $this->supplier_id;

		foreach( Shops::getList() as $shop ) {
			$v_sd = $variant->getShopData( $shop );
			$sd = $this->getShopData();

			$v_sd->setDateAvailable( $sd->getDateAvailable() );
			$v_sd->setDeliveryTermCode( $sd->getDeliveryTermCode() );
			$v_sd->setDeliveryClassCode( $sd->getDeliveryClassCode() );
			
			$v_sd->setName( $sd->getName() );
			$v_sd->setDescription( $sd->getDescription() );
			$v_sd->setShortDescription( $sd->getShortDescription() );
			$v_sd->setSeoTitle( $sd->getSeoTitle() );
			$v_sd->setSeoDescription( $sd->getSeoDescription() );
			$v_sd->setSeoKeywords( $sd->getSeoKeywords() );
			$v_sd->setSeoH1( $sd->getSeoH1() );
			$v_sd->setInternalFulltextKeywords( $sd->getInternalFulltextKeywords() );
		}
		
		
		
		$skip_properties = $this->getKind()?->getVariantSelectorPropertyIds();
		if(!$skip_properties) {
			$skip_properties = [];
		}

		foreach( $this->parameters as $pv) {
			$p_id = $pv->getPropertyId();
			if(in_array($p_id, $skip_properties)) {
				continue;
			}

			if(!isset( $variant->parameters[$p_id])) {
				$variant->parameters[$p_id] = new Product_Parameter();
				$variant->parameters[$p_id]->setPropertyId( $pv->getPropertyId() );
			}
			
			$variant->parameters[$p_id]->setRawValue( $pv->getRawValue() );
			$variant->parameters[$p_id]->setInformationIsNotAvailable( $pv->isInformationIsNotAvailable() );
		}

		
		//TODO: parametry z variant nastavit hlavnimu produktu
		//TODO: najit nejlevnejsi (vazane na lokalizaci)
		//TODO: najit nejdostupnejsi (vazane na lokalizaci)


		$variant->save();
	}


	public function actualizeVariant() : void
	{
		/** @noinspection PhpStatementHasEmptyBodyInspection */
		if($this->getType()!=Product::PRODUCT_TYPE_VARIANT) {
			//return;
		}

		//TODO:
	}
}