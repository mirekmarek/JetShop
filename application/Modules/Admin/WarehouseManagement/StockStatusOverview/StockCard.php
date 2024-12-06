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

namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;

use Jet\Form;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Product;
use JetApplication\WarehouseManagement_StockCard;

class StockCard extends WarehouseManagement_StockCard implements Admin_Entity_WithEShopRelation_Interface
{
	public function getAdminTitle() : string
	{
		$warehouse = $this->getWarehouse();
		$product = Product::get( $this->product_id );
		
		return $warehouse->getInternalName() .' / '.$product->getAdminTitle();
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