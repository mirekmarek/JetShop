<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\PropertyGroups;

use JetApplication\EShopEntity_Basic;
use JetApplication\PropertyGroup;
use JetApplication\Application_Service_Admin_PropertyGroup;


class Main extends Application_Service_Admin_PropertyGroup
{
	public const ADMIN_MAIN_PAGE = 'property-group';

	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new PropertyGroup();
	}
}