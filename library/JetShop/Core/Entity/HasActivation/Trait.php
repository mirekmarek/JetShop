<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Logger;
use JetApplication\FulltextSearch_IndexDataProvider;

trait Core_Entity_HasActivation_Trait {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $is_active = false;
	
	
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
	
}