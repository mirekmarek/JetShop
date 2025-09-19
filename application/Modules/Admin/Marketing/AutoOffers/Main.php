<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\AutoOffers;


use JetApplication\Application_Service_Admin_Marketing_AutoOffers;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_AutoOffer;


class Main extends Application_Service_Admin_Marketing_AutoOffers
{
	public const ADMIN_MAIN_PAGE = 'auto_offers';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_AutoOffer();
	}
}