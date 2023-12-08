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
	
}