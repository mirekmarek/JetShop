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
use JetApplication\Application_Service_Admin_Content_ArticleKindOfArticle;
use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'content_article_kind_of',
	database_table_name: 'content_articles_kind_of'
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Kind of article',
	admin_manager_interface: Application_Service_Admin_Content_ArticleKindOfArticle::class
)]
abstract class Core_Content_Article_KindOfArticle extends EShopEntity_Common implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	protected static ?array $internal_code_to_id_map = null;

	public static function getIdByInternalCode( string $internal_code ) : ?int
	{
		if( static::$internal_code_to_id_map===null ) {
			static::$internal_code_to_id_map = static::dataFetchPairs(
				select: [
					'internal_code',
					'id'
				],
				raw_mode: false
			);
		}
		
		return static::$internal_code_to_id_map[$internal_code] ?? null;
	}
}