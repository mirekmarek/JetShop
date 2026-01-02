<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\ZboziCZ;

use Jet\Db;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availability;
use JetApplication\Brand_EShopData;
use JetApplication\Exports_Definition;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_ExportCategory_Parameter;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_Module;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Exports_ProductParams;
use JetApplication\KindOfProduct;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;


class Main extends Exports_Module implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected array $allowed_locales = [
		'cs_CZ',
	];
	
	protected ?array $export_categories = null;
	
	public function getTitle(): string
	{
		return 'Zboží CZ';
	}
	
	public function isAllowedForShop( EShop $eshop ): bool
	{
		$locale = $eshop->getLocale()->toString();
		
		if(!in_array( $locale, $this->allowed_locales )) {
			return false;
		}
		
		return (bool)$this->getEshopConfig($eshop)->getCategoriesURL();
	}
	
	protected function _actualizeCategories( EShop $eshop, array $category, array $path=[], array $parent_full_name = [], string $parent_id='' ): void
	{
		$id = $category['id']??'';
		$name = $category['name']??'';
		
		$full_name = $parent_full_name;
		$full_name[] = $name;
		
		$next_path = $path;
		if($id) {
			$next_path[] = $id;
		}
		
		
		if($id) {
			$e_c = Exports_ExportCategory::get( $eshop, $this->getCode(), $id );
			
			if( !$e_c ) {
				$e_c = new Exports_ExportCategory();
				$e_c->setEshop( $eshop );
				$e_c->setExportCode( $this->getCode() );
				$e_c->setCategoryId( $id );
				$e_c->setCategorySecondaryId( $id );
			}
			
			$e_c->setParentCategoryId( $parent_id );
			$e_c->setName( $name );
			$e_c->setPath( $path );
			$e_c->setFullName( $full_name );
			
			$e_c->save();
		}
		
		if(isset($category['children'])) {
			foreach($category['children'] as $child) {
				$this->_actualizeCategories($eshop, $child, $next_path, $full_name, $id);
			}
		}
	}
	
	public function actualizeCategories( EShop $eshop ): void
	{
		if( !$this->isAllowedForShop( $eshop ) ) {
			return;
		}
		
		$context = stream_context_create(["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false]]);
		$data = file_get_contents($this->getEshopConfig($eshop)->getCategoriesURL(), context: $context);
		if(!$data) {
			return;
		}
		
		$data = json_decode($data, true);
		if(!$data) {
			return;
		}
		
		foreach($data as $category) {
			$this->_actualizeCategories( $eshop, $category );
		}

		
	}
	
	public function actualizeCategory( EShop $eshop, string $category_id ): void
	{
	}
	
	/** @noinspection SpellCheckingInspection */
	public function generateExports_products( bool $sklik, EShop $eshop, Pricelist $pricelist, Availability $availability ): void
	{
		$f = new Exports_Generator_XML( $this->getCode(), $eshop );
		
		$brand_map = Brand_EShopData::getNameMap( $eshop );
		$export_category_map = Exports_Join_KindOfProduct::getMap( $this->getCode(), $eshop );
		$parameters = [];
		$export_categories = [];
		
		$f->start();
		
		$f->tagStart( 'SHOP', [
			'xmlns' => 'http://www.zbozi.cz/ns/offer/1.0'
		] );
		
		$products = Product_EShopData::getAllActive( $eshop );
		
		$product_params_export = new Exports_ProductParams( $eshop );
		
		foreach($products as $sd) {
			if($sd->isVariantMaster()) {
				continue;
			}
			
			if($sklik) {
				if(
					$sd->getIsSale() ||
					$sd->getPrice_WithVAT( $pricelist )<250
				) {
					continue;
				}
			}
			
			$f->tagStart( 'SHOPITEM' );
			
			$f->tagPair( 'ITEM_ID', $sd->getId() );
			if($sd->isVariant()) {
				$f->tagPair( 'ITEMGROUP_ID', $sd->getVariantMasterProductId() );
			}
			
			$name = $sd->getFullName();
			$join = $this->getProductJoin( $sd->getEshop(), $sd->getId() );
			
			$name = $join->getAletrnativeName() ? : $name;
			
			
			$f->tagPair( 'PRODUCTNO', $sd->getInternalCode() );
			$f->tagPair( 'PRODUCTNAME', $name );
			$f->tagPair( 'PRODUCT', $sd->getName() );
			$f->tagPair( 'DESCRIPTION', $sd->getDescription() );
			$f->tagPair( 'URL', $sd->getURL() );
			$f->tagPair( 'PRICE_VAT', $sd->getPrice_WithVAT( $pricelist ) );
			$f->tagPair( 'IMGURL', $sd->getImageURL(0) );
			if($sd->getImageURL(1)) {
				$f->tagPair( 'IMGURL_ALTERNATIVE', $sd->getImageURL(1) );
			}
			
			$ean = $sd->getEan();
			if(str_contains($ean, ',')) {
				$ean = explode(',', $ean);
				$ean = $ean[0];
			}
			
			$f->tagPair('EAN', $ean );
			
			if(isset($brand_map[$sd->getBrandId()])) {
				$f->tagPair( 'MANUFACTURER', $brand_map[$sd->getBrandId()] );
			}
			
			if( ($export_category_id = $export_category_map[$sd->getKindId()]??null) ) {
				
				if(!array_key_exists($export_category_id, $export_categories)) {
					$export_categories[$export_category_id] = Exports_ExportCategory::get(
						$eshop,
						$this->getCode(),
						$export_category_id
					);
				}
				
				if( $export_categories[$export_category_id] ) {
					$export_category = $export_categories[$export_category_id];
					$f->tagPair( 'CATEGORYTEXT', implode(' | ', $export_category->getFullName()) );
				}
			}
			
			$delivery_info = $sd->getDeliveryInfo( units_required: 1, availability: $availability );
			
			$f->tagPair('DELIVERY_DATE',
				$delivery_info->getAvailableFromDate() ?
				$delivery_info->getAvailableFromDate()->format('Y-m-d') :
					($delivery_info->isAvailable() ? 0 : $delivery_info->getLengthOfDelivery())
			);
			
			$f->tagPair('SHOP_DEPOTS', '12870152');
			$f->tagPair('SHOP_DEPOTS', '12870139');
			
			/*
			if($sd->getDopravaZdarma()) {
				$f->tagPair('EXTRA_MESSAGE', 'free_delivery');
			}
			*/
			
			if($sd->getGifts()) {
				$f->tagPair('EXTRA_MESSAGE', 'free_gift');
			}
			if($sd->getExtendedWarranty()) {
				$f->tagPair('EXTRA_MESSAGE', 'extended_warranty');
			}
			//$f->tagPair('EXTRA_MESSAGE', 'free_store_pickup');
			
			
			$params = $product_params_export->get( $sd );

			foreach($params as $param) {
				$f->tagStart('PARAM');
				$f->tagPair('PARAM_NAME', $param->getName());
				if(is_bool($param->getValue())) {
					$f->tagPair('VAL', $param->getValue()?'ano':'ne');
				} else {
					if(is_array($param->getValue())) {
						$f->tagPair('VAL', implode(', ', $param->getValue()));
					} else {
						$f->tagPair('VAL', $param->getValue());
					}
				}
				if(
					$param->getUnits()
				) {
					$f->tagPair('UNIT', $param->getUnits());
				}
				$f->tagEnd('PARAM');
			}
			

			//BRAND
			//CUSTOM_LABEL_0
			//CUSTOM_LABEL_1
			//CUSTOM_LABEL_3
			//PRODUCT_LINE
			//RELEASE_DATE
			
			//EPREL_ID
			//CONDITION
			//CONDITION_DESC
			//MAX_CPC
			//MAX_CPC_SEARCH
			//VISIBILITY
			
			
			$f->tagEnd( 'SHOPITEM' );
		}
		
		
		$f->tagEnd( 'SHOP' );
		
		$f->done();
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_EXPORTS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Zboží CZ export';
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
			name: Tr::_('Zboží CZ - Products'),
			description: '',
			export_code: 'products',
			export: function() {
				$eshop = EShops::getCurrent();
				
				$this->generateExports_products(
					sklik: false,
					eshop: $eshop,
					pricelist: $eshop->getDefaultPricelist(),
					availability: $eshop->getDefaultAvailability()
				);
			}
		);
		
		$products_sklik = new Exports_Definition(
			module: $this,
			name: Tr::_('Zboží CZ / Sklik - Products'),
			description: '',
			export_code: 'products_sklik',
			export: function() {
				$eshop = EShops::getCurrent();
				
				$this->generateExports_products(
					sklik: true,
					eshop: $eshop,
					pricelist: $eshop->getDefaultPricelist(),
					availability: $eshop->getDefaultAvailability()
				);
			}
		);
		
		
		/*
		$old_categories = new Exports_Definition(
			module: $this,
			name: Tr::_('Zboží CZ - Import old categories'),
			description: '',
			export_code: 'old_categories',
			export: function() {
				$this->importOldCategories();
			}
			
		);
		*/
		
		return [
			$products,
			$products_sklik,
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
		
		$items = $old_db->fetchAll("SELECT categories_id, categories_name_zbozicz, language_id FROM categories_description_sport WHERE categories_name_zbozicz>0");
		
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
			
			$join->setExportCategoryId( $item['categories_name_zbozicz'] );
			$join->save();
			
			$category_ids[] = $item['categories_name_zbozicz'];
		}
		
		$category_ids = array_unique($category_ids);
		
		foreach($shop_map as $eshop) {
			$csv_url = $this->getEshopConfig($eshop)->getParametersCsvURL();
			
			$data = file_get_contents( $csv_url );
			
			$data = explode( "\n", $data );
			
			$schema = str_getcsv( $data[0] );
			unset( $data[0] );
			
			$l = 0;
			foreach( $data as $line ) {
				$line = trim( $line );
				$line = str_getcsv( $line );
				
				$item = [];
				foreach( $schema as $i => $name ) {
					$item[$name] = $line[$i];
				}
				
				
				$c_id = $item['category_id']??0;
				
				if(!in_array($c_id, $category_ids)) {
					continue;
				}
				
				$param_id = $item['parameter_id'];
				
				$e_p = Exports_ExportCategory_Parameter::get(
					$eshop,
					$this->getCode(),
					$c_id,
					$param_id
				);
				
				if( !$e_p ) {
					$e_p = new Exports_ExportCategory_Parameter();
					$e_p->setEshop( $eshop );
					$e_p->setExportCode( $this->getCode() );
					$e_p->setExportCategoryId( $c_id );
					$e_p->setExportParameterId( $param_id );
				}
				
				$e_p->setName( $item['parameter_name'] );
				$e_p->setUnits( $item['parameter_unit'] );
				
				switch( $item['parameter_data_type'] ) {
					case 'bool':
						$e_p->setType( Exports_ExportCategory_Parameter::PARAM_TYPE_OPTIONS );
						$e_p->setMultipleValues( false );
						$e_p->setOptions([
							'ano' => 'Ano',
							'ne' => 'Ne'
						]);
						break;
					case 'int':
					case 'integer':
					case 'float':
						$e_p->setType( Exports_ExportCategory_Parameter::PARAM_TYPE_NUMBER );
						break;
					default:
					case 'string':
						$e_p->setType( Exports_ExportCategory_Parameter::PARAM_TYPE_STRING );
						break;
				}
				
				$e_p->save();
				
			}
			
		}
	}
}