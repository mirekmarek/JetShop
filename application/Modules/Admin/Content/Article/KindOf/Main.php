<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\KindOf;


use JetApplication\Admin_Managers_Content_ArticleKindOfArticle;
use JetApplication\Content_Article_KindOfArticle;
use JetApplication\EShopEntity_Basic;


class Main extends Admin_Managers_Content_ArticleKindOfArticle
{
	public const ADMIN_MAIN_PAGE = 'kind-of-article';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Content_Article_KindOfArticle();
	}
}