<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\ReturnsOfGoods;

use Jet\Form;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\ReturnOfGoods as Application_ReturnOfGoods;

class ReturnOfGoods extends Application_ReturnOfGoods implements Admin_Entity_WithShopRelation_Interface
{
	
	public function isEditable(): bool
	{
		if(!Main::getCurrentUserCanEdit()) {
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