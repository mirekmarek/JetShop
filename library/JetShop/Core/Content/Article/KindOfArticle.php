<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Admin_Managers_ContentArticleKindOfArticle;
use JetApplication\Entity_Common;
use JetApplication\JetShopEntity_Definition;


#[DataModel_Definition(
	name: 'content_article_kind_of',
	database_table_name: 'content_articles_kind_of'
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_ContentArticleKindOfArticle::class
)]
abstract class Core_Content_Article_KindOfArticle extends Entity_Common implements Admin_Entity_Common_Interface
{
	use Admin_Entity_Common_Trait;

}