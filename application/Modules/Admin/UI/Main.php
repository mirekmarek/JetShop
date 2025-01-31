<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\UI;


use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Session;
use Jet\Translator;

use JetApplication\Admin_Managers_UI;
use JetApplication\EShops;



class Main extends Application_Module implements Admin_Managers_UI
{

	protected MVC_View $view;
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	
	protected function render( $script ) : string
	{
		$res = '';
		
		Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use (&$res, $script) {
				$res = $this->view->render($script);
			}
		);
		
		return $res;
	}
	
	
	
	
	public function renderSelectEntityWidget(
		string $name,
		string $caption,
		string $on_select,
		string $entity_type,
		string|array|null $object_type_filter,
		?bool $object_is_active_filter,
		?string $selected_entity_title,
		?string $selected_entity_edit_URL
	) : string
	{
		$this->view->setVar('name', $name);
		$this->view->setVar('caption', $caption);
		$this->view->setVar('on_select', $on_select);
		$this->view->setVar('entity_type', $entity_type);
		$this->view->setVar('object_type_filter', $object_type_filter);
		$this->view->setVar('object_is_active_filter', $object_is_active_filter);
		$this->view->setVar('selected_entity_title', $selected_entity_title);
		$this->view->setVar('selected_entity_edit_URL', $selected_entity_edit_URL);
		
		return $this->render('select-entity-widget');
	}
	
	
	public function renderEntityToolbar( Form $form, ?callable $buttons_renderer=null ) : string
	{
		$this->view->setVar('form', $form);
		$this->view->setVar('buttons_renderer', $buttons_renderer);
		
		return $this->render( 'entity-toolbar' );
	}
	
	
	public const CURR_ESHOP_SESSION = 'current_eshop';
	public const CURR_ESHOP_SESSION_KEY = 'key';
	public const CURR_ESHOP_GET_PARAM = 'select_eshop';
	

	public function handleCurrentPreferredShop() : void
	{
		$all_eshops = array_keys(EShops::getList());
		$default_eshop = EShops::getDefault();
		
		$session = new Session( static::CURR_ESHOP_SESSION );
		$current_eshop_key = $session->getValue(static::CURR_ESHOP_SESSION_KEY, '');
		if(!in_array($current_eshop_key, $all_eshops)) {
			$current_eshop_key = $default_eshop->getKey();
			$session->setValue(static::CURR_ESHOP_SESSION_KEY, $current_eshop_key);
		}
		
		
		$GET = Http_Request::GET();
		if($GET->exists(static::CURR_ESHOP_GET_PARAM)) {
			$current_eshop_key = $GET->getString(
				key:static::CURR_ESHOP_GET_PARAM,
				default_value: $default_eshop->getKey(),
				valid_values: $all_eshops
			);
			
			$session->setValue(static::CURR_ESHOP_SESSION_KEY, $current_eshop_key);
			
			Http_Headers::reload(unset_GET_params: [static::CURR_ESHOP_GET_PARAM]);
		}
		
		EShops::setCurrent( EShops::get($current_eshop_key) );
	}
	
	public function renderMainMenu() : string
	{
		return $this->render( 'main-menu' );
	}
	
}