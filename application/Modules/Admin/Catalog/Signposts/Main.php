<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Signposts;


use JetApplication\EShopEntity_Basic;
use JetApplication\Signpost;
use JetApplication\Application_Service_Admin_Signpost;


class Main extends Application_Service_Admin_Signpost
{
	public const ADMIN_MAIN_PAGE = 'signposts';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Signpost();
	}
	
}