<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\TimerManager;

use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;
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
		$shop_key = $GET->getString('shop_key');
		
		if(
			!class_exists($entity_class) ||
			!Shops::exists($shop_key)
		) {
			die();
		}
		
		$shop = Shops::get( $shop_key );
		
		/**
		 * @var Entity_WithShopData $entity
		 */
		$entity = $entity_class::load( $entity_id );
		
		if(!$entity) {
			die();
		}
		
		$actions = $entity->getShopData( $shop )->getAvailableTimerActions();
		$this->view->setVar('actions', $actions);

		if(Main::getCurrentUserCanSet()) {
			$forms = [];
			
			foreach($actions as $action) {
				$date_time = new Form_Field_DateTime('date_time', 'Date time:');
				$date_time->setErrorMessages([
					Form_Field_DateTime::ERROR_CODE_INVALID_FORMAT => 'Invalid value'
				]);
				
				$form = new Form('set_form_'.$action->getKey(), [$date_time]);
				$form->setAction( Http_Request::currentURI(set_GET_params: ['set_action'=>$action->getKey()]) );
				$action->updateForm( $form );
				$forms[$action->getKey()] = $form;
			}
			
			foreach($forms as $action_key=>$form) {
				if($form->catch()) {
					$action = $actions[$action_key];
					$context_value = $action->catchActionContextValue( $form );
					$date_time = $form->field('date_time')->getValue();
					
					if($date_time>Data_DateTime::now()) {
						Timer::newTimer(
							entity: $entity,
							shop: $shop,
							date_time: $date_time,
							action: $action_key,
							action_context: $context_value
						)->save();
					}
					
					Http_Headers::reload(unset_GET_params: ['set_action']);
				}
			}
			
			$this->view->setVar('forms', $forms);
		}
		
		$scheduled = Timer::getScheduled( $entity, $shop );
		
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