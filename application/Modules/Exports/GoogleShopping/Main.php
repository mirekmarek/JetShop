<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\GoogleShopping;


use Jet\Db;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availability;
use JetApplication\Brand_EShopData;
use JetApplication\Exports_Definition;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_Module;
use JetApplication\KindOfProduct;
use JetApplication\KindOfProduct_EShopData;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Pricelist;
use JetApplication\Product_Availability;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;



class Main extends Exports_Module implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected ?array $export_categories = null;

	public function getTitle(): string
	{
		return 'Google Shopping';
	}

	public function isAllowedForShop( EShop $eshop ): bool
	{
		return (bool)$this->getEshopConfig($eshop)->getCategoriesURL();
	}

	public function actualizeCategories( EShop $eshop ) : void
	{
		/**
		 * @var Exports_ExportCategory[] $export_categories
		 */
		$export_categories = [];

		$data = file( $this->getEshopConfig($eshop)->getCategoriesURL() );

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
			
			$e_c = Exports_ExportCategory::get( $eshop, $this->getCode(), $id  );
			
			if(!$e_c) {
				$e_c = new Exports_ExportCategory();
				$e_c->setEshop( $eshop );
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
	
	
	public function actualizeCategory( EShop $eshop, string $category_id ): void
	{
	}
	
	
	
	public function generateExports_products( EShop $eshop, Pricelist $pricelist, Availability $availability ): void
	{
		$f = new Exports_Generator_XML( $this->getCode(), $eshop );

		$f->start();

		$f->tagStart('rss', [
			'version' => '2.0',
			'xmlns:g' => 'http://base.google.com/ns/1.0'
		]);
		
		$f->tagPair('link', $eshop->getHomepage()->getURL());
		$f->tagPair('title', '');
		$f->tagPair('description', '');
		
		
		$kind_of_product_map = KindOfProduct_EShopData::getNameMap( $eshop );
		$brand_map = Brand_EShopData::getNameMap( $eshop );
		$export_category_map = Exports_Join_KindOfProduct::getMap( $this->getCode(), $eshop );
		$avl_map = Product_Availability::getInStockQtyMap( $eshop->getDefaultAvailability() );

		
		$products = Product_EShopData::getAllActive( $eshop );

		$count = count( $products );
		$c = 0;
		

		foreach($products as $sd) {
			$c++;
			

			$f->tagStart( 'item' );
			
			$name = $sd->getFullName();
			$join = $this->getProductJoin( $sd->getEshop(), $sd->getId() );
			$name = $join->getAletrnativeName() ?  : $name;
			
			$f->tagPair( 'title', $name );
			$f->tagPair( 'description', $sd->getDescription() );
			$f->tagPair( 'link', $sd->getURL() );

			$f->tagPair( 'g:image_link', $sd->getImageURL(0) );
			if( $sd->getImageURL( 1 ) ) {
				$f->tagPair( 'g:additional_image_link', $sd->getImageURL( 1 ) );
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
				$eshop = EShops::getCurrent();
				
				$this->generateExports_products(
					$eshop,
					$eshop->getDefaultPricelist(),
					$eshop->getDefaultAvailability()
				);
			}
		);
		
		/*
		$old_categories = new Exports_Definition(
			module: $this,
			name: Tr::_('Google shopping - Import old categories'),
			description: '',
			export_code: 'old_categories',
			export: function() {
				$this->importOldCategories();
			}
		
		);
		*/
		
		return [
			$products,
			//$old_categories
		];
	}
	
	
	public function importOldCategories() : void
	{
		$old_db = Db::get('old');
		
		$shop_map = [
			5 => EShops::get('b2c_cs_CZ'),
			20 => EShops::get('b2c_sk_SK'),
		];
		
		$items = $old_db->fetchAll("SELECT categories_id, categories_name_gnakupy, language_id FROM categories_description_sport WHERE categories_name_gnakupy>0");
		
		$category_ids = [];
		
		foreach($items as $item) {
			$kind_of_product= KindOfProduct::get( $item['categories_id'] );
			if(!$kind_of_product) {
				continue;
			}
			
			$eshop = $shop_map[$item['language_id']];
			if(!$eshop) {
				die('!!!!');
			}
			
			$join = Exports_Join_KindOfProduct::get(
				$this->getCode(),
				$eshop,
				$item['categories_id'],
			);
			
			$join->setExportCategoryId( $item['categories_name_gnakupy'] );
			$join->save();
			
			$category_ids[] = $item['categories_name_gnakupy'];
		}
		
	}
}