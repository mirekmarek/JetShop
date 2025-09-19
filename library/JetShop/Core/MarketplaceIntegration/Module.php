<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use Jet\Form_Field_MultiSelect;
use Jet\Form_Field_Select;
use Jet\MVC_Page_Content;

use Jet\Tr;
use JetApplication\Brand;
use JetApplication\MarketplaceIntegration_Join_Brand;
use JetApplication\MarketplaceIntegration_Join_Cache;
use JetApplication\MarketplaceIntegration_Join_ProductCommonData;
use JetApplication\MarketplaceIntegration_Marketplace;
use JetApplication\MarketplaceIntegration_MarketplaceBrand;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\MarketplaceIntegration_Module_Controller_BrandSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_KindOfProductSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;
use JetApplication\MarketplaceIntegration_Module_Controller_OrderDetail;
use JetApplication\MarketplaceIntegration;
use JetApplication\MarketplaceIntegration_Join_KindOfProduct;
use JetApplication\MarketplaceIntegration_Join_Product;
use JetApplication\MarketplaceIntegration_MarketplaceCategory;
use JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter;
use JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter_Value;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Product;
use JetApplication\EShop;
use JetApplication\KindOfProduct;
use JetApplication\Product_EShopData;

abstract class Core_MarketplaceIntegration_Module extends Application_Module
{
	
	/**
	 * @var Form[]
	 */
	protected array $param_edit_form = [];
	
	protected EShop $eshop;
	protected ?MarketplaceIntegration_Marketplace $marketplace = null;

	public function getCode() : string
	{
		$code = $this->getModuleManifest()->getName();

		$prefix = MarketplaceIntegration::getModuleNamePrefix();

		return substr($code, strlen($prefix));
	}

	
	public function init( EShop $eshop ): void
	{
		$this->eshop = $eshop;
		$this->marketplace = new MarketplaceIntegration_Marketplace( $this->getCode(), $eshop );
	}
	
	public function getMarketplace() : MarketplaceIntegration_Marketplace
	{
		return $this->marketplace;
	}

	abstract public function getTitle() : string;

	abstract public function isAllowedForShop( EShop $eshop ) : bool;
	
	public function hasProductSettings() : bool
	{
		return true;
	}
	
	public function hasKindOfProductSettings() : bool
	{
		return true;
	}
	
	public function hasBrandSettings() : bool
	{
		return true;
	}
	
	public function handleKindOfProductSettings( KindOfProduct $kind_of_product ): void
	{
		/**
		 * @var MarketplaceIntegration_Module $this
		 */
		$content = new MVC_Page_Content();

		$content->setModuleName( $this->getModuleManifest()->getName() );
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_KindOfProductSettings $controller
		 */
		$ns = $this->getModuleManifest()->getNamespace();
		$controller = $ns.'Controller_KindOfProductSettings';
		
		/**
		 * @var MarketplaceIntegration_Module $mp
		 */
		$mp = $this;
		
		Tr::setCurrentDictionaryTemporary(
			dictionary: $this->getModuleManifest()->getName(),
			action: function () use ($controller, $content, $kind_of_product, $mp) {
				$controller = new $controller( $content );
				$controller->init(
					$kind_of_product,
					$mp
				);
				$content->setControllerAction( $controller->resolve() );
				$controller->dispatch();
			}
		);
		
	}
	
	public function handleProductSettings( Product $product ): void
	{
		$content = new class extends MVC_Page_Content {
			public function output( string $output ): void
			{
				$this->output = $output;
			}
		};
		
		$content->setModuleName( $this->getModuleManifest()->getName() );
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_ProductSettings $controller
		 */
		$ns = $this->getModuleManifest()->getNamespace();
		$controller = $ns.'Controller_ProductSettings';
		
		/**
		 * @var MarketplaceIntegration_Module $mp
		 */
		$mp = $this;
		
		Tr::setCurrentDictionaryTemporary(
			dictionary: $this->getModuleManifest()->getName(),
			action: function () use ($controller, $content, $product, $mp) {
				$controller = new $controller( $content );
				$controller->init(
					$product,
					$mp
				);
				$content->setControllerAction( $controller->resolve() );
				$controller->dispatch();
			}
		);
	}
	
	public function handleBrandSettings( Brand $brand ): void
	{
		$content = new MVC_Page_Content();
		
		$content->setModuleName( $this->getModuleManifest()->getName() );
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_BrandSettings $controller
		 */
		$ns = $this->getModuleManifest()->getNamespace();
		$controller = $ns.'Controller_BrandSettings';
		
		/**
		 * @var MarketplaceIntegration_Module $mp
		 */
		$mp = $this;
		
		Tr::setCurrentDictionaryTemporary(
			dictionary: $this->getModuleManifest()->getName(),
			action: function () use ($controller, $content, $brand, $mp) {
				$controller = new $controller( $content );
				$controller->init(
					$brand,
					$mp
				);
				$content->setControllerAction( $controller->resolve() );
				$controller->dispatch();
			}
		);
		
	}
	
	
	public function handleOrderDetail( Order $order ): void
	{
		$this->init( $order->getEshop() );

		$content = new MVC_Page_Content();
		
		$content->setModuleName( $this->getModuleManifest()->getName() );
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_OrderDetail $controller
		 */
		$ns = $this->getModuleManifest()->getNamespace();
		$controller = $ns.'Controller_OrderDetail';
		
		/**
		 * @var MarketplaceIntegration_Module $mp
		 */
		$mp = $this;
		
		Tr::setCurrentDictionaryTemporary(
			dictionary: $this->getModuleManifest()->getName(),
			action: function () use ($controller, $content, $order, $mp) {
				$controller = new $controller( $content );
				$controller->init(
					$order,
					$mp
				);
				$content->setControllerAction( $controller->resolve() );
				$controller->dispatch();
			}
		);
		
	}
	
	
	abstract public function actualizeBrands() : void;
	
	abstract public function actualizeCategories() : void;
	
	abstract public function actualizeCategory( string $category_id ) : void;
	
	public function getImportSource() : string
	{
		return static::IMPORT_SOURCE;
	}
	
	public function orderIsRelevant( Order $order ) : bool
	{
		return ($order->getImportSource()==$this->getImportSource());
	}
	
	abstract public function handleOrderEvent( Order_Event $order_event ) : bool;
	
	
	public function getCache( string $cache_key ) : mixed
	{
		$cd = MarketplaceIntegration_Join_Cache::get( $this->getMarketplace(), $cache_key );
		return $cd?->getCacheData();
	}
	
	public function setCache( string $cache_key, mixed $cache_data ) : void
	{
		$cd = MarketplaceIntegration_Join_Cache::get( $this->getMarketplace(), $cache_key );
		if(!$cd) {
			$cd = new MarketplaceIntegration_Join_Cache();
			$cd->setMarketplace( $this->getMarketplace() );
			$cd->setCacheKey( $cache_key );
		}
		
		$cd->setCacheData( $cache_data );
		$cd->save();
	}
	
	
	public function getProductJoin( int $product_id ) : ?MarketplaceIntegration_Join_Product
	{
		return MarketplaceIntegration_Join_Product::get( $this->getMarketplace(), $product_id );
	}
	
	public function getProductCommonData( int $product_id, string $common_data_key ) : mixed
	{
		$cd = MarketplaceIntegration_Join_ProductCommonData::get( $this->getMarketplace(),$product_id, $common_data_key );
		
		return $cd?->getCommonData();
	}
	
	public function getProductCommonData_int( int $product_id, string $common_data_key ) : ?int
	{
		$co = $this->getProductCommonData( $product_id, $common_data_key );

		if(is_array($co)) {
			return (int)$co[0];
		}
		
		return null;
	}
	
	public function getProductCommonData_float( int $product_id, string $common_data_key ) : ?float
	{
		$co = $this->getProductCommonData( $product_id, $common_data_key );
		
		if(is_array($co)) {
			return (float)$co[0];
		}
		
		return null;
	}
	
	public function getProductCommonData_string( int $product_id, string $common_data_key ) : ?string
	{
		$co = $this->getProductCommonData( $product_id, $common_data_key );
		
		if(is_array($co)) {
			return (string)$co[0];
		}
		
		return null;
	}
	
	
	public function setProductCommonData( int $product_id, string $common_data_key, mixed $common_data ) : void
	{
		$cd = MarketplaceIntegration_Join_ProductCommonData::get( $this->getMarketplace(), $product_id, $common_data_key );
		if(!$cd) {
			$cd = new MarketplaceIntegration_Join_ProductCommonData();
			$cd->setMarketplace( $this->getMarketplace() );
			$cd->setProductId( $product_id );
			$cd->setCommonDataKey( $common_data_key );
		}
		
		$cd->setCommonData( $common_data );
		$cd->save();
	}
	
	public function stopSelling( int $product_id ) : void
	{
		$join = MarketplaceIntegration_Join_Product::get( $this->getMarketplace(), $product_id );
		$join?->delete();
	}
	
	public function startSelling( int $product_id ) : void
	{
		$join = MarketplaceIntegration_Join_Product::get( $this->getMarketplace(), $product_id );
		if($join) {
			return;
		}
		
		$join = new MarketplaceIntegration_Join_Product();
		$join->setMarketplace( $this->getMarketplace() );
		$join->setProductId( $product_id );
		$join->save();
	}
	
	public function getSellingProductIds() : array
	{
		return MarketplaceIntegration_Join_Product::getProductIds( $this->getMarketplace() );
	}
	
	public function getBrands() : array
	{
		return MarketplaceIntegration_MarketplaceBrand::getBrands( $this->getMarketplace() );
	}
	
	public function getBrandForProduct( Product|Product_EShopData $product ) : ?MarketplaceIntegration_MarketplaceBrand
	{
		$mp_brand = MarketplaceIntegration_Join_Brand::get( $this->getMarketplace(), $product->getBrandId() );
		if(!$mp_brand) {
			return null;
		}

		return MarketplaceIntegration_MarketplaceBrand::get( $this->getMarketplace(), $mp_brand->getMarketplaceBrandId() );
	}
	
	public function getKindOfProductJoinForProduct( Product|Product_EShopData $product ) : ?MarketplaceIntegration_Join_KindOfProduct
	{
		
		$kind_id = $product->getKindId();
		
		$p_id = ($product instanceof  Product) ? $product->getId() : $product->getEntityId();
		
		$pj = $this->getProductJoin( $p_id );
		if($pj?->getAlternativeKindOfProductId()) {
			$kind_id = $pj->getAlternativeKindOfProductId();
		}
		
		return MarketplaceIntegration_Join_KindOfProduct::get( $this->getMarketplace(), $kind_id );
	}

	public function getCategories() : array
	{
		return MarketplaceIntegration_MarketplaceCategory::getCategories( $this->getMarketplace() );
	}
	
	public function getCategoryForProduct( Product|Product_EShopData $product ) : ?MarketplaceIntegration_MarketplaceCategory
	{
		$c_j = $this->getKindOfProductJoinForProduct( $product );
		
		if(!$c_j) {
			return null;
		}
		
		return MarketplaceIntegration_MarketplaceCategory::get(
			$this->getMarketplace(),
			$c_j->getMarketplaceCategoryId()
		);
	}
	
	public function getParametersForProduct( Product|Product_EShopData $product ) : array
	{
		$mp_c_join = $this->getKindOfProductJoinForProduct( $product );

		if(!$mp_c_join) {
			return [];
		}
		
		return MarketplaceIntegration_MarketplaceCategory_Parameter::getForCategory(
			$this->getMarketplace(),
			$mp_c_join->getMarketplaceCategoryId()
		);
		
	}
	
	public function getParamsEditForm( Product $product ) : ?Form
	{
		$key = $this->getMarketplace()->getKey().':'.$product->getId();
		
		if(!isset($this->param_edit_form[$key])) {
			$mc_category_join = $this->getKindOfProductJoinForProduct( $product );
			
			if(!$mc_category_join) {
				return null;
			}
			
			$parameters = MarketplaceIntegration_MarketplaceCategory_Parameter::getForCategory(
				$this->getMarketplace(),
				$mc_category_join->getMarketplaceCategoryId()
			);
			
			if(!$parameters) {
				return null;
			}
			
			uasort($parameters, function( MarketplaceIntegration_MarketplaceCategory_Parameter $a, MarketplaceIntegration_MarketplaceCategory_Parameter $b) {
				return strcasecmp($a->getName(), $b->getName());
			});
			
			$values = MarketplaceIntegration_MarketplaceCategory_Parameter_Value::getForProduct(
				$this->getMarketplace(),
				$mc_category_join->getMarketplaceCategoryId(),
				$product->getId()
			);
			
			$fields = [];
			foreach($parameters as $param_id=>$param) {
				
				$label = '';
				
				if($param->getRequirementLevel()) {
					$label .= '<span style="font-size: 0.6rem">['.Tr::_( $param->getRequirementLevel() ).']</span> ';
				}
				
				$label .= $param->getName().($param->getUnits()?' ('.$param->getUnits().')': '');
				$label .= ' <span style="font-size: 0.6rem;color: #666666">('.$param_id.')</span>';
				
				$label .= '<br><span style="font-size: 10px;color: #484848;font-style: italic">' .$param->getDescription().'</span>';
				
				
				$value = $values[$param_id]??null;
				if(!$value) {
					$value = new MarketplaceIntegration_MarketplaceCategory_Parameter_Value();
					$value->setMarketplace( $this->getMarketplace() );
					$value->setMarketplaceCategoryId( $mc_category_join );
					$value->setMarketplaceParameterId( $param_id );
					$value->setProductId( $product->getId() );
					$value->save();
					
				}
				
				switch( $param->getType() ) {
					case MarketplaceIntegration_MarketplaceCategory_Parameter::PARAM_TYPE_NUMBER:
						$field = new Form_Field_Float( $param_id );
						$field->setLabel( $label);
						$field->setDoNotTranslateLabel( true );
						$field->setDefaultValue( (float)$value->getValue() );
						$field->setFieldValueCatcher( function() use ($value, $field) {
							$value->setValue( $field->getValue() );
							$value->save();
						} );
						$fields[] = $field;
						
						break;
					case MarketplaceIntegration_MarketplaceCategory_Parameter::PARAM_TYPE_STRING:
						$field = new Form_Field_Input( $param_id );
						$field->setLabel( $label);
						$field->setDoNotTranslateLabel( true );
						$field->setDefaultValue( $value->getValue() );
						$field->setFieldValueCatcher( function() use ($value, $field) {
							$value->setValue( $field->getValue() );
							$value->save();
						} );
						
						$fields[] = $field;
						
						break;
					case MarketplaceIntegration_MarketplaceCategory_Parameter::PARAM_TYPE_OPTIONS:
						if($param->getMultipleValues()) {
							$field = new Form_Field_MultiSelect( $param_id );
							$field->input()->addCustomCssStyle("height:400px;");
							$field->setDefaultValue( $value->getValue()?explode('|', $value->getValue()):[] );
							$field->setFieldValueCatcher( function() use ($value, $field) {
								$value->setValue( implode('|', $field->getValue()) );
								$value->save();
							} );
						} else {
							$field = new Form_Field_Select( $param_id );
							$field->setDefaultValue( $value->getValue() );
							$field->setFieldValueCatcher( function() use ($value, $field) {
								$value->setValue( $field->getValue() );
								$value->save();
							} );
						}
						
						$field->setErrorMessages([
							Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
						]);
						$field->setLabel( $label);
						$field->setDoNotTranslateLabel( true );
						$field->setSelectOptions( $param->getOptions() );
						
						
						
						$fields[] = $field;
						
						break;
				}
			}
			
			$this->param_edit_form[$key] = new Form('marketplace_params', $fields);
		}
		
		
		return $this->param_edit_form[$key];
	}
	
	public function catchParamsEditForm( Product $product ) : bool
	{
		return $this->getParamsEditForm( $product )->catch();
	}
}