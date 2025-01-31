<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\Heureka;


use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availability;
use JetApplication\Brand_EShopData;
use JetApplication\Exports_Definition;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_ExportCategory_Parameter;
use JetApplication\Exports_ExportCategory_Parameter_Value;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_Module;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;
use SimpleXMLElement;


class Main extends Exports_Module implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected array $allowed_locales = [
		'cs_CZ',
		'sk_SK'
	];
	
	protected ?array $export_categories = null;
	
	public function getTitle(): string
	{
		return 'Heureka';
	}
	
	public function isAllowedForShop( EShop $eshop ): bool
	{
		$locale = $eshop->getLocale()->toString();
		
		return in_array( $locale, $this->allowed_locales );
	}
	
	
	public function actualizeCategories( EShop $eshop ): void
	{
		if( !$this->isAllowedForShop( $eshop ) ) {
			return;
		}
		
		$context = stream_context_create( ['http' => ['timeout' => 2]] );
		$xml = @file_get_contents( $this->getEshopConfig($eshop)->getCategoriesURL(), false, $context );
		if( !$xml ) {
			return;
		}
		
		$xml = new SimpleXMLElement( $xml );
		
		$export_categories = [];
		
		$addCategory = null;
		
		$addCategory = function( SimpleXMLElement $xml, $parent_id = '', array $full_name = [], array $path = [] ) use ( $eshop, &$addCategory, &$export_categories ) {
			foreach( $xml->CATEGORY as $xnl_node ) {
				
				$id = trim( (string)$xnl_node->CATEGORY_ID );
				$next_path = $path;
				$next_path[] = $id;
				$name = (string)$xnl_node->CATEGORY_NAME;
				
				$_full_name = $full_name;
				$_full_name[] = $name;
				
				
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
				$e_c->setFullName( $_full_name );
				
				$e_c->save();
				
				$addCategory( $xnl_node, $id, $_full_name, $next_path );
			}
			
		};
		
		$addCategory( $xml );
	}
	
	public function actualizeCategory( EShop $eshop, string $category_id ): void
	{
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
			
			if( $item['categoryId'] != $category_id ) {
				continue;
			}
			
			$param_id = $item['parametrId'];
			
			$e_p = Exports_ExportCategory_Parameter::get(
				$eshop,
				$this->getCode(),
				$category_id,
				$param_id
			);
			
			if( !$e_p ) {
				$e_p = new Exports_ExportCategory_Parameter();
				$e_p->setEshop( $eshop );
				$e_p->setExportCode( $this->getCode() );
				$e_p->setExportCategoryId( $category_id );
				$e_p->setExportParameterId( $param_id );
			}
			
			switch( $item['parameterDataType'] ) {
				case 'string':
					$e_p->setType( Exports_ExportCategory_Parameter::PARAM_TYPE_STRING );
					break;
				default:
					$e_p->setType( Exports_ExportCategory_Parameter::PARAM_TYPE_OPTIONS );
					break;
			}
			
			$e_p->setName( $item['parameterName'] );
			$e_p->setOptions( explode( ',', $item['parameterValue'] ) );
			$e_p->setUnits( $item['parameterUnit'] );
			
			$e_p->save();
			
		}
	}
	
	/** @noinspection SpellCheckingInspection */
	public function generateExports_products( EShop $eshop, Pricelist $pricelist, Availability $availability ): void
	{
		$f = new Exports_Generator_XML( $this->getCode(), $eshop );
		
		$brand_map = Brand_EShopData::getNameMap( $eshop );
		$export_category_map = Exports_Join_KindOfProduct::getMap( $this->getCode(), $eshop );
		$parameters = [];
		$export_categories = [];
		
		$f->start();
		
		$f->tagStart( 'SHOP' );
		
		$products = Product_EShopData::getAllActive();
		
		foreach($products as $sd) {
			$f->tagStart( 'SHOPITEM' );
			
			$f->tagPair( 'ITEM_ID', $sd->getId() );

			if($sd->isVariant()) {
				$f->tagPair( 'ITEMGROUP_ID', $sd->getVariantMasterProductId() );
			}
			
			if($sd->isVariantMaster()) {
				$f->tagPair( 'ITEMGROUP_ID', $sd->getId() );
			}
			
			$f->tagPair( 'PRODUCT', $sd->getName() );
			$f->tagPair( 'PRODUCTNAME', $sd->getName() );
			$f->tagPair( 'DESCRIPTION', $sd->getDescription() );
			$f->tagPair( 'PRODUCTNO', $sd->getInternalCode() );
			$f->tagPair( 'URL', $sd->getURL() );
			$f->tagPair( 'PRICE_VAT', $sd->getPriceBeforeDiscount( $pricelist ) );
			
			if(isset($brand_map[$sd->getBrandId()])) {
				$f->tagPair( 'MANUFACTURER', $brand_map[$sd->getBrandId()] );
			}
			$f->tagPair( 'IMGURL', $sd->getImageURL(0) );
			
			//$f->tagPair( 'WARRANTY', '' );
			//$f->tagPair( 'DELIVERY_DATE', '' );
			
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

				if(!isset($parameters[$export_category_id])) {
					$parameters[$export_category_id] = Exports_ExportCategory_Parameter::getForCategory(
						$eshop,
						$this->getCode(),
						$export_category_id
					);
				}
				$params = $parameters[$export_category_id];
				
				$values = Exports_ExportCategory_Parameter_Value::getForProduct(
					$eshop,
					$this->getCode(),
					$export_category_id,
					$sd->getId()
				);
				
				foreach($params as $param_id=>$param) {
					$value = $values[$param_id]??null;
					if(
						!$value ||
						$value->getValue()===''
					) {
						continue;
					}
					
					$value = $value->getValue();
					
					if($param->getType()==Exports_ExportCategory_Parameter::PARAM_TYPE_OPTIONS) {
						$_value = explode('|', $value);
						$value = [];
						foreach($_value as $val) {
							$val = $param->getOptions()[$val];
							
							if($param->getUnits()) {
								$val .= ' '.$param->getUnits();
							}
							
							$value[] = $val;
						}
						
						$value = implode(', ', $value);
					} else {
						if($param->getUnits()) {
							$value .= ' '.$param->getUnits();
						}
					}
					
					
					$f->tagStart('PARAM');
						$f->tagPair('PARAM_NAME', $param->getName());
						$f->tagPair('VAL', $value);
					$f->tagEnd('PARAM');
				}

			}
			
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
		return 'Heureka export';
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
			name: Tr::_('Heureka - Products'),
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
		
		return [
			$products
		];
	}
}