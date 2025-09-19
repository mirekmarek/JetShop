<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_Date;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI;
use Jet\UI_button;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Application_Service_Admin;

abstract class Core_Admin_Listing_Filter_DateInterval extends Admin_Listing_Filter
{
	protected string $label;
	
	protected Form_Field_Date $form_field_from;
	protected Form_Field_Date $form_field_till;
	
	protected ?Data_DateTime $from = null;
	protected ?Data_DateTime $till = null;
	
	abstract public function generateWhere(): void;
	
	public function getFrom(): ?Data_DateTime
	{
		return $this->from;
	}
	
	
	public function getTill(): ?Data_DateTime
	{
		return $this->till;
	}
	
	
	public function catchParams(): void
	{
		$key = $this::getKey();
		
		$this->from = Data_DateTime::catchDate( Http_Request::GET()->getString($key.'_from') );
		if($this->from) {
			$this->from->setOnlyDate( false );
			$this->from->setTime( 0, 0, 0 );
			$this->listing->setParam($key.'_from', (string)$this->from);
		}
		
		$this->till = Data_DateTime::catchDate( Http_Request::GET()->getString($key.'_till') );
		if($this->till) {
			$this->till->setOnlyDate( false );
			$this->till->setTime( 23, 59, 59 );
			$this->listing->setParam($key.'_till', (string)$this->till);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$key = $this::getKey();
		
		$this->form_field_from = new Form_Field_Date($key.'_from', 'From:' );
		$this->form_field_from->setDefaultValue( $this->from );
		$form->addField($this->form_field_from);
		
		$this->form_field_till = new Form_Field_Date($key.'_till', 'Till:' );
		$this->form_field_till->setDefaultValue( $this->till );
		$form->addField($this->form_field_till);
	}
	
	public function catchForm( Form $form ): void
	{
		$key = $this::getKey();
		
		$this->from = Data_DateTime::catchDate( $this->form_field_from->getValue() );
		if($this->from) {
			$this->listing->setParam($key.'_from', (string)$this->from);
		} else {
			$this->listing->unsetParam($key.'_from');
		}
		
		$this->till = Data_DateTime::catchDate( $this->form_field_till->getValue() );
		if($this->till) {
			$this->listing->setParam($key.'_till', (string)$this->till);
		} else {
			$this->listing->unsetParam($key.'_till');
		}
		
	}
	
	
	public function renderForm(): string
	{
		return Application_Service_Admin::EntityListing()->renderListingFilter(
			filter:      $this,
			title:       Tr::_($this->label),
			form_fields: [$this->form_field_from, $this->form_field_till],
			is_active:   $this->isActive(),
			renderer:    function() {
				?>
				<div><?=Tr::_('From:', dictionary: Tr::COMMON_DICTIONARY)?></div>
				<div><?= $this->form_field_from->input() ?></div>
				<div><?=Tr::_('Till:', dictionary: Tr::COMMON_DICTIONARY)?></div>
				<div><?= $this->form_field_till->input() ?></div>
				
				<div><?=UI::button(Tr::_('Set', dictionary: Tr::COMMON_DICTIONARY))->setType(UI_button::TYPE_SUBMIT)?></div>
				<?php
			}
		);
		
	}
	
	public function isActive(): bool
	{
		return $this->from || $this->till;
	}
}