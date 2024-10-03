<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\TimerManager;

use Jet\Application_Module;
use Jet\Auth;
use Jet\Factory_MVC;
use Jet\MVC;
use Jet\MVC_View;
use Jet\Translator;
use JetApplication\Admin_Managers_Timer;
use JetApplication\Application_Admin;
use JetApplication\Auth_Administrator_Role;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;
use JetApplication\Timer;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Timer
{
	public const ACTION_VIEW_TIMERS = 'view_timers';
	public const ACTION_SET_TIMER = 'set_timer';
	public const ACTION_CANCEL_TIMER = 'cancel_timer';
	
	protected function getView() : MVC_View
	{
		return Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	public function renderIntegration() : string
	{
		if(!$this->getCurrentUserCanView()) {
			return '';
		}
		
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() {
				$page = MVC::getPage('timer-manager', base_id: Application_Admin::getBaseId());
				
				$view = $this->getView();
				$view->setVar('page_url', $page->getURL());
				
				return $view->render('integration');
			}
		);
	}
	
	public function renderIcon( Entity_WithShopData $entity, Shops_Shop $shop ) : string
	{
		if(!$this->getCurrentUserCanView()) {
			return '';
		}
		
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ($entity, $shop ) {
				$view = $this->getView();
				$class = addslashes(get_parent_class( $entity ));
				
				$view->setVar('entity', $entity);
				$view->setVar('shop', $shop);
				$view->setVar('class', $class);
				$view->setVar('has_not_processed', Timer::hasNotProcessed( $entity, $shop ));
				
				return $view->render('button');
			}
		);
	}
	
	public static function getCurrentUserCanView() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_VIEW_TIMERS );
	}
	
	public static function getCurrentUserCanSet() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_SET_TIMER );
	}
	
	public static function getCurrentUserCanCancel() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_CANCEL_TIMER );
	}
	
	
	
}