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

namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Form;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_Managers;
use JetApplication\OrderDispatch as Application_OrderDispatch;

class OrderDispatch extends Application_OrderDispatch implements Admin_Entity_WithShopRelation_Interface
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
		return Admin_Managers::OrderDispatch()->getOrderDispatchURL( $this->id );
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
	
	public static function get( int $id ): static|null
	{
		return static::load( $id );
	}
}