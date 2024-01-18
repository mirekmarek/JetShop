<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Customers;

use Jet\Form;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Customer as Application_Customer;

class Customer extends Application_Customer implements Admin_Entity_Common_Interface
{
	
	public function getAdminFulltextObjectClass(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectId(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectType(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectIsActive(): bool
	{
		return false;
	}
	
	public function getAdminFulltextObjectTitle(): string
	{
		return '';
	}
	
	public function getAdminFulltextTexts(): array
	{
		return [];
	}
	
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
	
	public function getAdminTitle(): string
	{
		return $this->getName();
	}
	
	public function isItPossibleToDelete(): bool
	{
		return false;
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