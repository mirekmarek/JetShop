<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI;
use Jet\UI_button;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Admin_Managers;

abstract class Core_Admin_Listing_Filter_Customer extends Admin_Listing_Filter
{
	protected string $label = 'Customer';
	
	protected Form_Field_Input $form_field;
	
	protected int $customer_id = 0;
	
	
	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	public function catchParams(): void
	{
		$this->customer_id = Http_Request::GET()->getInt( $this::getKey() );
		if($this->customer_id) {
			$this->listing->setParam($this::getKey() , $this->customer_id);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$this->form_field = new Form_Field_Input($this::getKey(), $this->label );
		$this->form_field->setDefaultValue( $this->customer_id?:'' );
		$form->addField($this->form_field);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->customer_id = (int)$this->form_field->getValue();
		if($this->customer_id) {
			$this->listing->setParam($this::getKey(), $this->customer_id);
		} else {
			$this->listing->unsetParam($this::getKey());
		}
	}
	
	public function isActive(): bool
	{
		return $this->customer_id > 0;
	}
	
	public function renderForm() : string
	{
		return Admin_Managers::EntityListing()->renderListingFilter(
			filter:      $this,
			title:       Tr::_($this->label),
			form_fields: [$this->form_field],
			is_active:   $this->isActive(),
			renderer:    function() {
				?>
				<?= $this->form_field->input() ?>
				<div><?=UI::button(' ')->setType(UI_button::TYPE_SUBMIT)->setIcon('search')?></div>
				<?php
			}
		);
		
	}
	
}