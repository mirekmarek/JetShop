<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\TimerManager;

use Jet\Auth;
use Jet\Factory_MVC;
use Jet\MVC;
use Jet\MVC_View;
use Jet\Translator;
use JetApplication\Admin_Managers_Timer;
use JetApplication\Application_Admin;
use JetApplication\Auth_Administrator_Role;
use JetApplication\EShopEntity_HasTimer_Interface;


class Main extends Admin_Managers_Timer
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
		if(!$this->getCurrentUserCanSet()) {
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
	
	public function renderEntityEdit( EShopEntity_HasTimer_Interface $entity, bool $editable ) : string
	{
		return '<div id="timmer_settings">'.$this->_renderEntityEdit($entity, $editable).'</div>';
	}
	
	public function _renderEntityEdit( EShopEntity_HasTimer_Interface $entity, bool $editable ) : string
	{
		return Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use ( $entity, $editable ) {
				$view = $this->getView();
				$class = addslashes( get_class( $entity ) );
				if(str_starts_with($class, 'JetApplicationModule')) {
					$class = addslashes( get_parent_class( $entity ) );
				}
				
				$view->setVar('entity', $entity);
				$view->setVar('class', $class);
				$view->setVar('editable', $editable);
				
				$res = '';
				
				$res .= $view->render('entity-edit');
				
				return $res;
			}
		);
	}
	
	protected static function getModuleName() : string
	{
		$module_name = substr( get_called_class(), 21, -5 );
		$module_name = str_replace('\\', '.', $module_name);
		
		return $module_name;
	}
	
	protected static function getCurrentUserCanDoAction( string $action ) : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::getModuleName().':'.$action );
	}
	
	
	public static function getCurrentUserCanView() : bool
	{
		return static::getCurrentUserCanDoAction( static::ACTION_VIEW_TIMERS );
	}
	
	public static function getCurrentUserCanSet() : bool
	{
		return static::getCurrentUserCanDoAction( static::ACTION_SET_TIMER );
	}
	
	public static function getCurrentUserCanCancel() : bool
	{
		return static::getCurrentUserCanDoAction( static::ACTION_CANCEL_TIMER );
	}
	
	
	
}