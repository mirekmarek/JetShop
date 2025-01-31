<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_Content_ArticleKindOfArticle;
use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'content_article_kind_of',
	database_table_name: 'content_articles_kind_of'
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_Content_ArticleKindOfArticle::class
)]
abstract class Core_Content_Article_KindOfArticle extends EShopEntity_Common implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;

}