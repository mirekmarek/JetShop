<?php
namespace JetApplication;

use Jet\DataModel_Definition_Property_DataModel;
use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;

trait Admin_Entity_Trait {
	/**
	 * @var static[]
	 */
	protected static array $loaded_items = [];
	
	protected ?Form $_add_form = null;
	
	protected ?Form $_edit_form = null;
	
	protected bool $editable;
	
	public function __construct()
	{
		/** @noinspection PhpMultipleClassDeclarationsInspection */
		parent::__construct();
		
		$this->afterLoad();
	}
	
	public function afterLoad() : void
	{
		$this->checkShopData();
	}
	
	public function isEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = $editable;
	}
	
	
	
	
	
	public static function get( int|string $id_or_code ) : static|null
	{
		if(!$id_or_code) {
			return null;
		}
		
		if(isset( static::$loaded_items[$id_or_code])) {
			return static::$loaded_items[$id_or_code];
		}
		
		
		static::$loaded_items[$id_or_code] = static::load( $id_or_code );
		
		return static::$loaded_items[$id_or_code];
	}
	
	
	public function checkShopData() : void
	{
		/**
		 * @var DataModel_Definition_Property_DataModel $def
		 */
		$def = static::getDataModelDefinition()->getProperty('shop_data');
		
		$shop_data_class = $def->getValueDataModelClass();
		
		foreach( Shops::getList() as $shop ) {
			$key = $shop->getKey();
			
			if(!isset($this->shop_data[$key])) {
				
				$sh = new $shop_data_class();
				
				$this->shop_data[$key] = $sh;
			}
			
			$this->shop_data[$key]->setShop( $shop );
		}
	}
	
	public function handleImages() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $this->isEditable() );
		$manager->handleSelectImageWidgets();
	}
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
		}
		
		return $this->_add_form;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			if(!$this->isEditable()) {
				$this->_edit_form->setIsReadonly();
			}
		}
		
		return $this->_edit_form;
	}
	
	
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}
	
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}
	
	/**
	 *
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getList() : DataModel_Fetch_Instances|iterable
	{
		$where = [];
		
		return static::fetchInstances( [] );
	}
	
	public function handleActivation() : void
	{
		
		$logEntityActivation = function() {
			$entity_type = $this->getEntityType();
			$entity_id = ($this instanceof Entity_WithCodeAndShopData) ? $this->getCode() : $this->getId();
			
			Logger::success(
				event: 'entity_activated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') activated',
				context_object_id: $entity_id,
				context_object_name: $this->getInternalName()
			);
		};
		$logEntityDeactivation = function() {
			$entity_type = $this->getEntityType();
			$entity_id = ($this instanceof Entity_WithCodeAndShopData) ? $this->getCode() : $this->getId();
			
			Logger::success(
				event: 'entity_deactivated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') deactivated',
				context_object_id: $entity_id,
				context_object_name: $this->getInternalName()
			);
		};
		$logEntityShopDataActivation = function( Shops_Shop $shop ) {
			$entity_type = $this->getEntityType();
			$entity_id = ($this instanceof Entity_WithCodeAndShopData) ? $this->getCode() : $this->getId();
			
			Logger::success(
				event: 'entity_shop_data_activated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') shop data '.$shop->getKey().' activated',
				context_object_id: $entity_id.':'.$shop->getKey(),
				context_object_name: $this->getInternalName().' ('.$shop->getShopName().')'
			);
		};
		$logEntityShopDataDeactivation = function( Shops_Shop $shop ) {
			$entity_type = $this->getEntityType();
			$entity_id = ($this instanceof Entity_WithCodeAndShopData) ? $this->getCode() : $this->getId();
			
			Logger::success(
				event: 'entity_shop_data_deactivated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') shop data '.$shop->getKey().' deactivated',
				context_object_id: $entity_id.':'.$shop->getKey(),
				context_object_name: $this->getInternalName().' ('.$shop->getShopName().')'
			);
		};
		
		$GET = Http_Request::GET();
		
		if($GET->exists('deactivate_entity')) {
			if($this->isActive()) {
				$this->deactivate();
				$logEntityDeactivation();
			}
			Http_Headers::reload(unset_GET_params: ['deactivate_entity']);
		}
		if($GET->exists('activate_entity')) {
			if(!$this->isActive()) {
				$this->activate();
				$logEntityActivation();
			}
			Http_Headers::reload(unset_GET_params: ['activate_entity']);
		}
		if($GET->exists('activate_entity_completely')) {
			if(!$this->isActive()) {
				$this->activate();
				$logEntityActivation();
			}
			
			foreach(Shops::getList() as $shop) {
				if(!$this->getShopData($shop)->isActiveForShop()) {
					$this->getShopData($shop)->activate();
					$logEntityShopDataActivation( $shop );
				}
			}
			
			Http_Headers::reload(unset_GET_params: ['activate_entity_completely']);
		}
		if($GET->exists('activate_entity_shop_data')) {
			$shop = Shops::get( $GET->getString('activate_entity_shop_data') );
			
			if(!$this->getShopData($shop)->isActiveForShop()) {
				$this->getShopData($shop)->activate();
				$logEntityShopDataActivation( $shop );
			}
			Http_Headers::reload(unset_GET_params: ['activate_entity_shop_data']);
		}
		if($GET->exists('deactivate_entity_shop_data')) {
			$shop = Shops::get( $GET->getString('deactivate_entity_shop_data') );
			
			if($this->getShopData($shop)->isActiveForShop()) {
				$this->getShopData($shop)->deactivate();
				$logEntityShopDataDeactivation( $shop );
			}
			Http_Headers::reload(unset_GET_params: ['deactivate_entity_shop_data']);
		}
		
	}
	
}