<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockStatusOverview;


use Jet\Auth;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Auth_Administrator_Role;
use JetApplication\EShopEntity_Basic;
use JetApplication\Product;
use JetApplication\Admin_Managers_WarehouseManagement_Overview;
use JetApplication\WarehouseManagement_StockCard;


class Main extends Admin_Managers_WarehouseManagement_Overview
{
	public const ADMIN_MAIN_PAGE = 'stock-status-overview';
	
	public const ACTION_CHANGE_LOCATION = 'change_location';
	public const ACTION_CANCEL_OR_REACTIVATE = 'cancel_or_reactivate';
	public const ACTION_RECALCULATE = 'recalculate';
	
	public static function getCurrentUserCanChangeLocation() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_CHANGE_LOCATION );
	}
	
	public static function getCurrentUserCanCancelOrReactivate() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_CANCEL_OR_REACTIVATE );
	}
	
	public static function getCurrentUserCanRecalculate() : bool
	{
		return Auth::getCurrentUserHasPrivilege( Auth_Administrator_Role::PRIVILEGE_MODULE_ACTION, static::ACTION_RECALCULATE );
	}
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new WarehouseManagement_StockCard();
	}
	
	public function renderProductStockStatusInfo( Product $product ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('product', $product);
				
				return $view->render('product-stock-status-info');
			}
		);
	}
	
}