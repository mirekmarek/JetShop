<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_Managers;
use JetApplication\Brand;
use JetApplication\MarketplaceIntegration_Join_Brand;
use JetApplication\MarketplaceIntegration_MarketplaceBrand;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;
use JetApplication\Shops_Shop;

/**
 *
 */
abstract class Core_MarketplaceIntegration_Module_Controller_BrandSettings extends MVC_Controller_Default
{
	/**
	 * @var MarketplaceIntegration_MarketplaceBrand[]
	 */
	protected array $marketplace_brands;
	
	protected Brand $brand;
	
	protected Shops_Shop $shop;
	
	protected MarketplaceIntegration_Module $marketplace;
	
	protected MarketplaceIntegration_Join_Brand $brand_id_join;
	
	protected ?MarketplaceIntegration_MarketplaceBrand $selected_marketplace_brand;
	
	protected Form $brand_form;
	
	public function init(
		Brand $brand,
		Shops_Shop $shop,
		MarketplaceIntegration_Module $marketplace
	): void
	{
		$this->brand = $brand;
		$this->shop = $shop;
		$this->marketplace = $marketplace;
		$this->marketplace_brands = $this->marketplace->getBrands( $this->shop );
		$this->selected_marketplace_brand = null;
		
		$this->brand_id_join = MarketplaceIntegration_Join_Brand::get(
			$this->marketplace->getCode(),
			$this->shop,
			$this->brand->getId()
		);
		
		$this->selected_marketplace_brand = $this->marketplace_brands[$this->brand_id_join->toString()] ?? null;
	}
	
	/**
	 * @return MarketplaceIntegration_MarketplaceBrand[]
	 */
	public function getMarketplaceBrands(): array
	{
		return $this->marketplace_brands;
	}
	
	public function getBrand(): Brand
	{
		return $this->brand;
	}
	
	public function getShop(): Shops_Shop
	{
		return $this->shop;
	}
	
	public function getMarketplace(): MarketplaceIntegration_Module
	{
		return $this->marketplace;
	}
	
	public function getBrandIdJoin(): MarketplaceIntegration_Join_Brand
	{
		return $this->brand_id_join;
	}
	
	public function getSelectedMarketplaceBrand(): ?MarketplaceIntegration_MarketplaceBrand
	{
		return $this->selected_marketplace_brand;
	}
	
	public function getBrandForm(): Form
	{
		return $this->brand_form;
	}
	
	
	
	
	public function resolve(): bool|string
	{
		$GET = Http_Request::GET();
		
		if($GET->exists('brand')) {
			return 'dialog_shop_brand';
		}
		
		
		return 'default';
	}
	
	public function dialog_shop_brand_Action() : void
	{
		/**
		 * @var MarketplaceIntegration_Module_Controller_BrandSettings $this
		 */
		AJAX::snippetResponse(
			Admin_Managers::Brand()->renderMarketPlaceIntegrationBrands(
				$this,
				Http_Request::GET()->getString('brand')
			)
		);
		
	}
	
	public function default_Action() : void
	{
		$brand_field = new Form_Field_Input('brand', 'Brand ID:');
		$brand_field->setDefaultValue( $this->brand_id_join );
		$brand_field->setErrorMessages([
			'unknown_brand' => 'Unknown brand'
		]);
		$brand_field->setValidator(function( Form_Field_Input $field ) {
			$value = $field->getValue();
			
			if(!$value) {
				return true;
			}
			
			if(!isset( $this->marketplace_brands[$value])) {
				$field->setError('unknown_brand');
				return false;
			}
			
			return true;
		});
		$brand_field->setFieldValueCatcher(function($value) {
			$this->brand_id_join->setMarketplaceBrandId($value);
		});
		
		$this->brand_form = new Form('brand_settings', [$brand_field]);
		
		if($this->brand_form->catch()) {
			$this->brand_id_join->save();
			UI_messages::success(Tr::_('Saved ...'));
		}
		
		
		$this->view->setVar('brand_form', $this->brand_form);
		
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_BrandSettings $this
		 */
		echo Admin_Managers::Brand()->renderMarketPlaceIntegrationForm( $this );
	}
	
	
}