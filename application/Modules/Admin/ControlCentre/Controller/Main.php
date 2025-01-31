<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentre;


use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\UI;
use JetApplication\Admin_ControlCentre;
use JetApplication\EShops;
use Jet\Navigation_Breadcrumb;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$modules = Admin_ControlCentre::getModuleList();

		$GET = Http_Request::GET();
		
		$selected_module_name = $GET->getString(
			key: 'id',
			default_value: '',
			valid_values: array_keys( $modules )
		);
		$selected_module = $modules[$selected_module_name]??null;
		
		if($selected_module) {
			Navigation_Breadcrumb::addURL( UI::icon( $selected_module->getControlCentreIcon() ).' '.$selected_module->getControlCentreTitle() );
		}
		
		$selected_eshop_key = $GET->getString(
			key: 'eshop',
			default_value: EShops::getDefault()->getKey(),
			valid_values: array_keys( EShops::getList() )
		);
		$selected_eshop = EShops::get( $selected_eshop_key );
		
		$this->view->setVar( 'selected_module', $selected_module );
		$this->view->setVar( 'selected_eshop', $selected_eshop );
		
		$this->output('default');
	}
}