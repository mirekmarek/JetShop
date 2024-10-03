<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;

use Jet\Form;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Entity_Simple_Trait;
use JetApplication\WarehouseManagement_LossOrDestruction;

class LossOrDestruction extends WarehouseManagement_LossOrDestruction implements Admin_Entity_Simple_Interface
{
	use Admin_Entity_Simple_Trait;
	
	protected bool $editable;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function getAdminTitle() : string
	{
		return $this->number;
	}
	
	public static function get( int $id ): static|null
	{
		return static::load( $id );
	}
	
	public function isEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = true;
	}
	
	protected function setupForm( Form $form ) : void
	{
		$form->field('product_id')->setFieldValueCatcher( function( string $id ) {
			$this->setProduct( (int)$id );
		} );
	}
	
	protected function catchForm( Form $form ) : bool
	{
		if(!$form->catch()) {
			
			return false;
		}
		
		return true;
	}
	
	
	
	public function setupAddForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	
	
	public function catchAddForm() : bool
	{
		return $this->catchForm( $this->getAddForm() );
	}
	
	public function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
		
		if($this->getStatus()!=static::STATUS_PENDING) {
			$form->setIsReadonly();
		}
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
}