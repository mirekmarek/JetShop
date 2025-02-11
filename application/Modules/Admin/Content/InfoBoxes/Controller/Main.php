<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\InfoBoxes;


use Jet\Tr;
use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getTabs(): array
	{
		$tabs = parent::getTabs();
		
		if(isset($tabs['description'])) {
			$tabs['description'] = Tr::_('Content');
		}
		
		return $tabs;
	}
}