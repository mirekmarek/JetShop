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


class Listing_Filter_IsSale extends DataListing_Filter
{
	public const KEY = 'is_sale';
	
	protected string $is_sale = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->is_sale = Http_Request::GET()->getString('is_sale', '', ['', '1', '0']);
		if($this->is_sale) {
			$this->listing->setParam('is_sale', $this->is_sale);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [
			''  => Tr::_('- all -', dictionary: Tr::COMMON_DICTIONARY),
			'1' => Tr::_('yes', dictionary: Tr::COMMON_DICTIONARY),
			'0' => Tr::_('no', dictionary: Tr::COMMON_DICTIONARY),
		];
		
		$is_sale = new Form_Field_Select('is_sale', 'Is sale:' );
		$is_sale->setDefaultValue( $this->is_sale );
		$is_sale->setSelectOptions( $options );

		$form->addField($is_sale);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->is_sale = $form->field('is_sale')->getValue();
		if($this->is_sale) {
			$this->listing->setParam('is_sale', $this->is_sale);
		} else {
			$this->listing->unsetParam('is_sale');
		}
	}
	
	public function generateWhere(): void
	{
		if($this->is_sale=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'is_sale'   => (bool)$this->is_sale,
		]);
	}
	
}