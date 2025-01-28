<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Entity_Admin_Interface;
use JetApplication\Entity_Admin_Trait;
use JetApplication\Admin_Managers_ContentArticleKindOfArticle;
use JetApplication\Entity_Common;
use JetApplication\Entity_Definition;


#[DataModel_Definition(
	name: 'content_article_kind_of',
	database_table_name: 'content_articles_kind_of'
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_ContentArticleKindOfArticle::class
)]
abstract class Core_Content_Article_KindOfArticle extends Entity_Common implements Entity_Admin_Interface
{
	use Entity_Admin_Trait;

}