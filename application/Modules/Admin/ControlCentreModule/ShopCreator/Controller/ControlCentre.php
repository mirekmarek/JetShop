<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentreModule\ShopCreator;

use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\EShop;


/**
 *
 */
class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		
		$new_eshop = new EShop();
		
		$form = $new_eshop->getCreateForm();
		
		$error_message = '';
		if($new_eshop->catchCreateForm( $error_message )) {
			UI_messages::success( Tr::_('New e-shop has been created'), 'CC' );
			Http_Headers::reload();
		}
		
		if($error_message) {
			UI_messages::danger( $error_message );
		}
		
		
		$this->view->setVar('new_eshop', $new_eshop);
		$this->view->setVar('form', $form);
		
		
		$this->output('default');
	}
}