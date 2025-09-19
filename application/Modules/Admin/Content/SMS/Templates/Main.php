<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\SMS\Templates;

use JetApplication\Application_Service_Admin_Content_SMSTemplates;
use JetApplication\EShopEntity_Basic;
use JetApplication\SMS_TemplateText;


class Main extends Application_Service_Admin_Content_SMSTemplates
{
	public const ADMIN_MAIN_PAGE = 'content-sms-templates';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new SMS_TemplateText();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

	
}