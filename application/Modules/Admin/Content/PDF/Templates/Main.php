<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\PDF\Templates;


use JetApplication\Application_Service_Admin_Content_PDFTemplates;
use JetApplication\EShopEntity_Basic;
use JetApplication\PDF_TemplateText;


class Main extends Application_Service_Admin_Content_PDFTemplates
{
	public const ADMIN_MAIN_PAGE = 'content-pdf-templates';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new PDF_TemplateText();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}

	
}