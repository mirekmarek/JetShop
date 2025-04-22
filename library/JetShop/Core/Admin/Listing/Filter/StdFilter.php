<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Admin_Managers;

abstract class Core_Admin_Listing_Filter_StdFilter extends Admin_Listing_Filter
{
	protected string $label;
	
	protected Form_Field_Select $form_field;
	protected string $value = '';
	
	abstract protected function getOptions() : array;
	
	abstract public function generateWhere(): void;
	
	
	protected function _getOptions() : array
	{
		return [''=>Tr::_(' - all -', dictionary: Tr::COMMON_DICTIONARY)] + $this->getOptions();
	}
	
	public function catchParams(): void
	{
		$key = $this::getKey();
		
		$this->value = Http_Request::GET()->getString($key, '', array_keys( $this->_getOptions() ));
		if($this->value) {
			$this->listing->setParam($key, $this->value);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$this->form_field = new Form_Field_Select( $this::getKey() , $this->label );
		$this->form_field->setDefaultValue( $this->value );
		$this->form_field->setSelectOptions( $this->_getOptions() );
		$form->addField( $this->form_field );
	}
	
	public function catchForm( Form $form ): void
	{
		$key = $this::getKey();
		
		$this->value = $form->field( $key )->getValue();
		if($this->value) {
			$this->listing->setParam( $key, $this->value);
		} else {
			$this->listing->unsetParam( $key );
		}
	}
	
	
	public function renderForm(): string
	{
		$this->form_field->input()->addJsAction( 'onchange', 'this.form.submit()' );
		
		return Admin_Managers::EntityListing()->renderListingFilter(
			filter:      $this,
			title:       $this->form_field->getLabel(),
			form_fields: [$this->form_field],
			is_active:   $this->value!='',
			renderer:    function() {
				echo $this->form_field->input();
			}
		);
		
	}
	
	public function isActive(): bool
	{
		return $this->value!=='';
	}
}