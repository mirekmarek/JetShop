<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
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
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\Application_Service_Admin;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_Join_Product;
use JetApplication\Exports_Module;
use JetApplication\Product;
use JetApplication\EShop;

/**
 *
 */
abstract class Core_Exports_Module_Controller_ProductSettings extends MVC_Controller_Default
{
	protected Product|EShopEntity_Admin_Interface $product;
	
	protected EShop $eshop;
	
	protected Exports_Module $export;
	
	protected ?Exports_ExportCategory $category = null;
	
	protected bool $editable;
	
	protected Exports_Join_Product $product_join;
	
	protected UI_tabs $tabs;
	
	protected ?Form $parameters_form;
	
	public function getProduct(): EShopEntity_Admin_Interface|Product
	{
		return $this->product;
	}
	
	public function getEshop(): EShop
	{
		return $this->eshop;
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
	
	public function getParametersForm(): ?Form
	{
		return $this->parameters_form;
	}
	
	public function getProductJoin(): Exports_Join_Product
	{
		return $this->product_join;
	}
	
	
	
	public function resolve(): bool|string
	{
		return $this->tabs->getSelectedTabId();
	}
	
	
	public function init(
		Product|EShopEntity_Admin_Interface $product,
		EShop                               $eshop,
		Exports_Module                      $export
	): void
	{
		$this->product = $product;
		$this->eshop = $eshop;
		$this->export = $export;
		$this->editable = $product->isEditable();
		$this->category = $this->export->getCategory( $eshop, $product );
		$this->product_join = $this->export->getProductJoin( $this->eshop, $this->product->getId() );
		
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
		$this->parameters_form = $this->export->getParamsEditForm( $this->eshop, $this->product );
		
		
		if($this->editable) {
			if( $this->parameters_form ) {
				if($this->export->catchParamsEditForm( $this->eshop, $this->product )) {
					UI_messages::success(
						Tr::_( 'Product <b>%NAME%</b> has been updated', [ 'NAME' => $this->product->getAdminTitle() ] )
					);
					
					Http_Headers::reload();
				}
			}
			
			if( $this->category ) {
				if(Http_Request::GET()->exists('actualize_list_of_parameters')) {
					$this->export->actualizeCategory( $this->eshop, $this->category->getCategoryId() );
					Http_Headers::reload(unset_GET_params: ['actualize_list_of_parameters']);
				}
				
			}
			
			if( $this->product_join->getEditForm()->catch() ) {
				
				$this->product_join->save();
				
				Http_Headers::reload();
			}
			
			
		} else {
			$this->parameters_form->setIsReadonly();
		}
		
		/** @noinspection PhpParamsInspection */
		echo Application_Service_Admin::Product()->renderExportSettings_parameters( $this );
	}
}