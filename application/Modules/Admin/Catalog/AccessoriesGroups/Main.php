<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\AccessoriesGroups;

use JetApplication\Accessories_Group;
use JetApplication\Application_Service_Admin_AccessoriesGroups;
use JetApplication\EShopEntity_Basic;

class Main extends Application_Service_Admin_AccessoriesGroups
{
	public const ADMIN_MAIN_PAGE = 'accesories-groups';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Accessories_Group();
	}
	
}