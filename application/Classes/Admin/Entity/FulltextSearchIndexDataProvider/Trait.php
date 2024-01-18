<?php
namespace JetApplication;

trait Admin_Entity_FulltextSearchIndexDataProvider_Trait {
	public function afterAdd(): void
	{
		Admin_Managers::FulltextSearch()->addIndex( $this );
	}
	
	public function afterUpdate(): void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function afterDelete(): void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
	public function getAdminFulltextObjectClass(): string
	{
		return static::getEntityType();
	}
	
	public function getAdminFulltextObjectId(): string
	{
		return $this->id;
	}
	
	public function getAdminFulltextObjectType(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getAdminFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getAdminFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
}