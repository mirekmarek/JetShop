<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Entity_Common;


#[DataModel_Definition(
	name: 'content_article_kind_of',
	database_table_name: 'content_articles_kind_of'
)]
abstract class Core_Content_Article_KindOfArticle extends Entity_Common {

}