<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\UI;


use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\Navigation_MenuSet;

use Jet\Auth;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Page_Content_Interface;
use JetApplication\Admin_UserSettings;
use JetApplication\AdministratorSignatures;
use JetApplication\EShops;



class Controller_Main extends MVC_Controller_Default
{
	
	public function __construct( MVC_Page_Content_Interface $content )
	{
		parent::__construct( $content );



		$GET = Http_Request::GET();

		if( $GET->exists( 'logout' ) ) {
			$this->logout_Action();
		}
	}
	
	public function logout_Action() : void
	{
		Auth::logout();

		Http_Headers::movedTemporary( MVC::getPage( MVC::HOMEPAGE_ID )->getURL() );
	}

	public function default_Action() : void
	{
		$this->handleMenuSetup();
		$this->handleSignatures();
		
		$this->output( 'default' );
	}
	
	protected function handleMenuSetup() : void
	{
		if(Http_Request::GET()->getString('action')=='setup_menu') {
			$POST = Http_Request::POST();
			
			$visible_menu = $POST->getRaw('visible_menu');
			$visible_menu_items = $POST->getRaw('visible_menu_items');
			
			
			$_menus = Navigation_MenuSet::get('admin')->getMenus();
			
			$all_menus = [];
			$all_items = [];
			
			foreach($_menus as $menu) {
				$all_menus[] = $menu->getId();
				foreach($menu->getItems() as $item) {
					$all_items[] = $item->getId();
				}
			}
			
			$hiden_menus = [];
			$hiden_items = [];
			
			foreach($all_menus as $menu_id) {
				if(!in_array($menu_id, $visible_menu)) {
					$hiden_menus[] = $menu_id;
				}
			}
			
			foreach($all_items as $item_id) {
				if(!in_array($item_id, $visible_menu_items)) {
					$hiden_items[] = $item_id;
				}
			}
			
			
			$settings = Admin_UserSettings::get();
			$settings->setHidenMenus( $hiden_menus );
			$settings->setHidenItems( $hiden_items );
			$settings->save();
			
			Http_Headers::reload(unset_GET_params: ['action']);
		}
		
	}
	
	protected function handleSignatures() : void
	{
		$signatures_form = new Form('admin_signatures_form', []);
		
		foreach( EShops::getList() as $eshop) {
			$signature = new Form_Field_Textarea('/signature/'.$eshop->getKey(), '');
			$signature->setDefaultValue( AdministratorSignatures::getSignature( $eshop ) );
			$signature->setFieldValueCatcher( function() use ($signature, $eshop) {
				AdministratorSignatures::setSignature( $eshop, Auth::getCurrentUser()->getId(), $signature->getValue() );
			} );
			$signatures_form->addField( $signature );
		}
		
		if($signatures_form->catch()) {
			Http_Headers::reload();
		}
		
		$this->view->setVar('admin_signatures_form', $signatures_form);
	}

	public function breadcrumb_navigation_Action() : void
	{
		$this->output( 'breadcrumb_navigation' );
	}

	public function messages_Action() : void
	{
		$this->output( 'messages' );
	}

	public function main_menu_Action() : void
	{
		$this->output( 'main_menu' );
	}

}