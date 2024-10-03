<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Entity_Simple_Trait;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

class ReceiptOfGoods extends WarehouseManagement_ReceiptOfGoods implements Admin_Entity_Simple_Interface
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
			$qty = new Form_Field_Float( '/item_'.$p_id.'/qty', '' );
			$qty->setDefaultValue( $item->getUnitsReceived() );
			$qty->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setUnitsReceived( $v );
			} );
			$form->addField( $qty );
			
			$ppu = new Form_Field_Float( '/item_'.$p_id.'/price_per_unit', '' );
			$ppu->setDefaultValue( $item->getPricePerUnit() );
			$ppu->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setPricePerUnit( $v );
			} );
			$form->addField( $ppu );
			
			$total_price = new Form_Field_Float( '/item_'.$p_id.'/total_price', '' );
			$total_price->setDefaultValue( $item->getTotalPrice() );
			$total_price->setFieldValueCatcher( function( float $v ) use ($item) : void {
			} );
			$form->addField( $total_price );
			
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
		
		$everything_zero = true;
		foreach($this->getItems() as $item) {
			if($item->getUnitsReceived()>0) {
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
		
		$order_number = new Form_Field_Input('order_number', 'Order number:');
		$order_number->setDefaultValue( $this->order_number );
		$order_number->setErrorMessages([
			'unknown_order' => 'Unknown order'
		]);
		$order_number->setValidator( function() use ($order_number) {
			$number = $order_number->getValue();
			if(!$number) {
				return true;
			}
			
			$order = Supplier_GoodsOrder::getByNumber( $number );
			if( !$order ) {
				$order_number->setError('unknown_order');
				return false;
			}
			
			return true;
		} );
		$order_number->setFieldValueCatcher( function( string $value ) : void {
			if($value) {
				$order = Supplier_GoodsOrder::getByNumber( $value );
				
				$this->order_id = $order->getId();
				$this->order_number = $order->getNumber();
			} else {
				$this->order_id = 0;
				$this->order_number = '';
			}
		} );
		$form->addField($order_number);
		
		if($this->getStatus()!=static::STATUS_PENDING) {
			$form->setIsReadonly();
		}
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
}