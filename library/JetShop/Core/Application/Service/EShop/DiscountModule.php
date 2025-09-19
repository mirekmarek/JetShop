<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;

use JetApplication\Application_Service_EShop;
use JetApplication\CashDesk;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use Jet\Application_Service_MetaInfo;
use JetApplication\Order;
use JetApplication\Order_Item;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: false,
	multiple_mode: true,
	name: 'Discount module',
	description: '',
	module_name_prefix: 'Discounts.',
)]
abstract class Core_Application_Service_EShop_DiscountModule extends Application_Module implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected CashDesk $cash_desk;
	
	public function setCashDesk( CashDesk $cash_desk ): void
	{
		$this->cash_desk = $cash_desk;
	}
	
	abstract public function handleShoppingCart( CashDesk $cash_desk ) : string;

	abstract public function generateDiscounts( CashDesk $cash_desk ) : void;
	
	abstract public function checkDiscounts( CashDesk $cash_desk ) : void;
	

	abstract public function Order_newOrderCreated( Order $order ) : void;

	abstract public function Order_canceled( Order $order ) : void;

	abstract public function Order_itemRemoved( Order $order, Order_Item $item ) : void;

	abstract public function Order_itemAdded( Order $order, Order_Item $item ) : void;

	abstract public function Order_reactivated( Order $order, Order_Item $item ) : void;

}