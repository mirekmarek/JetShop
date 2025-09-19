<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Application_Service_Admin_Content_ArticleAuthors;
use JetApplication\Content_Article_Author_EShopData;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\EShop;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'content_article_author',
	database_table_name: 'content_articles_authors',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Article author',
	admin_manager_interface: Application_Service_Admin_Content_ArticleAuthors::class,
	images: [
		'avatar_1' => 'Avatar 1',
		'avatar_2' => 'Avatar 2',
	],
	description_mode: true,
	separate_tab_form_shop_data: true
)]
abstract class Core_Content_Article_Author extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_WithEShopData_HasImages_Trait;
	use EShopEntity_Admin_WithEShopData_Trait;
	
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