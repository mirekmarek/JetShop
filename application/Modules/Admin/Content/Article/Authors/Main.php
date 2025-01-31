<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Authors;


use JetApplication\Admin_Managers_Content_ArticleAuthors;
use JetApplication\Content_Article_Author;
use JetApplication\EShopEntity_Basic;


class Main extends Admin_Managers_Content_ArticleAuthors
{
	public const ADMIN_MAIN_PAGE = 'article-authors';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_Article_Author();
	}
}