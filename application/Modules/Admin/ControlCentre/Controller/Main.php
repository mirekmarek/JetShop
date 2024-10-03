<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentre;

use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\UI;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_Managers;
use JetApplication\Shops;
use JetApplicationModule\Shop\Catalog\Navigation_Breadcrumb;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		Admin_Managers::UI()->initBreadcrumb();

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
		
		$selected_shop_key = $GET->getString(
			key: 'shop',
			default_value: Shops::getDefault()->getKey(),
			valid_values: array_keys( Shops::getList() )
		);
		$selected_shop = Shops::get( $selected_shop_key );
		
		$this->view->setVar( 'selected_module', $selected_module );
		$this->view->setVar( 'selected_shop', $selected_shop );
		
		$this->output('default');
	}
}