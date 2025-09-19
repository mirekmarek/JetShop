<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\BannerGroups;


use JetApplication\Application_Service_Admin_Marketing_BannerGroups;
use JetApplication\EShopEntity_Basic;
use JetApplication\Marketing_BannerGroup;


class Main extends Application_Service_Admin_Marketing_BannerGroups
{
	public const ADMIN_MAIN_PAGE = 'banner-groups';

	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Marketing_BannerGroup();
	}
}