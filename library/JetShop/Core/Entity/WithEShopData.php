<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel_Definition_Property_DataModel;
use Jet\Form_Definition;
use Jet\Logger;
use Jet\Tr;
use JetApplication\Entity_Basic;
use JetApplication\Entity_HasActivation_Interface;
use JetApplication\Entity_HasActivation_Trait;
use JetApplication\Entity_HasGet_Interface;
use JetApplication\Entity_HasGet_Trait;
use JetApplication\Entity_HasInternalParams_Interface;
use JetApplication\Entity_HasInternalParams_Trait;
use JetApplication\Entity_HasTimer_Interface;
use JetApplication\Entity_WithEShopData_EShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Timer_Action;

#[DataModel_Definition]
abstract class Core_Entity_WithEShopData extends Entity_Basic implements
	Entity_HasInternalParams_Interface,
	Entity_HasGet_Interface,
	Entity_HasActivation_Interface,
	Entity_HasTimer_Interface
{
	use Entity_HasInternalParams_Trait;
	use Entity_HasGet_Trait;
	use Entity_HasActivation_Trait;
	
	/**
	 * @var Entity_WithEShopData_EShopData[]
	 */
	#[Form_Definition(is_sub_forms: true)]
	protected array $eshop_data = [];
	
	public function __construct()
	{
		$this->checkShopData();
	}
	
	public function setId( int $id ) : void
	{
		$this->checkShopData();
		$this->id = $id;
	}
	
	
	public function afterLoad() : void
	{
		$this->checkShopData();
	}
	
	public function checkShopData() : void
	{
		/**
		 * @var DataModel_Definition_Property_DataModel $def
		 */
		$def = static::getDataModelDefinition()->getProperty('eshop_data');
		
		$eshop_data_class = $def->getValueDataModelClass();

		foreach( EShops::getList() as $eshop ) {
			$key = $eshop->getKey();
			
			if(!isset( $this->eshop_data[$key])) {
				
				/**
				 * @var Entity_WithEShopData_EShopData $sh
				 */
				$sh = new $eshop_data_class();
				$sh->setEntityId( $this->getId() );
				$sh->setEshop( $eshop );
				
				$this->eshop_data[$key] = $sh;
				
				if($this->getId()) {
					$this->eshop_data[$key]->actualizeRelations( $this->getIDController() );
					$this->eshop_data[$key]->save();
				}
			}
			
			$this->eshop_data[$key]->setEshop( $eshop );
		}
	}
	
	
	public function isActive() : bool
	{
		return $this->is_active;
	}
	
	public function activate() : void
	{
		if($this->is_active) {
			return;
		}
		
		$this->is_active = true;
		static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
		
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->_setEntityIsActive( $this->is_active );
		}
		
		$entity_type = $this->getEntityType();
		
		Logger::success(
			event: 'entity_activated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$this->id.') activated',
			context_object_id: $this->id,
			context_object_name: $this->getAdminTitle()
		);
		
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
		
	}
	
	public function deactivate() : void
	{
		if(!$this->is_active) {
			return;
		}
		
		$this->is_active = false;
		static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
		
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->_setEntityIsActive( $this->is_active );
		}
		
		$entity_type = $this->getEntityType();
		
		Logger::success(
			event: 'entity_deactivated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$this->getId().') deactivated',
			context_object_id: $this->getId(),
			context_object_name: $this->getAdminTitle()
		);
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	public function activateEShopData( EShop $eshop ) : void
	{
		$sd = $this->getEshopData( $eshop );

		if( $sd->isActiveForShop() ) {
			return;
		}
		
		$sd->_activate();
		
		$entity_type = $this->getEntityType();
		$entity_id = $this->getId();
		
		Logger::success(
			event: 'entity_eshop_data_activated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') eshop data '.$eshop->getKey().' activated',
			context_object_id: $entity_id.':'.$eshop->getKey(),
			context_object_name: $this->getAdminTitle()
		);
		
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	public function deactivateEShopData( EShop $eshop ) : void
	{
		$sd = $this->getEshopData( $eshop );
		if( !$sd->isActiveForShop() ) {
			return;
		}
		
		$sd->_deactivate();
		
		$entity_type = $this->getEntityType();
		$entity_id = $this->getId();
		
		Logger::success(
			event: 'entity_eshop_data_deactivated:'.$entity_type,
			event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') eshop data '.$eshop->getKey().' deactivated',
			context_object_id: $entity_id.':'.$eshop->getKey(),
			context_object_name: $this->getAdminTitle()
		);
		
		
		if($this instanceof FulltextSearch_IndexDataProvider) {
			$this->updateFulltextSearchIndex();
		}
	}
	
	public function activateCompletely() : void
	{
		
		$updated = false;
		
		if(!$this->is_active) {
			$this->is_active = true;
			static::updateData(data: ['is_active'=>$this->is_active], where: ['id'=>$this->id]);
			
			foreach( EShops::getList() as $eshop ) {
				$this->getEshopData( $eshop )->_setEntityIsActive( $this->is_active );
			}
			
			$entity_type = $this->getEntityType();
			
			Logger::success(
				event: 'entity_activated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$this->id.') activated',
				context_object_id: $this->id,
				context_object_name: $this->getAdminTitle()
			);
			
			$updated = true;
		}
		
		foreach( EShops::getList() as $eshop) {
			$sd = $this->getEshopData( $eshop );
			
			if( !$sd->isActiveForShop() ) {
				$sd->_activate();
				
				$entity_type = $this->getEntityType();
				$entity_id = $this->getId();
				
				Logger::success(
					event: 'entity_eshop_data_activated:'.$entity_type,
					event_message: 'Entity '.$entity_type.' \''.$this->getInternalName().'\' ('.$entity_id.') eshop data '.$eshop->getKey().' activated',
					context_object_id: $entity_id.':'.$eshop->getKey(),
					context_object_name: $this->getAdminTitle()
				);
				
				$updated = true;
			}
		}
		
		
		
		if(
			$updated &&
			$this instanceof FulltextSearch_IndexDataProvider
		) {
			$this->updateFulltextSearchIndex();
		}
		
	}
	
	protected function _getEshopData( ?EShop $eshop=null ) : Entity_WithEShopData_EShopData
	{
		if(!isset( $this->eshop_data[$eshop->getKey()])) {
			$this->checkShopData();
		}
		
		return $this->eshop_data[$eshop->getKey()];
	}
	
	abstract public function getEshopData( ?EShop $eshop = null ): Entity_WithEShopData_EShopData;
	
	public static function getEntityShopDataInstance() : Entity_WithEShopData_EShopData
	{
		$def = static::getDataModelDefinition();
		
		/**
		 * @var DataModel_Definition_Property_DataModel $prop
		 */
		$prop = $def->getProperty('eshop_data');
		
		$class = $prop->getValueDataModelClass();
		
		return new $class();
	}
	
	/**
	 * @return Timer_Action[]
	 */
	public function getAvailableTimerActions() : array
	{
		$actions = [];
		
		$activate = new class() extends Timer_Action {
			
			public function getAction(): string
			{
				return 'activate';
			}
			
			public function getTitle(): string
			{
				return Tr::_('Activate - master switch');
			}
			
			public function perform( Entity_Basic|Entity_HasActivation_Interface $entity, mixed $action_context ): bool
			{
				$entity->activate();
				return true;
			}
		};
		$actions[$activate->getAction()] = $activate;
		
		$deactivate = new class() extends Timer_Action {
			
			public function getAction(): string
			{
				return 'deactivate';
			}
			
			public function getTitle(): string
			{
				return Tr::_('Deactivate - master switch');
			}
			
			public function perform( Entity_Basic|Entity_HasActivation_Interface $entity, mixed $action_context ): bool
			{
				$entity->deactivate();
				return true;
			}
		};
		$actions[$deactivate->getAction()] = $deactivate;
		
		
		
		foreach(EShops::getListSorted() as $eshop) {
			$activate = new class( $eshop ) extends Timer_Action {
				protected EShop $eshop;
				public function __construct( EShop $eshop )
				{
					$this->eshop = $eshop;
				}
				
				public function getAction(): string
				{
					return 'activate:'.$this->eshop->getKey();
				}
				
				public function getTitle(): string
				{
					return Tr::_('Activate - %ESHOP%', ['ESHOP'=>$this->eshop->getName()]);
				}
				
				public function perform( Entity_Basic $entity, mixed $action_context ): bool
				{
					$entity->activateEShopData( $this->eshop );
					return true;
				}
			};
			$actions[$activate->getAction()] = $activate;
			
			$deactivate = new class($eshop) extends Timer_Action {
				protected EShop $eshop;
				public function __construct( EShop $eshop )
				{
					$this->eshop = $eshop;
				}
				
				public function getAction(): string
				{
					return 'deactivate:'.$this->eshop->getKey();
				}
				
				public function getTitle(): string
				{
					return Tr::_('Deactivate - %ESHOP%', ['ESHOP'=>$this->eshop->getName()]);
				}
				
				public function perform( Entity_Basic $entity, mixed $action_context ): bool
				{
					$entity->deactivateEShopData( $this->eshop );
					return true;
				}
			};
			
			$actions[$deactivate->getAction()] = $deactivate;
			
		}
		
		
		return $actions;
	}
	
}