<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\MarketplaceIntegration;

class Listing_Filter_Source extends Admin_Listing_Filter
{
	public const KEY = 'source';
	
	protected string $source = '';
	
	public function catchParams(): void
	{
		$this->source = Http_Request::GET()->getString('source', '', array_keys($this->getScope()));
		if($this->source) {
			$this->listing->setParam('source', $this->source);
		}
	}
	
	public function getScope() : array
	{
		return [
			'eshop' => 'e-shop'
		] + MarketplaceIntegration::getSourcesScope();
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + $this->getScope();
		
		$source = new Form_Field_Select('source', 'Payment method:' );
		$source->setDefaultValue( $this->source );
		$source->setSelectOptions( $options );
		$source->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($source);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->source = $form->field('source')->getValue();
		if($this->source) {
			$this->listing->setParam('source', $this->source);
		} else {
			$this->listing->unsetParam('source');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->source) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'import_source'   => $this->source!='eshop'? $this->source : '',
		]);
	}
	
}