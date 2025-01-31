<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\ControlCentreModule\MeasureUnits;


use Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\MeasureUnit;
use JetApplication\MeasureUnits;



class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		$save = function() {
			$ok = true;
			try {
				MeasureUnits::saveCfg();
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
			$new_measure_unit = new MeasureUnit();
			
			if( $new_measure_unit->getAddForm()->catch() ) {
				MeasureUnits::add( $new_measure_unit );
				$save();
				Http_Headers::reload();
			}
			
			
			$this->view->setVar('new_measure_unit', $new_measure_unit);
		}
		
		
		$selected_measure_unit_code = $GET->getString('measure_unit', default_value: '', valid_values: array_keys(MeasureUnits::getScope()));
		
		if($selected_measure_unit_code) {
			$selected_measure_unit = MeasureUnits::get($selected_measure_unit_code);
			
			if($GET->exists('delete')) {
				MeasureUnits::remove( $selected_measure_unit );
				$save();
				Http_Headers::reload(unset_GET_params: ['measure_unit', 'delete']);
			}
			
			
			if( $selected_measure_unit->getEditForm()->catch() ) {
				$save();
				Http_Headers::reload();
			}
			
			$this->view->setVar('selected_measure_unit', $selected_measure_unit);
		}
		
		
		
		$this->output('default');
	}
}