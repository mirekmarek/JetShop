<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Exports\GoogleShopping;

use JetApplication\Exports_CategoriesCache;
use JetApplication\Exports_FileGenerator_XML;
use JetApplication\Exports_Module;
use JetApplication\Product;
use JetApplication\Product_ShopData;
use JetApplication\Shops_Shop;
use JetApplicationModule\Exports\Heureka\HeurekaCategory;


/**
 *
 */
class Main extends Exports_Module
{
	protected ?array $export_categories = null;

	public function getTitle(): string
	{
		return 'Google Shopping';
	}

	public function isAllowedForShop( Shops_Shop $shop ): bool
	{
		return true;
	}

	public function joinCategoriesAllowed(): bool
	{
		return true;
	}

	public function joinProductsAllowed(): bool
	{
		return true;
	}

	public function __getExportCategories( Shops_Shop $shop ) : array
	{
		$export_categories = [];

		$data = file( Config::getCategoriesURL($shop) );
		/** @noinspection PhpAutovivificationOnFalseValuesInspection */
		unset( $data[0]);

		$map = [];

		foreach( $data as $v ) {

			$v = trim($v);

			$id = strstr($v, ' - ', true);
			$full_name = substr( $v, strlen($id)+3 );

			$full_name_a = explode(' > ',$full_name);

			$map[$full_name] = $id;

			if(count($full_name_a)>1) {
				$last_i = count($full_name_a)-1;

				$name = $full_name_a[$last_i];
				unset($full_name_a[$last_i]);

				$parent_full_name = implode(' > ', $full_name_a);

				$parent_id = $map[$parent_full_name];
				$parent = $export_categories[$parent_id];

			} else {
				$parent = null;
				$name = $full_name;
			}

			$new_item = new GoogleCategory();
			$new_item->setId( $id );
			$new_item->setName( $name );
			$new_item->setFullName( $full_name );
			if($parent) {
				$new_item->setParent($parent);
			}

			$export_categories[$id] = $new_item;
		}

		return $export_categories;

	}

	/**
	 * @param Shops_Shop $shop
	 *
	 * @return HeurekaCategory[]
	 */
	public function getExportCategories( Shops_Shop $shop ) : array
	{

		if($this->export_categories===null) {

			$this->export_categories = Exports_CategoriesCache::get( $this->getCode(), $shop, function() use ($shop) {
				return $this->__getExportCategories( $shop );
			} );
		}


		return $this->export_categories;

	}

	public function actualizeMetadata( Shops_Shop $shop ): void
	{
		Exports_CategoriesCache::reset( $this->getCode(), $shop );
		Exports_CategoriesCache::get( $this->getCode(), $shop, function() use ($shop) {
			return $this->__getExportCategories( $shop );
		} );
	}

	public function generateExports( Shops_Shop $shop ): void
	{
		$this->generateExports_products( $shop );
	}

	public function generateExports_products( Shops_Shop $shop ): void
	{
		$f = new Exports_FileGenerator_XML( $this->getCode(), $shop, 'google_shopping.xml' );

		$f->start();

		$f->tagStart('rss', [
			'version' => '2.0',
			'xmlns:g' => 'http://base.google.com/ns/1.0'
		]);

		$f->tagPair('title', ''); //TODO:
		$f->tagPair('link', ''); //TODO:
		$f->tagPair('description', ''); //TODO:



		$product_ids = Product::dataFetchAll(select:['id','ean', 'internal_code'], where:[
			'is_active' => true
		]);

		$count = count( $product_ids );
		$c = 0;

		foreach($product_ids as $product) {
			$id = $product['id'];
			$c++;

			echo '['.$c.'/'.$count.'] '.$id.PHP_EOL;

			/**
			 * @var Product_ShopData $sd
			 */
			$sd = Product_ShopData::load([
				'product_id' => $id,
				'AND',
				$shop->getWhere(),
				'AND',
				'is_active' => true
			], ['products_shop_data.*']);

			if(!$sd) {
				continue;
			}

			$f->tagStart( 'item' );

			$f->tagPair( 'title', $sd->getFullName() );
			$f->tagPair( 'description', $sd->getDescription() );
			$f->tagPair( 'link', $sd->getURL() );

			$f->tagPair( 'g:image_link', $sd->getImgUrl(0) );
			if( $sd->getImgUrl( 1 ) ) {
				$f->tagPair( 'g:additional_image_link', $sd->getImgUrl( 1 ) );
			}

			if( $sd->getDiscountPercentage() ) {
				$f->tagPair( 'g:price', $sd->getStandardPrice() );
				$f->tagPair( 'g:sale_price', $sd->getPrice() );
			} else {
				$f->tagPair( 'g:price', $sd->getPrice() );
			}


			$f->tagPair( 'g:condition', 'new' );
			$f->tagPair( 'g:identifier_exists', 'no' );
			$f->tagPair( 'g:id', $id );
			//TODO: $f->tagPair( 'g:product_type',  );
			//TODO: $f->tagPair( 'g:availability',  );
			//TODO: $f->tagPair( 'g:brand',  );
			//TODO: $f->tagPair( 'g:mpn',  );

			//TODO:$f->tagPair( 'g:google_product_category', $category );


			//TODO:
			//$f->tagStart( 'g:shipping' );
			//$f->tagEnd( 'g:shipping' );

			$f->tagEnd( 'item' );
		}


		$f->tagEnd('rss');
		$f->done();
	}

}