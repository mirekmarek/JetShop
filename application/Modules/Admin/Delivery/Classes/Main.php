<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Delivery\Classes;


use JetApplication\Application_Service_Admin_DeliveryClasses;
use JetApplication\Delivery_Class;
use JetApplication\EShopEntity_Basic;


class Main extends Application_Service_Admin_DeliveryClasses
{
	public const ADMIN_MAIN_PAGE = 'delivery-class';

	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Delivery_Class();
	}
}