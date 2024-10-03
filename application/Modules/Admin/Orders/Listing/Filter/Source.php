<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\MarketplaceIntegration;

class Listing_Filter_Source extends DataListing_Filter
{
	public const KEY = 'source';
	
	protected string $source = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
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
			'shop' => 'e-shop'
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
			'import_source'   => $this->source!='shop'? $this->source : '',
		]);
	}
	
}