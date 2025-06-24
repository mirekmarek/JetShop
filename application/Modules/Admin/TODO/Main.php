<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\TODO;

use Jet\Factory_MVC;
use Jet\MVC_View;
use Jet\Tr;
use JetApplication\Admin_Managers_Todo;


class Main extends Admin_Managers_Todo
{
	
	protected function getView() : MVC_View
	{
		return Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	
	public function renderTool( string $entity_type, int $entity_id ): string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($entity_type, $entity_id) {
				$view = $this->getView();
				$view->setVar('entity_type', $entity_type);
				$view->setVar('entity_id', $entity_id);
				
				return $view->render('entity-edit');
			}
		);
	}
	
	public function renderHasTodoTag(  string $entity_type, int $entity_id  ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($entity_type, $entity_id) {
				if(!Item::entotyHasTodo( $entity_type, $entity_id )) {
					return '';
				}

				$view = $this->getView();
				$view->setVar('entity_type', $entity_type);
				$view->setVar('entity_id', $entity_id);
				
				return $view->render('entity-has-todo-tag');
			}
		);
		
	}
	
	
	public function renderDashboard() : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				$view = $this->getView();
				
				return $view->render('dashboard');
			}
		);
	}
}