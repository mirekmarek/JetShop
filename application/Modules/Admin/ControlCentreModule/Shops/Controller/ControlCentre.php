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
use JetApplication\EShops;


/**
 *
 */
class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		
		$GET = Http_Request::GET();
		
		$selected_eshop_code = $GET->getString('eshop', default_value: '', valid_values: array_keys(EShops::getList()));
		
		if($selected_eshop_code) {
			$selected_eshop = EShops::get($selected_eshop_code);
			
			if( $selected_eshop->getEditForm()->catch() ) {
				$ok = true;
				try {
					$selected_eshop->save();
				} catch( Exception $e ) {
					$ok = false;
					UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
				}
				
				if($ok) {
					UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
				}
				
				Http_Headers::reload();
			}
			
			$this->view->setVar('selected_eshop', $selected_eshop);
		}
		
		
		
		$this->output('default');
	}
}