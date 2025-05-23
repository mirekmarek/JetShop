<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Availability;
use JetApplication\DeliveryTerm_Info;
use JetApplication\DeliveryTerm_Manager;
use JetApplication\Managers_General;
use JetApplication\Order;
use JetApplication\Product_EShopData;

abstract class Core_DeliveryTerm {
	public const SITUATION_IN_STOCK = 'in_stock';
	public const SITUATION_GOOD = 'good';
	public const SITUATION_SO_SO = 'so_so';
	public const SITUATION_BAD = 'bad';
	public const SITUATION_TERRIBLE = 'terrible';
	public const SITUATION_NOT_AVAILABLE = 'not_available';

	
	
	public static function getManager() : DeliveryTerm_Manager|Application_Module
	{
		return Managers_General::DeliveryTerm();
	}
	
	public static function getInfo( Product_EShopData $product, ?Availability $availability=null ) : DeliveryTerm_Info
	{
		return static::getManager()->getInfo( $product, $availability );
	}
	
	public static function setupOrder( Order $order ) : void
	{
		static::getManager()->setupOrder( $order );
	}
	
}