<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Exports\GoogleShopping;

use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availabilities_Availability;
use JetApplication\Brand_ShopData;
use JetApplication\Exports_Definition;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_Module;
use JetApplication\KindOfProduct_ShopData;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Pricelists_Pricelist;
use JetApplication\Product_Availability;
use JetApplication\Product_ShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;


/**
 *
 */
class Main extends Exports_Module implements ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected ?array $export_categories = null;

	public function getTitle(): string
	{
		return 'Google Shopping';
	}

	public function isAllowedForShop( Shops_Shop $shop ): bool
	{
		return true;
	}

	public function actualizeCategories( Shops_Shop $shop ) : void
	{
		/**
		 * @var Exports_ExportCategory[] $export_categories
		 */
		$export_categories = [];

		$data = file( $this->getShopConfig($shop)->getCategoriesURL() );

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
				
				$parent_id = $parent->getCategoryId();
				$path = $parent->getPath();
				if(!$path) {
					$path = [$parent_id];
				} else {
					$path[] = $parent_id;
				}
				
				
			} else {
				$parent_id = '';
				$parent = null;
				$name = $full_name;
				$path = [];
			}
			
			$e_c = Exports_ExportCategory::get( $shop, $this->getCode(), $id  );
			
			if(!$e_c) {
				$e_c = new Exports_ExportCategory();
				$e_c->setShop( $shop );
				$e_c->setExportCode( $this->getCode() );
				$e_c->setCategoryId( $id );
				$e_c->setCategorySecondaryId( $id );
			}
			
			$e_c->setParentCategoryId( $parent_id );
			$e_c->setName( $name );
			$e_c->setPath( $path );
			$e_c->setFullName( explode(' > ', $full_name) );
			
			$e_c->save();
			
			$export_categories[$id] = $e_c;
		}
	}
	
	
	public function actualizeCategory( Shops_Shop $shop, string $category_id ): void
	{
	}
	
	
	
	public function generateExports_products( Shops_Shop $shop, Pricelists_Pricelist $pricelist, Availabilities_Availability $availability ): void
	{
		$f = new Exports_Generator_XML( $this->getCode(), $shop );

		$f->start();

		$f->tagStart('rss', [
			'version' => '2.0',
			'xmlns:g' => 'http://base.google.com/ns/1.0'
		]);
		
		$f->tagPair('link', $shop->getHomepage()->getURL());
		$f->tagPair('title', '');
		$f->tagPair('description', '');
		
		
		$kind_of_product_map = KindOfProduct_ShopData::getNameMap( $shop );
		$brand_map = Brand_ShopData::getNameMap( $shop );
		$export_category_map = Exports_Join_KindOfProduct::getMap( $this->getCode(), $shop );
		$avl_map = Product_Availability::getInStockQtyMap( $shop->getDefaultAvailability() );

		
		$products = Product_ShopData::getAllActive();

		$count = count( $products );
		$c = 0;
		

		foreach($products as $sd) {
			$c++;
			

			$f->tagStart( 'item' );

			$f->tagPair( 'title', $sd->getFullName() );
			$f->tagPair( 'description', $sd->getDescription() );
			$f->tagPair( 'link', $sd->getURL() );

			$f->tagPair( 'g:image_link', $sd->getImgUrl(0) );
			if( $sd->getImgUrl( 1 ) ) {
				$f->tagPair( 'g:additional_image_link', $sd->getImgUrl( 1 ) );
			}
			
			if( $sd->getDiscountPercentage( $pricelist ) ) {
				$f->tagPair( 'g:price', $sd->getPriceBeforeDiscount( $pricelist ) );
				$f->tagPair( 'g:sale_price', $sd->getPrice( $pricelist ) );
			} else {
				$f->tagPair( 'g:price', $sd->getPrice( $pricelist ) );
			}


			$f->tagPair( 'g:condition', 'new' );
			$f->tagPair( 'g:identifier_exists', 'no' );
			$f->tagPair( 'g:id', $sd->getId() );
			
			if(isset($kind_of_product_map[$sd->getKindId()])) {
				$f->tagPair( 'g:product_type', $kind_of_product_map[$sd->getKindId()] );
				
				
				if( ($export_category_id = $export_category_map[$sd->getKindId()]??null) ) {
					$f->tagPair( 'g:google_product_category', $export_category_id );
				}
		   }
			
			
			if(isset($brand_map[$sd->getBrandId()])) {
				$f->tagPair( 'g:brand', $brand_map[$sd->getBrandId()] );
			}
			
			$in_stock_qty = $avl_map[$sd->getId()]??0;

			$f->tagPair( 'g:availability',
				($in_stock_qty>0) ? 'in_stock' : 'out_of_stock'
			);

			
			//$f->tagStart( 'g:shipping' ); $f->tagEnd( 'g:shipping' );
			//$f->tagPair( 'g:mpn', '' );

			$f->tagEnd( 'item' );
		}


		$f->tagEnd('rss');
		$f->done();
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_EXPORTS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Google Shopping export';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'file-export';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
	public function getExportsDefinitions(): array
	{
		$products = new Exports_Definition(
			module: $this,
			name: Tr::_('Google shopping - Products'),
			description: '',
			export_code: 'products',
			export: function() {
				$shop = Shops::getCurrent();
				
				$this->generateExports_products(
					$shop,
					$shop->getDefaultPricelist(),
					$shop->getDefaultAvailability()
				);
			}
		);
		
		return [
			$products
		];
	}
}