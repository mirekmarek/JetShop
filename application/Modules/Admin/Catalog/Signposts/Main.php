<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\Signposts;


use JetApplication\EShopEntity_Basic;
use JetApplication\Signpost;
use JetApplication\Admin_Managers_Signpost;


class Main extends Admin_Managers_Signpost
{
	public const ADMIN_MAIN_PAGE = 'signposts';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Signpost();
	}
	
}