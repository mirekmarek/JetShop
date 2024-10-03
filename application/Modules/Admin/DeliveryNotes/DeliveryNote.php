<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\DeliveryNotes;

use Jet\Form;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\DeliveryNote as Application_DeliveryNote;


class DeliveryNote extends Application_DeliveryNote implements Admin_Entity_WithShopRelation_Interface
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