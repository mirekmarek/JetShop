<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\AccessoriesGroups;

use JetApplication\Accessories_Group;
use JetApplication\Admin_Managers_AccessoriesGroups;
use JetApplication\EShopEntity_Basic;

class Main extends Admin_Managers_AccessoriesGroups
{
	public const ADMIN_MAIN_PAGE = 'accesories-groups';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Accessories_Group();
	}
	
}