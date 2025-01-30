<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\TimerManager;

use Jet\AJAX;
use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;
use JetApplication\Application_Admin;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\Timer;

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
		MVC_Layout::getCurrentLayout()->setScriptName('dialog');
		
		$GET = Http_Request::GET();
		
		$entity_type = $GET->getString('entity_type');
		$entity_class = $GET->getString('entity_class');
		$entity_id = $GET->getString('entity_id');
		
		if(
			!class_exists($entity_class)
		) {
			die();
		}
		
		/**
		 * @var EShopEntity_WithEShopData $entity
		 */
		$entity = $entity_class::load( $entity_id );
		
		if(!$entity) {
			die();
		}
		
		$actions = $entity->getAvailableTimerActions();
		$this->view->setVar('actions', $actions);
		
		
		if($GET->getBool('reload_settings')) {

			AJAX::snippetResponse(
				$this->module->_renderEntityEdit( $entity, Main::getCurrentUserCanSet() )
			);
		}
		
		
		$page = MVC::getPage('timer-manager', base_id: Application_Admin::getBaseId());
		
		
		$reload_URL = $page->getURL(  GET_params: [
			'entity_type' => $entity_type,
			'entity_class' => $entity_class,
			'entity_id' => $entity_id,
			'reload_settings' => true
		]);
		
		$this->view->setVar( 'reload_URL', $reload_URL );
		

		if(Main::getCurrentUserCanSet()) {
			$forms = [];
			
			foreach($actions as $action) {
				$date_time = new Form_Field_DateTime('date_time', 'Date and time:');
				$date_time->setErrorMessages([
					Form_Field_DateTime::ERROR_CODE_INVALID_FORMAT => 'Invalid value'
				]);
				
				$form = new Form('set_form_'.$action->getAction(), [$date_time]);
				$form->setAction( Http_Request::currentURI(set_GET_params: ['set_action'=>$action->getAction()]) );
				$action->updateForm( $form );
				$forms[$action->getAction()] = $form;
			}
			
			foreach($forms as $action_key=>$form) {
				if($form->catch()) {
					$action = $actions[$action_key];
					$context_value = $action->catchActionContextValue( $form );
					$date_time = $form->field('date_time')->getValue();
					
					if($date_time>Data_DateTime::now()) {
						Timer::newTimer(
							entity: $entity,
							date_time: $date_time,
							action: $action,
							action_context: $context_value
						)->save();
					}
					
					Http_Headers::reload(unset_GET_params: ['set_action']);
				}
			}
			
			$this->view->setVar('forms', $forms);
		}
		
		$scheduled = Timer::getScheduled( $entity );
		
		if( ($cancel=$GET->getInt('cancel')) ) {
			if(Main::getCurrentUserCanCancel()) {
				foreach($scheduled as $item) {
					if($item->getId()==$cancel) {
						$item->delete();
					}
				}
			}
			
			Http_Headers::reload(unset_GET_params: ['cancel']);
		}
		
		$this->view->setVar('scheduled', $scheduled);
		
		$this->output('default');
	}
}