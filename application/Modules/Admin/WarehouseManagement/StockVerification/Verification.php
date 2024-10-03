<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Entity_Simple_Trait;
use JetApplication\WarehouseManagement_StockVerification;

class Verification extends WarehouseManagement_StockVerification implements Admin_Entity_Simple_Interface
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
		foreach($this->items as $p_id=>$item) {
			$qty_reality = new Form_Field_Float( '/item_'.$p_id.'/qty_reality', '' );
			$qty_reality->setDefaultValue( $item->getNumberOfUnitsReality() );
			$qty_reality->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setNumberOfUnitsReality( $v );
			} );
			$form->addField( $qty_reality );
			
			
			$sector = new Form_Field_Input( '/item_'.$p_id.'/sector', '' );
			$sector->setDefaultValue( $item->getSector() );
			$sector->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setSector( $v );
			} );
			$form->addField( $sector );
			
			
			$rack = new Form_Field_Input( '/item_'.$p_id.'/rack', '' );
			$rack->setDefaultValue( $item->getRack() );
			$rack->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setRack( $v );
			} );
			$form->addField( $rack );
			
			
			$position = new Form_Field_Input( '/item_'.$p_id.'/position', '' );
			$position->setDefaultValue( $item->getPosition() );
			$position->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setPosition( $v );
			} );
			$form->addField( $position );
		}
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