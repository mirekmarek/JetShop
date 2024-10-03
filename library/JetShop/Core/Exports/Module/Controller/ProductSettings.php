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
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_Module;
use JetApplication\Product;
use JetApplication\Shops_Shop;

/**
 *
 */
abstract class Core_Exports_Module_Controller_ProductSettings extends MVC_Controller_Default
{
	protected Product|Admin_Entity_WithShopData_Interface $product;
	
	protected Shops_Shop $shop;
	
	protected Exports_Module $export;
	
	protected ?Exports_ExportCategory $category;
	
	protected bool $editable;
	
	protected bool $selling;
	
	protected UI_tabs $tabs;
	
	protected ?Form $parameters_form;
	
	public function getProduct(): Admin_Entity_WithShopData_Interface|Product
	{
		return $this->product;
	}
	
	public function getShop(): Shops_Shop
	{
		return $this->shop;
	}
	
	public function getExport(): Exports_Module
	{
		return $this->export;
	}
	
	public function getEditable(): bool
	{
		return $this->editable;
	}
	
	public function getTabs(): UI_tabs
	{
		return $this->tabs;
	}
	
	public function getCategory(): ?Exports_ExportCategory
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
		Product|Admin_Entity_WithShopData_Interface $product,
		Shops_Shop                    $shop,
		Exports_Module $export
	): void
	{
		$this->product = $product;
		$this->shop = $shop;
		$this->export = $export;
		$this->editable = $product->isEditable();
		$this->category = $this->export->getCategory( $shop, $product );
		$this->selling = $this->export->getProductIsSelling( $this->shop, $this->product->getId() );
		
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
			'parameters' => Tr::_('Parameters')
		];
	}
	
	public function parameters_Action() : void
	{
		$this->parameters_form = $this->export->getParamsEditForm( $this->shop, $this->product );
		
		
		if($this->editable) {
			if( $this->parameters_form ) {
				if($this->export->catchParamsEditForm( $this->shop, $this->product )) {
					UI_messages::success(
						Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $this->product->getAdminTitle() ] )
					);
					
					Http_Headers::reload();
				}
			}
			
			if( $this->category ) {
				if(Http_Request::GET()->exists('actualize_list_of_parameters')) {
					$this->export->actualizeCategory( $this->shop, $this->category->getCategoryId() );
					Http_Headers::reload(unset_GET_params: ['actualize_list_of_parameters']);
				}
				
			}
			
		} else {
			$this->parameters_form->setIsReadonly();
		}
		
		/** @noinspection PhpParamsInspection */
		echo Admin_Managers::Product()->renderExportSettings_parameters( $this );
	}
}