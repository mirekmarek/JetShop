<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\Criteo;


use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availability;
use JetApplication\Brand_EShopData;
use JetApplication\Exports_Definition;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_Module;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\KindOfProduct_EShopData;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;


class Main extends Exports_Module implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	
	public function getTitle(): string
	{
		return 'Criteo';
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
	
	/** @noinspection SpellCheckingInspection */
	public function generateExports_products( EShop $eshop, Pricelist $pricelist, Availability $availability ): void
	{
		$f = new Exports_Generator_XML( $this->getCode(), $eshop );
		
		$brand_map = Brand_EShopData::getNameMap( $eshop );
		$export_category_map = Exports_Join_KindOfProduct::getMap( $this->getCode(), $eshop );
		$parameters = [];
		$export_categories = [];
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getEshopConfig( $eshop );
		
		$f->start();
		
		
		$f->tagStart( 'rss', [
			'version' => '2.0',
			'xmlns:g' => 'http://base.google.com/ns/1.0',
		] );
		$f->tagStart('channel');
		$f->tagPair( 'title', $config->getTitle() );
		$f->tagPair( 'description', $config->getDescription() );
		$f->tagPair( 'link', $config->getLink() );
		
		$brand_map = Brand_EShopData::getNameMap( $eshop );
		
		
		$products = Product_EShopData::getAllActive( $eshop );
		
		$kind_of_product_map = KindOfProduct_EShopData::getNameMap( $eshop );
		$export_category_map = Exports_Join_KindOfProduct::getMap( 'GoogleShopping', $eshop );
		
		foreach($products as $sd) {
			if($sd->isVariantMaster()) {
				continue;
			}
			$f->tagStart( 'item' );
			
			$f->tagPair( 'title', $sd->getFullName() );
			$f->tagPair( 'description', $sd->getDescription() );
			$f->tagPair( 'link', $sd->getURL() );
			$f->tagPair( 'g:id', $sd->getId() );
			$f->tagPair( 'g:condition', 'new' );
			$f->tagPair( 'g:price', $sd->getPrice($pricelist).' '.$pricelist->getCurrency()->getCode() );
			$f->tagPair( 'g:gtin', $sd->getEan() );
			$f->tagPair( 'g:mpn', $sd->getInternalCode() );
			
			if(isset($brand_map[$sd->getBrandId()])) {
				$f->tagPair( 'g:brand', $brand_map[$sd->getBrandId()] );
			}
			
			if(isset($kind_of_product_map[$sd->getKindId()])) {
				$f->tagPair( 'g:product_type', $kind_of_product_map[$sd->getKindId()] );
				
				if( ($export_category_id = $export_category_map[$sd->getKindId()]??null) ) {
					$f->tagPair( 'g:google_product_category', $export_category_id );
				}
			}
			
			
			foreach($sd->getImages() as $image) {
				if($image->getImageIndex()==0) {
					$f->tagPair( 'g:image_link', $image->getURL() );
				} else {
					$f->tagPair( 'g:additional_image_link', $image->getURL() );
				}
			}
			
			$avl_info = $sd->getDeliveryInfo( 1, $availability );
			if($avl_info->getNumberOfUnitsAvailable()<3) {
				$f->tagPair('g:availability', 'out of stock');
			} else {
				$f->tagPair('g:availability', 'in stock');
			}
			
			$f->tagEnd( 'item' );
			
		}
		
		
		$f->tagEnd('channel');
		$f->tagEnd( 'rss' );
		
		$f->done();
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_EXPORTS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Criteo export';
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
		$allowed_eshops = [];
		
		foreach(EShops::getList() as $eshop) {
			/**
			 * @var Config_PerShop $config
			 */
			$config = $this->getEshopConfig($eshop);
			if($config->getTitle()) {
				$allowed_eshops[] = $eshop;
			}
		}
		
		$products = new Exports_Definition(
			module: $this,
			name: Tr::_('Crtiteo - Products'),
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
		
		$products->setAllowedEshops( $allowed_eshops );
		
		return [
			$products
		];
	}

}