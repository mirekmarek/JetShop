<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\InvoicesInAdvance;

use Jet\Form;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\InvoiceInAdvance as Application_Invoice;


class Invoice extends Application_Invoice implements Admin_Entity_WithEShopRelation_Interface
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
		return new Form( '', [] );
	}
	
	public function catchAddForm(): bool
	{
		return false;
	}
	
	public function getEditForm(): Form
	{
		return new Form( '', [] );
	}
	
	public function catchEditForm(): bool
	{
		return false;
	}
	
}