<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Customers;

use Jet\Form;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Customer as Application_Customer;

class Customer extends Application_Customer implements Admin_Entity_WithShopRelation_Interface
{
	
	public function isEditable(): bool
	{
		return false;
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