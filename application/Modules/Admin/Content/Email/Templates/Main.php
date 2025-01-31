<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Email\Templates;


use JetApplication\Admin_Managers_Content_EMailTemplates;
use JetApplication\EShopEntity_Basic;
use JetApplication\EMail_TemplateText;


class Main extends Admin_Managers_Content_EMailTemplates
{
	public const ADMIN_MAIN_PAGE = 'content-email-templates';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new EMail_TemplateText();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

	
}