<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;


use JetApplication\Admin_Managers_Content_Articles;
use JetApplication\EShopEntity_Basic;
use JetApplication\Content_Article;


class Main extends Admin_Managers_Content_Articles
{
	public const ADMIN_MAIN_PAGE = 'articles';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_Article();
	}
	
}