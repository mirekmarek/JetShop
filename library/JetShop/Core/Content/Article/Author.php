<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Content_Article_Author_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;



#[DataModel_Definition(
	name: 'content_article_author',
	database_table_name: 'content_articles_authors',
)]
abstract class Core_Content_Article_Author extends Entity_WithEShopData
{
	
	/**
	 * @var Content_Article_Author_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_Article_Author_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function getEshopData( ?EShop $eshop = null ): Content_Article_Author_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
}