<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Complaints;


/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Complaints;

use Jet\Form;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Complaint as Application_Complaint;

class Complaint extends Application_Complaint implements Admin_Entity_WithShopRelation_Interface
{
	
	public function isEditable(): bool
	{
		if(!Main::getCurrentUserCanEdit()) {
			return false;
		}
		
		return parent::isEditable();
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