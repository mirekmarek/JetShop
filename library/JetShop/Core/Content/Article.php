<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Application_Service_Admin;
use JetApplication\Application_Service_Admin_Content_Articles;
use JetApplication\Application_Service_EShop;
use JetApplication\Content_Article_Author;
use JetApplication\Content_Article_Category;
use JetApplication\Content_Article_KindOfArticle;
use JetApplication\Content_Article_EShopData;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\EShopEntity_Definition;
use JetApplication\FulltextSearch_IndexDataProvider;


#[DataModel_Definition(
	name: 'content_article',
	database_table_name: 'content_articles',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Article',
	admin_manager_interface: Application_Service_Admin_Content_Articles::class,
	images: [
		'header_1' => 'Header 1',
		'header_2' => 'Header 2',
	],
	separate_tab_form_shop_data: true
)]
abstract class Core_Content_Article extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	EShopEntity_Admin_WithEShopData_Interface,
	FulltextSearch_IndexDataProvider
{
	use EShopEntity_WithEShopData_HasImages_Trait;
	use EShopEntity_Admin_WithEShopData_Trait;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Priority:'
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Kind of article:',
		is_required: true,
		select_options_creator: [
			Content_Article_KindOfArticle::class,
			'getScope'
		],
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY         => 'Invalid value',
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
		
	)]
	protected int $kind_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Author of article:',
		is_required: true,
		select_options_creator: [
			Content_Article_Author::class,
			'getScope'
		],
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY         => 'Invalid value',
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]
	
	)]
	protected int $author_id = 0;
	
	
	protected ?array $category_ids = null;
	
	
	public function afterAdd(): void
	{
		foreach( EShops::getList() as $eshop ) {
			$eshop_key = $eshop->getKey();
			
			$this->eshop_data[$eshop_key]->generateURLPathPart();
			$this->eshop_data[$eshop_key]->save();
		}
		
		parent::afterAdd();
	}
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}

	public function setPriority( int $priority ): void
	{
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setPriority( $priority );
		}
		$this->priority = $priority;
	}

	public function getKindId(): int
	{
		return $this->kind_id;
	}
	
	public function setKindId( int $kind_id ): void
	{
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setKindId( $kind_id );
		}
		$this->kind_id = $kind_id;
	}

	public function getAuthorId(): int
	{
		return $this->author_id;
	}
	
	public function setAuthorId( int $author_id ): void
	{
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setAuthorId( $author_id );
		}
		$this->author_id = $author_id;
	}

	public function getCategoryIds(): array
	{
		if($this->category_ids===null) {
			$this->category_ids = Content_Article_Category::dataFetchCol(
				select: ['category_id'],
				where: ['article_id'=>$this->getId()],
				order_by: ['priority']
			);
		}
		
		return $this->category_ids;
	}
	
	public function setCategoryIds( array $category_ids ): void
	{
		Content_Article_Category::dataDelete([
			'article_id'=>$this->getId()
		]);
		
		$p = 0;
		foreach( $category_ids as $c_id ) {
			$ca = new Content_Article_Category();
			$ca->setCategoryId( $c_id );
			$ca->setArticleId( $this->getId() );
			$ca->setPriority( $p );
			$ca->save();
			
			$p++;
		}
		
		$this->category_ids = $category_ids;
	}
	
	
	public function sortCategories( array $category_ids ): void
	{
		if(array_diff($category_ids, $this->getCategoryIds())) {
			return;
		}
		
		Content_Article_Category::dataDelete([
			'article_id'=>$this->getId()
		]);
		
		$p = 0;
		foreach( $category_ids as $c_id ) {
			$ca = new Content_Article_Category();
			$ca->setCategoryId( $c_id );
			$ca->setArticleId( $this->getId() );
			$ca->setPriority( $p );
			$ca->save();
			
			$p++;
		}
		
		$this->category_ids = $category_ids;
	}
	
	
	public function addCategory( int $category_id ) : void
	{
		$category_ids = $this->getCategoryIds();
		if(in_array($category_id, $category_ids)) {
			return;
		}
		
		$ca = new Content_Article_Category();
		$ca->setCategoryId( $category_id );
		$ca->setArticleId( $this->getId() );
		$ca->setPriority( count($this->category_ids)+1 );
		$ca->save();
		
	}
	
	
	public function removeCategory( int $category_id ) : void
	{
		$category_ids = $this->getCategoryIds();
		if(!in_array($category_id, $category_ids)) {
			return;
		}
		
		Content_Article_Category::dataDelete([
			'article_id'=>$this->getId()
		]);
		
		$p = 0;
		foreach( $category_ids as $c_id ) {
			if($c_id==$category_id) {
				continue;
			}
			
			$ca = new Content_Article_Category();
			$ca->setCategoryId( $c_id );
			$ca->setArticleId( $this->getId() );
			$ca->setPriority( $p );
			$ca->save();
			
			$p++;
		}
		
		$this->category_ids = null;
	}
	
	
	/**
	 * @var Content_Article_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_Article_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function getEshopData( ?EShop $eshop = null ): Content_Article_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	
	
	public function getFulltextObjectType(): string
	{
		return '';
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getShopFulltextTexts( EShop $eshop ): array
	{
		$sd = $this->getEshopData( $eshop );
		
		return [$sd->getTitle()];
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Application_Service_Admin::FulltextSearch()->updateIndex( $this );
		Application_Service_EShop::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Application_Service_Admin::FulltextSearch()->deleteIndex( $this );
		Application_Service_EShop::FulltextSearch()->deleteIndex( $this );
	}
	
}