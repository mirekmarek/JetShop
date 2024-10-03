<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Http_Request;
use JetApplication\Content_Article_Author_ShopData;
use JetApplication\Content_Article_Category;
use JetApplication\Content_Article_KindOfArticle;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Content_Article;
use JetApplication\Shops_Shop;


#[DataModel_Definition(
	name: 'content_article_shop_data',
	database_table_name: 'content_articles_shop_data',
	parent_model_class: Content_Article::class
)]
abstract class Core_Content_Article_ShopData extends Entity_WithShopData_ShopData
{
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $kind_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $author_id = 0;
	
	protected ?array $category_ids = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	protected string $title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'SEO Title:'
	)]
	protected string $seo_title = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Perex:'
	)]
	protected string $perex = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Text:'
	)]
	protected string $text = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 512,
	)]
	protected string $URL_path_part = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_header_1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_header_2 = '';
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		$this->generateURLPathPart();
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}

	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}

	public function getKindId(): int
	{
		return $this->kind_id;
	}

	public function setKindId( int $kind_id ): void
	{
		$this->kind_id = $kind_id;
	}

	public function getAuthorId(): int
	{
		return $this->author_id;
	}

	public function setAuthorId( int $author_id ): void
	{
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
	
	
	public function getTitle(): string
	{
		return $this->title;
	}
	
	public function setTitle( string $title ): void
	{
		$this->title = $title;
		$this->generateURLPathPart();
	}
	
	public function getSeoTitle(): string
	{
		return $this->seo_title;
	}

	public function setSeoTitle( string $seo_title ): void
	{
		$this->seo_title = $seo_title;
	}

	public function getPerex(): string
	{
		return $this->perex;
	}
	
	public function setPerex( string $perex ): void
	{
		$this->perex = $perex;
	}

	public function setText( string $value ) : void
	{
		$this->text = $value;
	}
	
	public function getText() : string
	{
		return $this->text;
	}
	
	
	public function setImageHeader1( string $image ) : void
	{
		$this->image_header_1 = $image;
	}
	
	public function getImageHeader1() : string
	{
		return $this->image_header_1;
	}
	
	public function getImageHeader1ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('header_1', $max_w, $max_h);
	}
	
	public function getImageHeader1Url(): string
	{
		return $this->getImageUrl('header_1');
	}
	
	
	public function setImageHeader2( string $image ) : void
	{
		$this->image_header_2 = $image;
	}
	
	public function getImageHeader2() : string
	{
		return $this->image_header_2;
	}
	
	public function getImageHeader2ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl('header_2', $max_w, $max_h);
	}
	
	public function getImageHeader2Url(): string
	{
		return $this->getImageUrl('header_2');
	}
	
	public function getURLPathPart() : string
	{
		return $this->URL_path_part;
	}
	
	public function setURLPathPart( string $URL_path_part ) : void
	{
		$this->URL_path_part = $URL_path_part;
	}
	
	public function getURL() : string
	{
		return $this->getShop()->getURL( [$this->URL_path_part] );
	}
	
	public function generateURLPathPart() : void
	{
		if(!$this->entity_id) {
			return;
		}
		
		$this->URL_path_part = $this->_generateURLPathPart( $this->getTitle(), 'a' );
		
		$where = $this->getShop()->getWhere();
		$where[] = 'AND';
		$where['entity_id'] = $this->entity_id;
		
		static::updateData(
			['URL_path_part'=>$this->URL_path_part],
			$where
		);
		
	}
	
	public function getPreviewURL() : string
	{
		return $this->getShop()->getURL( [$this->getURLPathPart()], GET_params: ['pvk'=>$this->generatePreviewKey()] );
	}
	
	public function generatePreviewKey() : string
	{
		return sha1(
			$this->entity_id.'|'.$this->title.'|'.$this->text
		);
	}
	
	public static function getByURLPathPart( ?string $URL_path, ?Shops_Shop $shop=null ) : ?static
	{
		
		if(!preg_match('/-a-([0-9]+)$/', $URL_path, $res)) {
			return null;
		}
		
		$id = (int)$res[1];
		
		$article = static::get( $id, $shop );
		
		return $article;
	}
	
	public function checkPreviewKey() : bool
	{
		return Http_Request::GET()->getString('pvk')==$this->generatePreviewKey();
	}

	public function getAuthor() : ?Content_Article_Author_ShopData
	{
		if($this->author_id) {
			return Content_Article_Author_ShopData::get( $this->author_id, $this->getShop() );
		}
		
		return null;
	}
	
	/**
	 * @param Shops_Shop|null $shop
	 * @param string|null $kind_code
	 * @param int|null $author_id
	 * @param array|null $category_ids
	 *
	 * @return static[]
	 */
	public static function getArticleList(
		?Shops_Shop $shop=null,
		?string $kind_code=null,
		?int $author_id=null,
		?array $category_ids = null
	) : array
	{
		$where = static::getActiveQueryWhere( $shop );
		
		if($kind_code!=null) {
			$kind_id = (int)Content_Article_KindOfArticle::dataFetchOne(select: ['id'], where: ['internal_code'=>$kind_code]);
			if(!$kind_id) {
				return [];
			}
			$where[] = 'AND';
			$where['kind_id'] = $kind_id;
		}
		
		if($author_id!=null) {
			$where[] = 'AND';
			$where['author_id'] = $author_id;
		}
		if($category_ids!=null) {
			$ids = Content_Article_Category::dataFetchCol(
				select: ['article_id'],
				where: [
					'category_id' => $category_ids
				]);
			
			if(!$ids) {
				return [];
			}
			
			$where[] = 'AND';
			$where['entity_id'] = $ids;
		}
		
		return static::fetch(['this'=>$where], order_by: ['priority']);
	}
}