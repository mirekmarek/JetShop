<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Email\Layouts;


use JetApplication\Application_Service_Admin_Content_EMailLayouts;
use JetApplication\EMail_Layout;
use JetApplication\EShopEntity_Basic;


class Main extends Application_Service_Admin_Content_EMailLayouts
{
	public const ADMIN_MAIN_PAGE = 'content-email-layouts';

	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new EMail_Layout();
	}

}