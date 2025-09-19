<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\PromoAreaDefinition;


use JetApplication\Application_Service_Admin_Marketing_PromoAreaDefinitions;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_PromoAreaDefinition;


class Main extends Application_Service_Admin_Marketing_PromoAreaDefinitions
{

	public const ADMIN_MAIN_PAGE = 'promo-areas-definition';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_PromoAreaDefinition();
	}

}