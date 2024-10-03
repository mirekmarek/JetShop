<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Customer\CustomerSection;

use Jet\Application_Module;
use Jet\MVC;
use Jet\Tr;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\Shop_Managers;
use JetApplication\Shop_Managers_CustomerSection;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplication\Shop_Pages;

/**
 *
 */
class Main extends Application_Module implements Shop_ModuleUsingTemplate_Interface, Shop_Managers_CustomerSection
{
	use Shop_ModuleUsingTemplate_Trait;
	
	public static function initTabs( &$selected_tab='' ) : UI_tabs
	{
		$tabs = [];
		
		$tabs[''] = UI::icon('user').' '.Tr::_('Basic information');
		$tabs['addresses'] = UI::icon('address-book').' '.Tr::_('Addresses');
		$tabs['orders'] = UI::icon('box').' '.Tr::_('Orders');
		
		if( ($reviews_manager = Shop_Managers::ProductReviews()) ) {
			$tabs['reviews'] = UI::icon('reviews').' '.Tr::_('Product reviews');
		}
		
		$tabs['newsletter-subscription'] = UI::icon('envelope').' '.Tr::_('Newsletter subscription');
		
		if(Shop_Pages::Complaints()) {
			$tabs['complaints'] = UI::icon('complaint').' '.Tr::_('Complaints');
		}
		
		if(Shop_Pages::ReturnOfGoods()) {
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
					return Shop_Pages::Complaints()->getURL();
				}
				
				if($tab=='return-of-goods') {
					return Shop_Pages::ReturnOfGoods()->getURL();
				}
				
				return Shop_Pages::CustomerSection()->getURL([$tab]);
			},
			selected_tab_id: $selected_tab
		);
		
	}
}