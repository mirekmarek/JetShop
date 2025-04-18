<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;


class Listing_Filter_Archived extends DataListing_Filter
{
	public const KEY = 'archived';
	
	protected string $archived = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->archived = Http_Request::GET()->getString('archived', '', ['', '1', '0']);
		if($this->archived) {
			$this->listing->setParam('archived', $this->archived);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [
			''  => Tr::_('- all -', dictionary: Tr::COMMON_DICTIONARY),
			'1' => Tr::_('yes', dictionary: Tr::COMMON_DICTIONARY),
			'0' => Tr::_('no', dictionary: Tr::COMMON_DICTIONARY),
		];
		
		$archived = new Form_Field_Select('archived', 'Archived' );
		$archived->setDefaultValue( $this->archived );
		$archived->setSelectOptions( $options );
		
		$form->addField($archived);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->archived = $form->field('archived')->getValue();
		if($this->archived) {
			$this->listing->setParam('archived', $this->archived);
		} else {
			$this->listing->unsetParam('archived');
		}
	}
	
	public function generateWhere(): void
	{
		if($this->archived=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'archived'   => (bool)$this->archived,
		]);
	}
	
}