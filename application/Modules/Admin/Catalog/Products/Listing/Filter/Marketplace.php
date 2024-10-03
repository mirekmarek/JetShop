<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\MarketplaceIntegration;
use JetApplication\Shops;


class Listing_Filter_Marketplace extends DataListing_Filter
{
	public const KEY = 'marketplace';
	
	protected string $marketplace = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->marketplace = Http_Request::GET()->getString('marketplace', '', array_keys(MarketplaceIntegration::getScope()));

		if($this->marketplace) {
			$this->listing->setParam('marketplace', $this->marketplace);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + MarketplaceIntegration::getScope();
		
		$marketplace = new Form_Field_Select('marketplace', 'Supplier:' );
		$marketplace->setDefaultValue( $this->marketplace );
		$marketplace->setSelectOptions( $options );
		$marketplace->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($marketplace);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->marketplace = $form->field('marketplace')->getValue();
		if($this->marketplace) {
			$this->listing->setParam('marketplace', $this->marketplace);
		} else {
			$this->listing->unsetParam('marketplace');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->marketplace) {
			return;
		}
		
		[$mp_code, $shop_key] = explode(':', $this->marketplace);
		
		$mp = MarketplaceIntegration::getActiveModule( $mp_code );
		$shop = Shops::get( $shop_key );
		
		$ids = $mp->getSellingProductIds( $shop );
		if(!$ids) {
			$ids = [0];
		}
		
		$this->listing->addFilterWhere([
			'id'   => $ids,
		]);
	}
	
}