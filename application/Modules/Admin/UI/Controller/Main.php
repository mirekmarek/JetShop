<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\UI;

use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\MVC;
use Jet\MVC_Controller_Default;

use Jet\Auth;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Page_Content_Interface;
use JetApplication\AdministratorSignatures;
use JetApplication\Shops;


/**
 *
 */
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
		$signatures_form = new Form('admin_signatures_form', []);
		
		foreach(Shops::getList() as $shop) {
			$signature = new Form_Field_Textarea('/signature/'.$shop->getKey(), '');
			$signature->setDefaultValue( AdministratorSignatures::getSignature( $shop ) );
			$signature->setFieldValueCatcher( function() use ($signature, $shop) {
				AdministratorSignatures::setSignature( $shop, Auth::getCurrentUser()->getId(), $signature->getValue() );
			} );
			$signatures_form->addField( $signature );
		}
		
		if($signatures_form->catch()) {
			Http_Headers::reload();
		}
		
		$this->view->setVar('admin_signatures_form', $signatures_form);
		
		$this->output( 'default' );
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