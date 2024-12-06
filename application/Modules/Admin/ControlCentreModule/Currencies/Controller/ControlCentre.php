<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Currencies;

use Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Currencies;
use JetApplication\Currency;


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
				Currencies::saveCfg();
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
			$new_currency = new Currency();
			
			if( $new_currency->getAddForm()->catch() ) {
				Currencies::addCurrency( $new_currency );
				$save();
				Http_Headers::reload();
			}
			
			
			$this->view->setVar('new_currency', $new_currency);
		}
		
		
		$selected_currency_code = $GET->getString('currency', default_value: '', valid_values: array_keys(Currencies::getList()));
		
		if($selected_currency_code) {
			$selected_currency = Currencies::get($selected_currency_code);
			
			if($GET->exists('delete')) {
				Currencies::removeCurrency( $selected_currency );
				$save();
				Http_Headers::reload(unset_GET_params: ['currency', 'delete']);
			}
			
			
			if( $selected_currency->getEditForm()->catch() ) {
				$save();
				Http_Headers::reload();
			}
			
			$this->view->setVar('selected_currency', $selected_currency);
		}
		
		
		
		$this->output('default');
	}
}