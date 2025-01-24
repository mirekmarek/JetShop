<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Admin_Managers_ContentArticleAuthors;
use JetApplication\Content_Article_Author_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;
use JetApplication\JetShopEntity_Definition;


#[DataModel_Definition(
	name: 'content_article_author',
	database_table_name: 'content_articles_authors',
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_ContentArticleAuthors::class
)]
abstract class Core_Content_Article_Author extends Entity_WithEShopData implements Admin_Entity_WithEShopData_Interface
{
	use Admin_Entity_WithEShopData_Trait;
	
	/**
	 * @var Content_Article_Author_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_Article_Author_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'avatar_1',
			image_title:  Tr::_('Avatar 1'),
		);
		$this->defineImage(
			image_class:  'avatar_2',
			image_title:  Tr::_('Avatar 2'),
		);
	}
	
	public function getEshopData( ?EShop $eshop = null ): Content_Article_Author_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
}