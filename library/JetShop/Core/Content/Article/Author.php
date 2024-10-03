<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Content_Article_Author_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops_Shop;



#[DataModel_Definition(
	name: 'content_article_author',
	database_table_name: 'content_articles_authors',
)]
abstract class Core_Content_Article_Author extends Entity_WithShopData
{
	
	/**
	 * @var Content_Article_Author_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_Article_Author_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	
	public function getShopData( ?Shops_Shop $shop = null ): Content_Article_Author_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
	
}