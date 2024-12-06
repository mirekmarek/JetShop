<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Form;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Order as Application_Order;


class Order extends Application_Order implements Admin_Entity_WithEShopRelation_Interface
{
	
	public function isEditable(): bool
	{
		if( !Main::getCurrentUserCanEdit() ) {
			return false;
		}
		
		return parent::isEditable();
	}
	
	
	public function setEditable( bool $editable ): void
	{
	}
	
	public function getEditURL(): string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function getAddForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchAddForm(): bool
	{
		return false;
	}
	
	public function getEditForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchEditForm(): bool
	{
		return false;
	}
	
}