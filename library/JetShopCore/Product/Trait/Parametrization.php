<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;

trait Core_Product_Trait_Parametrization
{
	/**
	 * @var Product_ParametrizationValue[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_ParametrizationValue::class,
		form_field_type: false
	)]
	protected array $parametrization_values = [];

	protected ?Form $_parametrization_edit_form = null;

	public function getParametrizationEditForm() : Form
	{
		if(!$this->_parametrization_edit_form) {

			$fields = [];

			$enable_only = null;
			if($this->type==Product::PRODUCT_TYPE_VARIANT) {
				$variant_master = Product::get($this->variant_master_product_id);

				$enable_only = $variant_master->getVariantControlPropertyIds();
			}

			foreach( $this->getCategories() as $category ) {
				foreach($category->getParametrizationProperties() as $property) {
					$property_id = $property->getId();

					if(!isset($this->parametrization_values[$property_id])) {
						$pv = new Product_ParametrizationValue();
						$this->parametrization_values[$property_id] = $pv;
					} else {
						$pv = $this->parametrization_values[$property_id];
					}

					$pv->setProperty( $property );

					$disabled = false;
					if(
						$enable_only!==null &&
						!in_array($property_id, $enable_only)
					) {
						$disabled = true;
					}

					foreach( $pv->getValueEditForm()->getFields() as $field ) {
						$field->setName('/'.$category->getId().'/'.$property->getGroupId().'/'.$property->getId().'/'.$field->getName());
						if($disabled) {
							$field->setIsReadonly(true);
						}
						$fields[] = $field;
					}
				}
			}

			$form = new Form('parametrization_edit_form', $fields);
			$form->setDoNotTranslateTexts(true);

			$this->_parametrization_edit_form = $form;
		}

		return $this->_parametrization_edit_form;
	}

	public function catchParametrizationEditForm() : bool
	{
		$edit_form = $this->getParametrizationEditForm();
		if(
			!$edit_form->catchInput() ||
			!$edit_form->validate()
		) {
			return false;
		}

		$edit_form->catchData();

		foreach($this->getCategories() as $c) {
			Category::addSyncCategory( $c->getId() );
		}


		return true;

	}

}