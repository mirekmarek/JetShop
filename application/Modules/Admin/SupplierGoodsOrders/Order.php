<?php
namespace JetApplicationModule\Admin\SupplierGoodsOrders;

use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Entity_Simple_Trait;
use JetApplication\Supplier_GoodsOrder;

#[DataModel_Definition]
class Order extends Supplier_GoodsOrder implements Admin_Entity_Simple_Interface
{
	use Admin_Entity_Simple_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	protected function setupForm( Form $form ) : void
	{
		foreach($this->items as $item) {
			$qty = new Form_Field_Float( '/order/'.$item->getProductId(), '' );
			$qty->setDefaultValue( $item->getUnitsOrdered() );
			$qty->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setUnitsOrdered( $v );
			} );
			$form->addField( $qty );
		}
	}
	
	public function getAdminTitle() : string
	{
		return $this->supplier_company_name . ' / '.$this->number;
	}
	
	protected function setupAddForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
		
		if(!in_array($this->status, [
			static::STATUS_PENDING,
			static::STATUS_PROBLEM_DURING_SENDING
		])) {
			$form->setIsReadonly();
		}
	}
	
	protected function catchForm( Form $form ) : bool
	{
		if(!$form->catch()) {
			return false;
		}
		
		$everything_zero = true;
		foreach($this->getItems() as $item) {
			if($item->getUnitsOrdered()>0) {
				$everything_zero = false;
				break;
			}
		}
		
		if($everything_zero) {
			$form->setCommonMessage(
				UI_messages::createDanger(Tr::_('Please specify at least one item to order'))
			);
			return false;
		}
		
		return true;
	}
	
	public function catchAddForm() : bool
	{
		return $this->catchForm( $this->getAddForm() );
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
	public function getSetSupplierOrderNumberForm() : Form
	{
		$number = new Form_Field_Input('number', 'Order number: ');
		$number->setDefaultValue( $this->getNumberBySupplier() );
		$number->setFieldValueCatcher( function( string $value ) {
			$this->setNumberBySupplier( $value );
		} );
		
		$form = new Form('set_supplier_order_number', [$number]);
		
		return $form;
	}
	
	
}