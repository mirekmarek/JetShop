<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_Common;


#[DataModel_Definition(
	name: 'suppliers',
	database_table_name: 'suppliers'
)]
abstract class Core_Supplier extends Entity_Common {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $is_active = false;
	
	
	public function isActive() : bool
	{
		return $this->is_active;
	}
	
	public function setIsActive( bool $is_active ): void
	{
		$this->is_active = $is_active;
	}
}