<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\GoogleSitemap;

use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availability;
use JetApplication\Brand;
use JetApplication\Brand_EShopData;
use JetApplication\Category;
use JetApplication\Category_EShopData;
use JetApplication\Content_Article;
use JetApplication\Content_Article_EShopData;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShops;
use JetApplication\Exports_Definition;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Module;
use JetApplication\Marketing_LandingPage;
use JetApplication\Pricelist;
use JetApplication\Product;
use JetApplication\Product_EShopData;
use JetApplication\Signpost;
use JetApplication\Signpost_EShopData;

class Main extends Exports_Module implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public function getTitle(): string
	{
		return 'Google Sitemap';
	}
	
	public function isAllowedForShop( EShop $eshop ): bool
	{
		return false;
	}
	
	public function actualizeCategories( EShop $eshop ): void
	{
	}
	
	public function actualizeCategory( EShop $eshop, string $category_id ): void
	{
	}
	
	public function getExportsDefinitions(): array
	{
		
		$_exports = [
			'products' => [
				'name' => 'Products',
				'allowed_method_name' => 'getProductsAllowed',
			],
			'categories' => [
				'name' => 'Categories',
				'allowed_method_name' => 'getCategoriesAllowed',
			],
			'signposts' => [
				'name' => 'Signposts',
				'allowed_method_name' => 'getSignpostsAllowed',
			],
			
			'articles' => [
				'name' => 'Articles',
				'allowed_method_name' => 'getArticlesAllowed',
			],
			'brands' => [
				'name' => 'Brands',
				'allowed_method_name' => 'getBrandsAllowed',
			],
			
			'marketing_landing_pages' => [
				'name' => 'Marketing landing pages',
				'allowed_method_name' => 'getMarketingLandingPagesAllowed',
			],
		];
		
		
		$common_allowed_eshops = [];
		$exports = [];
		foreach($_exports as $code=>$e) {
			$allowed_eshops = [];
			foreach(EShops::getList() as $eshop) {
				/**
				 * @var Config_PerShop $config
				 */
				$config = $this->getEshopConfig( $eshop );
				if($config->{$e['allowed_method_name']}()) {
					$allowed_eshops[] = $eshop;
					$common_allowed_eshops[$eshop->getKey()] = $eshop;
				}
			}
			$export = new Exports_Definition(
				module: $this,
				name: Tr::_('Google Sitemap - '.$e['name']),
				description: '',
				export_code: $code,
				export: function() use ($code) {
					$eshop = EShops::getCurrent();
					
					$this->{"generateExports_{$code}"}(
						$eshop,
						$eshop->getDefaultPricelist(),
						$eshop->getDefaultAvailability()
					);
					
				}
			);
			$export->setAllowedEshops( $allowed_eshops );
			
			$exports[] = $export;
		}

		
		
		$index = new Exports_Definition(
			module: $this,
			name: Tr::_('Google Sitemap - Index'),
			description: '',
			export_code: 'index',
			export: function() use ($exports) {
				$eshop = EShops::getCurrent();
				
				$this->generateExports_index(
					$eshop,
					$exports
				);
				
			}
		);
		$index->setAllowedEshops( $common_allowed_eshops );
		
		$exports[] = $index;
		
		return $exports;
	}
	
	protected function startExport( EShop $eshop ) : Exports_Generator_XML
	{
		$f = new Exports_Generator_XML( $this->getCode(), $eshop );
		
		$f->start();
		$f->tagStart('urlset', [
			'xmlns'       => 'http://www.sitemaps.org/schemas/sitemap/0.9',
			'xmlns:image' => 'http://www.google.com/schemas/sitemap-image/1.1',
			'xmlns:video' => 'http://www.google.com/schemas/sitemap-video/1.1'
		]);

		return $f;
	}
	
	protected function getLastModified( string|EShopEntity_Basic $entity, int $id ): Data_DateTime
	{
		$item_data = $entity::dataFetchRow(
			select: [
				'created',
				'last_update'
			],
			where: ['id'=>$id],
		);
		
		$last_mod = null;
		
		if($item_data) {
			if($item_data['last_update']) {
				$last_mod = $item_data['last_update'];
			} else {
				if($item_data['created']) {
					$last_mod = $item_data['created'];
				}
			}
		}
		
		if(!$last_mod) {
			$last_mod = Data_DateTime::now();
		}
		
		return $last_mod;
	}
	
	public function generateExports_products( EShop $eshop, Pricelist $pricelist, Availability $availability ) : void
	{
		$f = $this->startExport( $eshop );
		
		$items = Product_EShopData::getAllActive( $eshop );
		
		foreach($items as $item) {
			$last_mod = $this->getLastModified( Product::class, $item->getId() );
			
			$f->tagStart('url');
				$f->tagPair('loc', $item->getURL());
				
				$image = $item->getImage(0)?->getUrl()??'';
			
				if($image) {
					$f->tagStart('image:image');
						$f->tagPair('image:loc', $image);
						//$f->tagPair('image:caption', $product->getDescription());
						//$f->tagPair('image:title', $product->getName());
					$f->tagEnd('image:image');
				}
				
				$f->tagPair('changefreq', 'weekly');
				$f->tagPair('last_mod', $last_mod);
				//$f->tagPair('priority', $priority);
			
			
			$f->tagEnd('url');
		}
		
		
		$f->tagEnd('urlset');
		$f->done();
	}
	
	public function generateExports_categories( EShop $eshop, Pricelist $pricelist, Availability $availability ) : void
	{
		$f = $this->startExport( $eshop );
		
		$items = Category_EShopData::getAllActive( $eshop );
		foreach( $items as $item ) {
			if(
				!$item->isActive() ||
				!$item->getBranchProductsCount()
			) {
				continue;
			}
			
			$last_mod = $this->getLastModified( Category::class, $item->getId() );
			
			$f->tagStart('url');
				$f->tagPair('loc', $item->getURL());
				
				$image = $item->getMainImageUrl();
			
				if($image) {
					$f->tagStart('image:image');
					$f->tagPair('image:loc', $image);
					$f->tagEnd('image:image');
				}
				
				$f->tagPair('changefreq', 'weekly');
				$f->tagPair('last_mod', $last_mod);
				
			$f->tagEnd('url');
		}
		
		
		$f->tagEnd('urlset');
		$f->done();
	}
	
	public function generateExports_articles( EShop $eshop, Pricelist $pricelist, Availability $availability ) : void
	{
		$f = $this->startExport( $eshop );
		
		$items = Content_Article_EShopData::getAllActive( $eshop );
		foreach( $items as $item ) {
			
			$last_mod = $this->getLastModified( Content_Article::class, $item->getId() );
			
			$f->tagStart('url');
			$f->tagPair('loc', $item->getURL());
			
			$image = $item->getImageHeader1Url();
			
			if($image) {
				$f->tagStart('image:image');
				$f->tagPair('image:loc', $image);
				$f->tagEnd('image:image');
			}
			
			$f->tagPair('changefreq', 'weekly');
			$f->tagPair('last_mod', $last_mod);
			
			$f->tagEnd('url');
		}
		
		
		$f->tagEnd('urlset');
		$f->done();
	}
	
	public function generateExports_brands( EShop $eshop, Pricelist $pricelist, Availability $availability ) : void
	{
		$f = $this->startExport( $eshop );
		
		$items = Brand_EShopData::getAllActive( $eshop );
		foreach( $items as $item ) {
			
			$last_mod = $this->getLastModified( Brand::class, $item->getId() );
			
			$f->tagStart('url');
			$f->tagPair('loc', $item->getURL());
			
			$image = $item->getImageUrl('logo');
			
			if($image) {
				$f->tagStart('image:image');
				$f->tagPair('image:loc', $image);
				$f->tagEnd('image:image');
			}
			
			$f->tagPair('changefreq', 'weekly');
			$f->tagPair('last_mod', $last_mod);
			
			$f->tagEnd('url');
		}
		
		
		$f->tagEnd('urlset');
		$f->done();
	}
	
	public function generateExports_signposts( EShop $eshop, Pricelist $pricelist, Availability $availability ) : void
	{
		$f = $this->startExport( $eshop );
		
		$items = Signpost_EShopData::getAllActive( $eshop );
		foreach( $items as $item ) {
			
			$last_mod = $this->getLastModified( Signpost::class, $item->getId() );
			
			$f->tagStart('url');
			$f->tagPair('loc', $item->getURL());
			
			$image = $item->getImageUrl('main');
			
			if($image) {
				$f->tagStart('image:image');
				$f->tagPair('image:loc', $image);
				$f->tagEnd('image:image');
			}
			
			$f->tagPair('changefreq', 'weekly');
			$f->tagPair('last_mod', $last_mod);
			
			$f->tagEnd('url');
		}
		
		
		$f->tagEnd('urlset');
		$f->done();
		
	}
	
	public function generateExports_marketing_landing_pages( EShop $eshop, Pricelist $pricelist, Availability $availability ) : void
	{
		$f = $this->startExport( $eshop );
		
		$items = Marketing_LandingPage::getAllActive( $eshop );
		foreach( $items as $item ) {
			
			$last_mod = $item->getLastUpdate();
			
			$f->tagStart('url');
			$f->tagPair('loc', $item->getURL());
			
			$f->tagPair('changefreq', 'weekly');
			$f->tagPair('last_mod', $last_mod);
			
			$f->tagEnd('url');
		}
		
		
		$f->tagEnd('urlset');
		$f->done();
		
	}

	
	/**
	 * @param EShop $eshop
	 * @param array<Exports_Definition> $exports
	 * @return void
	 */
	public function generateExports_index( EShop $eshop, array $exports ) : void
	{
		
		$f = new Exports_Generator_XML( $this->getCode(), $eshop );
		$f->start();
		
		$f->tagStart('sitemapindex', [
			'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9'
		]);
		
		foreach($exports as $export) {
			if(!$export->isAllowedForEshop($eshop)) {
				continue;
			}
			
			$f->tagStart('sitemap');
			$f->tagPair('loc', str_replace('2025.mastersport.cz', $_SERVER['HTTP_HOST'], $export->getURL()));
			$f->tagEnd('sitemap');
		}
		
		$f->tagEnd('sitemapindex');
		$f->done();
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_EXPORTS;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Google sitemap export';
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
}