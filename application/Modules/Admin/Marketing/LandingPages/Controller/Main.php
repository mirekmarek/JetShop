<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\LandingPages;


use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use Jet\UI_messages;
use Jet\Http_Headers;
use JetApplication\Marketing_LandingPage;



class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function getTabs(): array
	{
		$tabs = parent::getTabs();

		
		$tabs['landing_page'] = Tr::_('Landing page');
		
		return $tabs;
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_landing_page', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='landing_page';
			} );
		
	}
	
	
	
	public function edit_landing_page_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Landing page') );
		$this->view->setVar('item', $this->current_item);
		
		/**
		 * @var Marketing_LandingPage $item
		 */
		$item = $this->current_item;
		
		$this->view->setVar('form', $item->getLandingPageEditForm() );
		if($item->catchLandingPageEditForm()) {
			$item->save();
			
			UI_messages::success(
				$this->generateText_edit_main_msg()
			);
			
			Http_Headers::reload();
		}
		
		$this->output('edit/landing-page');
	}

	
	
}