<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Availabilities;

use Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Availabilities;
use JetApplication\Availability;


/**
 *
 */
class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		$save = function() {
			$ok = true;
			try {
				Availabilities::saveCfg();
			} catch( Exception $e ) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
		};
		
		$GET = Http_Request::GET();
		if( $GET->exists('add') ) {
			$new_pl = new Availability();
			
			if( $new_pl->getAddForm()->catch() ) {
				Availabilities::addAvailability( $new_pl );
				$save();
				Http_Headers::reload();
			}
			
			
			$this->view->setVar('new_avl', $new_pl);
		}
		
		
		$selected_pl_code = $GET->getString('availability', default_value: '', valid_values: array_keys(Availabilities::getList()));
		
		if($selected_pl_code) {
			$selected_pl = Availabilities::get($selected_pl_code);
			
			if($GET->exists('delete')) {
				Availabilities::removeAvailability( $selected_pl );
				$save();
				Http_Headers::reload(unset_GET_params: ['availability', 'delete']);
			}
			
			
			if( $selected_pl->getEditForm()->catch() ) {
				$save();
				Http_Headers::reload();
			}
			
			$this->view->setVar('selected_avl', $selected_pl);
		}
		
		
		
		$this->output('default');
	}
}