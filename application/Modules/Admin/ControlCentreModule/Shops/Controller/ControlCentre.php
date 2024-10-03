<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Shops;

use Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Shops;


/**
 *
 */
class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		
		$GET = Http_Request::GET();
		
		$selected_shop_code = $GET->getString('shop', default_value: '', valid_values: array_keys(Shops::getList()));
		
		if($selected_shop_code) {
			$selected_shop = Shops::get($selected_shop_code);
			
			if( $selected_shop->getEditForm()->catch() ) {
				$ok = true;
				try {
					$selected_shop->save();
				} catch( Exception $e ) {
					$ok = false;
					UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
				}
				
				if($ok) {
					UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
				}
				
				Http_Headers::reload();
			}
			
			$this->view->setVar('selected_shop', $selected_shop);
		}
		
		
		
		$this->output('default');
	}
}