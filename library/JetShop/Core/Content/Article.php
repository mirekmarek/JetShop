<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Content_Article_Author;
use JetApplication\Content_Article_Category;
use JetApplication\Content_Article_KindOfArticle;
use JetApplication\Content_Article_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;



#[DataModel_Definition(
	name: 'content_article',
	database_table_name: 'content_articles',
)]
abstract class Core_Content_Article extends Entity_WithShopData
{
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
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
			
			$this->shop_data[$shop_key]->generateURLPathPart();
			$this->shop_data[$shop_key]->save();
		}
		
		parent::afterAdd();
	}
	
	
	public function getPriority(): int
	{
		return $this->priority;
	}

	public function setPriority( int $priority ): void
	{
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setPriority( $priority );
		}
		$this->priority = $priority;
	}

	public function getKindId(): int
	{
		return $this->kind_id;
	}
	
	public function setKindId( int $kind_id ): void
	{
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setKindId( $kind_id );
		}
		$this->kind_id = $kind_id;
	}

	public function getAuthorId(): int
	{
		return $this->author_id;
	}
	
	public function setAuthorId( int $author_id ): void
	{
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setAuthorId( $author_id );
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
	 * @var Content_Article_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_Article_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	
	public function getShopData( ?Shops_Shop $shop = null ): Content_Article_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
	
}