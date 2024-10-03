<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Entity_Simple_Trait;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use Jet\Form_Field_Input;

class Transfer extends WarehouseManagement_TransferBetweenWarehouses implements Admin_Entity_Simple_Interface
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
		$source_wh = $this->getSourceWarehouse();

		foreach($this->items as $p_id=>$item) {
			$qty = new Form_Field_Float( '/item_'.$p_id.'/qty', '' );
			$qty->setMaxValue( $source_wh->getCard( $item->getProductId() )->getInStock() );
			$qty->setDefaultValue( $item->getNumberOfUnits() );
			$qty->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setNumberOfUnits( $v );
			} );
			$form->addField( $qty );
			
		}

	}
	
	protected function catchForm( Form $form ) : bool
	{
		if(!$form->catch()) {
			return false;
		}
		
		$everything_zero = true;
		foreach($this->getItems() as $item) {
			if($item->getNumberOfUnits()>0) {
				$everything_zero = false;
				break;
			}
		}
		
		if($everything_zero) {
			$form->setCommonMessage(
				UI_messages::createDanger(Tr::_('Please specify at least one item'))
			);
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
		
		foreach($this->items as $p_id=>$item) {
			if( $this->getStatus()==static::STATUS_SENT ) {
				$form->field('/item_'.$p_id.'/qty')->setIsReadonly(true);
			}
			
			$sector = new Form_Field_Input( '/item_'.$p_id.'/sector', '' );
			$sector->setDefaultValue( $item->getTargetSector() );
			$sector->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setTargetSector( $v );
			} );
			$form->addField( $sector );
			
			
			$rack = new Form_Field_Input( '/item_'.$p_id.'/rack', '' );
			$rack->setDefaultValue( $item->getTargetRack() );
			$rack->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setTargetRack( $v );
			} );
			$form->addField( $rack );
			
			$position = new Form_Field_Input( '/item_'.$p_id.'/position', '' );
			$position->setDefaultValue( $item->getTargetPosition() );
			$position->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setTargetPosition( $v );
			} );
			$form->addField( $position );

		}
		
		
		if(
			$this->getStatus()==static::STATUS_RECEIVED ||
			$this->getStatus()==static::STATUS_CANCELLED
		) {
			$form->setIsReadonly();
		}
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
}