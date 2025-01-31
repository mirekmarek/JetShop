<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\ControlCentreModule\Pricelists;


use Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Pricelists;
use JetApplication\Pricelist;


class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$save = function() {
			$ok = true;
			try {
				Pricelists::saveCfg();
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
			$new_pl = new Pricelist();
			
			if( $new_pl->getAddForm()->catch() ) {
				Pricelists::addPricelist( $new_pl );
				$save();
				Http_Headers::reload();
			}
			
			
			$this->view->setVar('new_pl', $new_pl);
		}
		
		
		$selected_pl_code = $GET->getString('pricelist', default_value: '', valid_values: array_keys(Pricelists::getList()));
		
		if($selected_pl_code) {
			$selected_pl = Pricelists::get($selected_pl_code);
			
			if($GET->exists('delete')) {
				Pricelists::removePricelist( $selected_pl );
				$save();
				Http_Headers::reload(unset_GET_params: ['pricelist', 'delete']);
			}
			
			
			if( $selected_pl->getEditForm()->catch() ) {
				$save();
				Http_Headers::reload();
			}
			
			$this->view->setVar('selected_pl', $selected_pl);
		}
		
		
		
		$this->output('default');
	}
}