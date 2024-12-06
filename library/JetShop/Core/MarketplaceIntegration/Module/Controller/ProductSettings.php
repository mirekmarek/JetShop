<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Form;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI;
use Jet\UI_messages;
use Jet\UI_tabs;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\MarketplaceIntegration_MarketplaceCategory;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;
use JetApplication\Product;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\EShop;

/**
 *
 */
abstract class Core_MarketplaceIntegration_Module_Controller_ProductSettings extends MVC_Controller_Default
{
	protected Product|Admin_Entity_WithEShopData_Interface $product;
	
	protected EShop $eshop;
	
	protected MarketplaceIntegration_Module $marketplace;
	
	protected ?MarketplaceIntegration_MarketplaceCategory $category;
	
	protected bool $editable;
	
	protected bool $selling;
	
	protected UI_tabs $tabs;
	
	protected ?Form $parameters_form;
	
	public function getProduct(): Admin_Entity_WithEShopData_Interface|Product
	{
		return $this->product;
	}
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}

	public function getMarketplace(): MarketplaceIntegration_Module
	{
		return $this->marketplace;
	}
	
	public function getEditable(): bool
	{
		return $this->editable;
	}
	
	public function getTabs(): UI_tabs
	{
		return $this->tabs;
	}
	
	public function getCategory(): ?MarketplaceIntegration_MarketplaceCategory
	{
		return $this->category;
	}
	
	public function getSelling(): bool
	{
		return $this->selling;
	}
	
	public function getParametersForm(): ?Form
	{
		return $this->parameters_form;
	}
	
	
	public function resolve(): bool|string
	{
		return $this->tabs->getSelectedTabId();
	}
	
	
	public function init(
		Product|Admin_Entity_WithEShopData_Interface $product,
		EShop                                        $eshop,
		MarketplaceIntegration_Module                $marketplace
	): void
	{
		$this->product = $product;
		$this->eshop = $eshop;
		$this->marketplace = $marketplace;
		$this->editable = $product->isEditable();
		$this->category = $this->marketplace->getCategory( $eshop, $product );
		$this->selling = $this->marketplace->getProductIsSelling( $this->eshop, $this->product->getId() );
		
		$tabs = $this->initTabs();
		
		$this->tabs = UI::tabs(
			tabs: $tabs,
			tab_url_creator: function( string $tab ) : string {
				return Http_Request::currentURI(set_GET_params: ['mp_tab'=>$tab]);
			},
			selected_tab_id: Http_Request::GET()->getString('mp_tab', valid_values: array_keys($tabs))
		);
		
		
	}
	
	protected function initTabs() : array
	{
		return [
			'main' => Tr::_('Main'),
			'parameters' => Tr::_('Parameters')
		];
	}
	
	public function main_Action() : void
	{
		
		if(
			$this->editable &&
			$this->category
		) {

			if(Http_Request::GET()->exists('stop_selling')) {
				$this->marketplace->stopSelling( $this->eshop, $this->product->getId() );
				Http_Headers::reload(unset_GET_params: ['stop_selling']);
			}
			
			if(Http_Request::GET()->exists('start_selling')) {
				$this->marketplace->startSelling( $this->eshop, $this->product->getId() );
				Http_Headers::reload(unset_GET_params: ['start_selling']);
			}
		}
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_ProductSettings $this
		 */
		echo Admin_Managers::Product()->renderMarketPlaceSettings_main( $this );
	}
	
	
	public function parameters_Action() : void
	{
		$this->parameters_form = $this->marketplace->getParamsEditForm( $this->eshop, $this->product );
		
		
		if($this->editable) {
			if( $this->parameters_form ) {
				if($this->marketplace->catchParamsEditForm( $this->eshop, $this->product )) {
					UI_messages::success(
						Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $this->product->getAdminTitle() ] )
					);
					
					Http_Headers::reload();
				}
			}
			
			if( $this->category ) {
				if(Http_Request::GET()->exists('actualize_list_of_parameters')) {
					$this->marketplace->actualizeCategory( $this->eshop, $this->category->getCategoryId() );
					Http_Headers::reload(unset_GET_params: ['actualize_list_of_parameters']);
				}
				
			}
			
		} else {
			$this->parameters_form->setIsReadonly();
		}
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_ProductSettings $this
		 */
		echo Admin_Managers::Product()->renderMarketPlaceSettings_parameters( $this );
	}
	
}