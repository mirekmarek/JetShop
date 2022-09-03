<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;

trait Core_Product_Trait_Parameters
{
	/**
	 * @var Product_Parameter[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_Parameter::class,
	)]
	protected array $parameters = [];

	protected ?Form $_parameters_edit_form = null;

	public function getParametersEditForm() : Form
	{
		if(!$this->_parameters_edit_form) {

			$fields = [];
			$enable_only = null;
			
			$properties = [];
			$kind = $this->getKind();
			if($kind) {
				$properties = $kind->getAllProperties();
				
				if($this->getType()==Product::PRODUCT_TYPE_VARIANT) {
					$enable_only = array_keys($kind->getVariantSelectorProperties());
				}
			}
			
			

			foreach($properties as $property) {
				$property_id = $property->getId();

				if(!isset( $this->parameters[$property_id])) {
					$pv = new Product_Parameter();
					$this->parameters[$property_id] = $pv;
				} else {
					$pv = $this->parameters[$property_id];
				}

				$pv->setProperty( $property );

				foreach( $pv->getValueEditForm()->getFields() as $field ) {
					$field->setName('/'.$property->getId().'/'.$field->getName());
					if(
						$enable_only!==null &&
						!in_array($property_id, $enable_only)
					) {
						$field->setIsReadonly(true);
					}
					$fields[] = $field;
				}
			}


			$form = new Form('parameters_edit_form', $fields);
			$form->setDoNotTranslateTexts(true);

			$this->_parameters_edit_form = $form;
		}

		return $this->_parameters_edit_form;
	}

	public function catchParametersEditForm() : bool
	{
		$edit_form = $this->getParametersEditForm();
		
		if($edit_form->catch()) {
			foreach($this->getCategories() as $c) {
				Category::addSyncCategory( $c->getId() );
			}

			return true;
		}
		
		return false;

	}

}