<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\UI;


use Jet\AJAX;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Session;
use Jet\Translator;

use JetApplication\Application_Service_Admin_UI;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShops;



class Main extends Application_Service_Admin_UI
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
		string|EShopEntity_Basic $entity_class,
		string $name,
		string $on_select,
		string|EShopEntity_Basic|null $selected = null,
		string|array|null $object_type_filter = null,
		?bool $object_is_active_filter = null
	) : string
	{
		$entity_definition = $entity_class::getEntityDefinition();
		
		$caption = '.. select '.$entity_definition->getEntityNameReadable().' ...';
		
		$this->view->setVar('name', $name);
		$this->view->setVar('caption', $caption);
		$this->view->setVar('selected', $selected);
		$this->view->setVar('on_select', $on_select);
		$this->view->setVar('entity_class', $entity_class);
		$this->view->setVar('object_type_filter', $object_type_filter);
		$this->view->setVar('object_is_active_filter', $object_is_active_filter);
		
		return $this->render('select-entity-widget');
	}
	
	public function renderSelectEntitiesWidget(
		string|EShopEntity_Basic $entity_class,
		Form_Field_Hidden $input,
		string|array|null $object_type_filter=null,
		?bool $object_is_active_filter=null,
	) : string
	{
		$name = $input->getName();
		$js_class_name = $entity_class::getEntityType().'_'.$name;
		
		$this->view->setVar( 'entity_class', $entity_class );
		$this->view->setVar( 'input', $input );
		$this->view->setVar( 'js_class_name', $js_class_name );
		
		$this->view->setVar('object_type_filter', $object_type_filter);
		$this->view->setVar('object_is_active_filter', $object_is_active_filter);
		
		
		$GET = Http_Request::GET();
		if(
			$GET->exists('show_entities_widget_list') &&
			$GET->getString('input_id')==$input->getId()
		) {
			$input->setDefaultValue( $GET->getString('show_entities_widget_list') );
			
			AJAX::snippetResponse(
				$this->view->render('select-entities/list')
			);
		}
		
		return $this->view->render('select-entities/select');
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