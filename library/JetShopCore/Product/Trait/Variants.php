<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Input;

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

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Synchronize categories'
	)]
	protected bool $variant_sync_categories = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Synchronize descriptions'
	)]
	protected bool $variant_sync_descriptions = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Synchronize stickers'
	)]
	protected bool $variant_sync_stickers = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Synchronize prices'
	)]
	protected bool $variant_sync_prices = true;

	protected ?Form $_variant_setup_form = null;

	protected ?Form $_variant_add_form = null;

	protected ?Form $_update_variants_form = null;


	public function getVariantMasterProductId() : int
	{
		return $this->variant_master_product_id;
	}

	public function setVariantMasterProductId( int $variant_master_product_id ) : void
	{
		$this->variant_master_product_id = $variant_master_product_id;
	}


	public function isVariantSyncCategories() : bool
	{
		return $this->variant_sync_categories;
	}

	public function setVariantSyncCategories( bool $variant_sync_categories ) : void
	{
		$this->variant_sync_categories = $variant_sync_categories;
	}

	public function isVariantSyncDescriptions() : bool
	{
		return $this->variant_sync_descriptions;
	}

	public function setVariantSyncDescriptions( bool $variant_sync_descriptions ) : void
	{
		$this->variant_sync_descriptions = $variant_sync_descriptions;
	}

	public function isVariantSyncStickers() : bool
	{
		return $this->variant_sync_stickers;
	}

	public function setVariantSyncStickers( bool $variant_sync_stickers ) : void
	{
		$this->variant_sync_stickers = $variant_sync_stickers;
	}

	public function isVariantSyncPrices() : bool
	{
		return $this->variant_sync_prices;
	}

	public function setVariantSyncPrices( bool $variant_sync_prices ) : void
	{
		$this->variant_sync_prices = $variant_sync_prices;
	}


	public function getVariantSetupForm() : Form
	{
		if(!$this->_variant_setup_form) {
			$this->_variant_setup_form = $this->createForm(form_name: 'variant_setup_form', only_fields: [
				'variant_sync_categories',
				'variant_sync_descriptions',
				'variant_sync_stickers',
				'variant_sync_prices',
			]);
		}

		return $this->_variant_setup_form;
	}

	public function catchVariantSetupForm() : bool
	{
		$edit_form = $this->getVariantSetupForm();
		if( $edit_form->catch() ) {
			foreach($this->getCategories() as $c) {
				Category::addSyncCategory( $c->getId() );
			}
			
			return true;
		}
		
		return false;
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

			foreach($this->getVariants() as $variant) {

				foreach(Shops::getList() as $shop) {
					$variant_name = new Form_Field_Input('/'.$variant->getId().'/'.$shop->getKey().'/variant_name', 'Variant name' );
					$variant_name->setDefaultValue( $variant->getShopData($shop)->getVariantName() );

					$variant_name->setFieldValueCatcher( function( $value ) use ($variant, $shop) {
						$variant->getShopData(  $shop )->setVariantName( $value );
					} );

					$fields[] = $variant_name;

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
				
				foreach($variant->getCategories() as $c) {
					Category::addSyncCategory( $c->getId() );
				}
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
				$p = Product::get($id);
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
		}


		foreach($this->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}
		foreach($variant->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}




		if($variant->variant_sync_categories || $variant->getIsNew()) {
			foreach($this->getCategories() as $c) {
				$variant->addCategory($c->getId());
			}

			foreach($variant->getCategories() as $c) {
				if(!$this->hasCategory($c->getId())) {
					$variant->removeCategory( $c->getId() );
				}
			}
		}



		if($this->variant_sync_descriptions || $variant->getIsNew() ) {
			foreach( Shops::getList() as $shop ) {
				$v_sd = $variant->getShopData( $shop );
				$sd = $this->getShopData();

				$v_sd->setName( $sd->getName() );
				$v_sd->setDescription( $sd->getDescription() );
				$v_sd->setShortDescription( $sd->getShortDescription() );
				$v_sd->setSeoTitle( $sd->getSeoTitle() );
				$v_sd->setSeoDescription( $sd->getSeoDescription() );
				$v_sd->setSeoKeywords( $sd->getSeoKeywords() );
				$v_sd->setSeoH1( $sd->getSeoH1() );
				$v_sd->setInternalFulltextKeywords( $sd->getInternalFulltextKeywords() );
			}
		}




		$skip_properties = $this->getVariantControlPropertyIds();

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



		if($this->variant_sync_stickers || $variant->getIsNew() ) {
			foreach($this->getStickers() as $c) {
				$variant->addSticker($c->getCode());
			}

			foreach($variant->getStickers() as $c) {
				if(!$this->hasSticker($c->getCode())) {
					$variant->removeSticker( $c->getCode() );
				}
			}
		}

		if($this->variant_sync_prices || $variant->getIsNew() ) {
			foreach( Shops::getList() as $shop ) {
				$v_sd = $variant->getShopData( $shop );
				$t_sd = $this->getShopData( $shop );

				$v_sd->setStandardPrice( $t_sd->getStandardPrice() );
				$v_sd->setActionPrice( $t_sd->getActionPrice() );
				$v_sd->setActionPriceValidFrom( $t_sd->getActionPriceValidFrom() );
				$v_sd->setActionPriceValidTill( $t_sd->getActionPriceValidTill() );

				$v_sd->actualizePrice();
			}
		}


		$variant->save();
	}

	protected function _setupForm_variants( Form $form ) : void
	{
		$form->removeField('variant_sync_categories');
		$form->removeField('variant_sync_descriptions');
		$form->removeField('variant_sync_stickers');
		$form->removeField('variant_sync_prices');


		if($this->type==Product::PRODUCT_TYPE_VARIANT) {
			$this->_edit_form->field('brand_id')->setIsReadonly(true);
			$this->_edit_form->field('supplier_id')->setIsReadonly(true);

			foreach(Shops::getList() as $shop) {
				$shop_key = $shop->getKey();

				$this->_edit_form->field('/shop_data/'.$shop_key.'/vat_rate')->setIsReadonly(true);
				$this->_edit_form->field('/shop_data/'.$shop_key.'/delivery_term_code')->setIsReadonly(true);
				$this->_edit_form->field('/shop_data/'.$shop_key.'/date_available')->setIsReadonly(true);

			}

			$master = Product::get($this->variant_master_product_id);

			if($master) {
				if($master->variant_sync_prices) {
					foreach(Shops::getList() as $shop) {
						$shop_key = $shop->getKey();

						$this->_edit_form->field('/shop_data/'.$shop_key.'/standard_price')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/action_price')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/action_price_valid_from')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/action_price_valid_till')->setIsReadonly(true);
					}
				}
				if($master->variant_sync_descriptions) {
					foreach(Shops::getList() as $shop) {
						$shop_key = $shop->getKey();

						$this->_edit_form->field('/shop_data/'.$shop_key.'/name')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/description')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/short_description')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_title')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_h1')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_description')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/seo_keywords')->setIsReadonly(true);
						$this->_edit_form->field('/shop_data/'.$shop_key.'/internal_fulltext_keywords')->setIsReadonly(true);
					}
				}

				if($master->variant_sync_stickers) {
					foreach(Sticker::getList() as $sticker) {
						$this->_edit_form->field('/sticker/'.$sticker->getCode())->setIsReadonly(true);
					}
				}

			}
		}

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