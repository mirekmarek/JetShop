<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\InfoBoxes;


use JetApplication\Application_Service_Admin_Content_InfoBoxes;
use JetApplication\Content_InfoBox;
use JetApplication\EShopEntity_Basic;


class Main extends Application_Service_Admin_Content_InfoBoxes
{
	public const ADMIN_MAIN_PAGE = 'content-info-box';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_InfoBox();
	}

}