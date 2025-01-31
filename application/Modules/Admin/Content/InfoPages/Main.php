<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Content\InfoPages;


use JetApplication\Admin_Managers_Content_InfoPages;
use JetApplication\Content_InfoPage;
use JetApplication\EShopEntity_Basic;


class Main extends Admin_Managers_Content_InfoPages
{
	public const ADMIN_MAIN_PAGE = 'content-info-page';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_InfoPage();
	}

}