<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Admin_Managers_ContentArticleAuthors;
use JetApplication\Content_Article_Author_EShopData;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Entity_Admin_WithEShopData_Trait;
use JetApplication\Entity_HasImages_Interface;
use JetApplication\Entity_WithEShopData;
use JetApplication\Entity_WithEShopData_HasImages_Trait;
use JetApplication\EShop;
use JetApplication\Entity_Definition;


#[DataModel_Definition(
	name: 'content_article_author',
	database_table_name: 'content_articles_authors',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_ContentArticleAuthors::class,
	images: [
		'avatar_1' => 'Avatar 1',
		'avatar_2' => 'Avatar 2',
	],
	separate_tab_form_shop_data: true
)]
abstract class Core_Content_Article_Author extends Entity_WithEShopData implements
	Entity_HasImages_Interface,
	Entity_Admin_WithEShopData_Interface
{
	use Entity_WithEShopData_HasImages_Trait;
	use Entity_Admin_WithEShopData_Trait;
	
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