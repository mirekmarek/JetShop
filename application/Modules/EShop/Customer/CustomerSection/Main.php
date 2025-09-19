<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Customer\CustomerSection;

use Jet\MVC;
use Jet\Tr;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\Customer;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_EShop_CustomerSection;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShop_Pages;


class Main extends Application_Service_EShop_CustomerSection implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public static function initTabs( &$selected_tab='' ) : UI_tabs
	{
		$tabs = [];
		
		$tabs[''] = UI::icon('user').' '.Tr::_('Basic information');
		$tabs['addresses'] = UI::icon('address-book').' '.Tr::_('Addresses');
		$tabs['orders'] = UI::icon('box').' '.Tr::_('Orders');
		
		if( ($reviews_manager = Application_Service_EShop::ProductReviews()) ) {
			$tabs['reviews'] = UI::icon('reviews').' '.Tr::_('Product reviews');
		}
		
		$tabs['newsletter-subscription'] = UI::icon('envelope').' '.Tr::_('Newsletter subscription');
		
		if(EShop_Pages::Complaints()) {
			$tabs['complaints'] = UI::icon('complaint').' '.Tr::_('Complaints');
		}
		
		if(EShop_Pages::ReturnOfGoods()) {
			$tabs['return-of-goods'] = UI::icon('return').' '.Tr::_('Returns');
		}
		
		if(!$selected_tab) {
			$path = MVC::getRouter()->getUrlPath();
			if($path && isset($tabs[$path])) {
				$selected_tab = $path;
				MVC::getRouter()->setUsedUrlPath( $selected_tab );
			}
		}
		
		
		return new UI_tabs(
			tabs: $tabs,
			tab_url_creator: function(string $tab) : string {
				
				if($tab=='complaints') {
					return EShop_Pages::Complaints()->getURL();
				}
				
				if($tab=='return-of-goods') {
					return EShop_Pages::ReturnOfGoods()->getURL();
				}
				
				if($tab=='reviews') {
					return EShop_Pages::ProductReviews()->getURL();
				}
				
				
				return EShop_Pages::CustomerSection()->getURL([$tab]);
			},
			selected_tab_id: $selected_tab
		);
		
	}
	
	public function showMenu( string $selected_section ): string
	{
		if(!Customer::getCurrentCustomer()) {
			return '';
		}
		
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ( $selected_section ) {
				return static::initTabs(  $selected_section )->toString();
			}
		);
	}
}