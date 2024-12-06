<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use JetApplication\Pricelist;
use JetApplication\Pricelists;
use JetApplication\Product_Price;
use JetApplication\Product_PriceHistory;
use JetApplicationModule\Admin\Catalog\Products\Main;

trait Core_Product_Trait_Price
{
	use Core_Entity_HasPrice_Trait;
	
	protected ?Form $set_price_form = null;
	
	public function getPriceEntity( Pricelist $pricelist ) : Product_Price
	{
		return Product_Price::get( $pricelist, $this->getId() );
	}
	
	public function getPriceBeforeDiscount( Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getPriceBeforeDiscount();
	}
	
	public function getDiscountPercentage( Pricelist $pricelist ) : float
	{
		return $this->getPriceEntity( $pricelist )->getDiscountPercentage();
	}
	
	/**
	 * @return Product_PriceHistory[]
	 */
	public function getPriceHistory( Pricelist $pricelist ) : array
	{
		return Product_PriceHistory::get( $pricelist, $this->getId() );
	}
	
	public function getSetPriceForm() : ?Form
	{
		if(
			$this->isVariantMaster() ||
			$this->isSet() ||
			!Main::getCurrentUserCanSetPrice()
		) {
			return null;
		}
		
		if(!$this->set_price_form) {
			$this->set_price_form = new Form('set_price_form', []);
			
			
			foreach(Pricelists::getList() as $pl ) {
				
				$pp = Product_Price::get( $pl, $this->getId() );
				
				$field_name_prefix = '/'.$pl->getCode().'/';
				
				$vat_rate = new Form_Field_Select( $field_name_prefix.'vat_rate', 'VAT rate:' );
				$vat_rate->setDefaultValue( $pp->getVatRate() );
				$vat_rate->setErrorMessages([
					Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
				]);
				$vat_rate->setFieldValueCatcher(function( $value ) use ($pp) {
					$pp->setVatRate( $value );
					$pp->save();
				});
				$vat_rate->setSelectOptions( $pl->getVatRatesScope() );
				$this->set_price_form->addField( $vat_rate );
				
				
				$price = new Form_Field_Float($field_name_prefix.'price', 'Price:');
				$price->setDefaultValue( $pp->getPrice() );
				$price->setFieldValueCatcher(function( $value ) use ($pp) {
					$pp->setPrice( $value );
					$pp->save();
				});
				$this->set_price_form->addField( $price );
				
			}
			
		}
		
		return $this->set_price_form;
	}
	
}