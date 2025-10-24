<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\DogNet;

use Jet\Application_Modules;
use Jet\Tr;
use JetApplication\Application_Service_EShop;
use JetApplication\Availability;
use JetApplication\Brand_EShopData;
use JetApplication\Delivery_Method;
use JetApplication\Exports_Definition;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_ExportCategory_Parameter;
use JetApplication\Exports_ExportCategory_Parameter_Value;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_Module;
use JetApplication\Exports_ProductParams;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplicationModule\Exports\Heureka\Main as Heureka;

class Main extends Exports_Module
{
	
	
	protected ?array $export_categories = null;
	
	public function getTitle(): string
	{
		return 'DogNet';
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
	
	public function generateExports_products( EShop $eshop, Pricelist $pricelist, Availability $availability ): void
	{
		$products = Product_EShopData::getAllActive( $eshop );
		$this->_generateExports_products(
			$eshop,$pricelist, $availability, $products,
		);
		
	}
	
	
	/**
	 * @param EShop $eshop
	 * @param Pricelist $pricelist
	 * @param Availability $availability
	 * @param Product_EShopData[] $products
	 * @return void
	 */
	public function _generateExports_products( EShop $eshop, Pricelist $pricelist, Availability $availability, array $products ): void
	{
		/*
		
		
			if (($product->getDeliverySetting_isXL())&&( $product->getFinalPriceVAT()>5000)) {
				
			
			}elseif (($product->getDeliverySetting_isXL())) {
				$content .= "\t\t" . '<DELIVERY>' . "\n";
				$content .= "\t\t" . '<DELIVERY_ID>GEIS</DELIVERY_ID>' . "\n";
				$content .= "\t\t" . '<DELIVERY_PRICE>299</DELIVERY_PRICE>' . "\n";
				$content .= "\t\t" . '<DELIVERY_PRICE_COD>329</DELIVERY_PRICE_COD>' . "\n";
				$content .= "\t" . '</DELIVERY>' . "\n";
			}
			
		

		 */
		
		
		
		
		
		
		$f = new Exports_Generator_XML( $this->getCode(), $eshop );
		
		$brand_map = Brand_EShopData::getNameMap( $eshop );
		$export_category_map = Exports_Join_KindOfProduct::getMap( 'Heureka', $eshop );
		$parameters = [];
		$export_categories = [];
		
		$product_params_export = new Exports_ProductParams( $eshop );
		
		$formatter = Application_Service_EShop::PriceFormatter();
		
		$f->start();
		
		$f->tagStart( 'SHOP' );
		
		
		foreach($products as $sd) {
			if($sd->isVariantMaster()) {
				continue;
			}
			
			$f->tagStart( 'SHOPITEM' );
			
			$f->tagPair( 'ITEM_ID', $sd->getId() );
			
			if($sd->isVariant()) {
				$f->tagPair( 'ITEMGROUP_ID', $sd->getVariantMasterProductId() );
			}
			
			if ($sd->getIsSale()) {
				$f->tagPair('ITEM_TYPE', 'bazar');
			}
			
			$f->tagPair( 'PRODUCT', $sd->getName() );
			$f->tagPair( 'PRODUCTNAME', $sd->getName() );
			$f->tagPair( 'DESCRIPTION', $sd->getDescription() );
			$f->tagPair( 'PRODUCTNO', $sd->getInternalCode() );
			$f->tagPair( 'URL', $sd->getURL() );
			$f->tagPair( 'PRICE_VAT', $sd->getPrice_WithVAT( $pricelist ) );
			
			foreach ($sd->getGifts() as $gift) {
				$g_p = $gift->getGiftProduct();
				if($g_p) {
					$f->tagPair('GIFT', " + ZDARMA " . $g_p->getName() . ' (v ceně '.$formatter->formatWithCurrency( $g_p->getPrice($pricelist), $pricelist ).')');
					break;
				}
			}
			
			
			if(isset($brand_map[$sd->getBrandId()])) {
				$f->tagPair( 'MANUFACTURER', $brand_map[$sd->getBrandId()] );
			}
			$f->tagPair( 'IMGURL', $sd->getImageURL(0) );
			foreach($sd->getImages() as $image) {
				if($image->getImageIndex()==0) {
					continue;
				}
				$f->tagPair( 'IMGURL_ALTERNATIVE', $image->getURL() );
			}
			
			$f->tagPair( 'WARRANTY', '24' );
			$f->tagPair( 'EAN',  $sd->getEan());
			
			
			$delivery_info = $sd->getDeliveryInfo( units_required: 1, availability: $availability );
			
			$f->tagPair('DELIVERY_DATE',
				$delivery_info->getAvailableFromDate() ?
					$delivery_info->getAvailableFromDate()->format('Y-m-d') : $delivery_info->getLengthOfDelivery()
			);
			
			
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
						$value->getValue()==='' ||
						$value->getValue()==='0'
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
				if($param->getUnits()) {
					$f->tagPair('UNIT', $param->getUnits());
				}
				$f->tagEnd('PARAM');
			}
			
			
			if ( $sd->getWeight()){
				$f->tagStart( 'PARAM' );
				$f->tagPair( 'PARAM_NAME', 'Hmotnost' );
				$f->tagPair( 'VAL', $sd->getWeight() );
				$f->tagEnd( 'PARAM' );
			}
			
			foreach(Delivery_Method::getAvailableByProducts( $eshop, [$sd] ) as $d_method) {
				$cod_price = 0;
				foreach($d_method->getPaymentMethods() as $payment_method) {
					if($payment_method->getKind()->isCOD()) {
						$cod_price = $payment_method->getPrice( $pricelist );
					}
				}
				
				$f->tagStart( 'DELIVERY' );
				$f->tagPair( 'DELIVERY_ID', $d_method->getID() );
				$f->tagPair( 'DELIVERY_PRICE', $d_method->getPrice($pricelist) );
				$f->tagPair( 'DELIVERY_PRICE_COD', $d_method->getPrice($pricelist)+$cod_price );
				$f->tagEnd( 'DELIVERY' );
				
				break;
			}
			
			$f->tagEnd( 'SHOPITEM' );
		}
		
		
		$f->tagEnd( 'SHOP' );
		
		$f->done();
	}
	
	
	public function getExportsDefinitions(): array
	{
		/**
		 * @var Heureka $heureka
		 */
		$heureka = Application_Modules::moduleInstance('Exports.Heureka');
		$allowed_eshops = [];
		foreach(EShops::getList() as $eshop) {
			if($heureka->isAllowedForShop($eshop)) {
				$allowed_eshops[] = $eshop;
			}
		}
		
		$products = new Exports_Definition(
			module: $this,
			name: Tr::_('DogNet - Products'),
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
			$products,
			//$old_categories
		];
	}
}