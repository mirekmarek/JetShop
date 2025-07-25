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
use JetApplication\MarketplaceIntegration_Join_Cache;
use JetApplication\MarketplaceIntegration_Join_ProductCommonData;
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

abstract class Core_MarketplaceIntegration_Module extends Application_Module
{
	
	/**
	 * @var Form[]
	 */
	protected array $param_edit_form = [];

	public function getCode() : string
	{
		$code = $this->getModuleManifest()->getName();

		$prefix = MarketplaceIntegration::getModuleNamePrefix();

		return substr($code, strlen($prefix));
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
	
	public function handleKindOfProductSettings( KindOfProduct $kind_of_product, EShop $eshop ): void
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
			action: function () use ($controller, $content, $kind_of_product, $eshop, $mp) {
				$controller = new $controller( $content );
				$controller->init(
					$kind_of_product,
					$eshop,
					$mp
				);
				$content->setControllerAction( $controller->resolve() );
				$controller->dispatch();
			}
		);
		
	}
	
	public function handleProductSettings( Product $product, EShop $eshop ): void
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
			action: function () use ($controller, $content, $product, $eshop, $mp) {
				$controller = new $controller( $content );
				$controller->init(
					$product,
					$eshop,
					$mp
				);
				$content->setControllerAction( $controller->resolve() );
				$controller->dispatch();
			}
		);
	}
	
	public function handleBrandSettings( Brand $brand, EShop $eshop ): void
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
			action: function () use ($controller, $content, $brand, $eshop, $mp) {
				$controller = new $controller( $content );
				$controller->init(
					$brand,
					$eshop,
					$mp
				);
				$content->setControllerAction( $controller->resolve() );
				$controller->dispatch();
			}
		);
		
	}
	
	
	public function handleOrderDetail( Order $order ): void
	{
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
	
	
	abstract public function actualizeBrands( EShop $eshop ) : void;
	
	abstract public function actualizeCategories( EShop $eshop ) : void;
	
	abstract public function actualizeCategory( EShop $eshop, string $category_id ) : void;
	
	public function getImportSource() : string
	{
		return static::IMPORT_SOURCE;
	}
	
	public function orderIsRelevant( Order $order ) : bool
	{
		return ($order->getImportSource()==$this->getImportSource());
	}
	
	abstract public function handleOrderEvent( Order_Event $order_event ) : bool;
	
	
	public function getCache( EShop $eshop, string $cache_key ) : mixed
	{
		$cd = MarketplaceIntegration_Join_Cache::get( $this->getCode(), $eshop, $cache_key );
		return $cd?->getCacheData();
	}
	
	public function setCache( EShop $eshop, string $cache_key, mixed $cache_data ) : void
	{
		$cd = MarketplaceIntegration_Join_Cache::get( $this->getCode(), $eshop, $cache_key );
		if(!$cd) {
			$cd = new MarketplaceIntegration_Join_Cache();
			$cd->setEshop( $eshop );
			$cd->setMarketplaceCode( $this->getCode() );
			$cd->setCacheKey( $cache_key );
		}
		
		$cd->setCacheData( $cache_data );
		$cd->save();
	}
	
	
	
	
	public function getProductIsSelling( EShop $eshop, int $product_id ) : bool
	{
		return (bool)MarketplaceIntegration_Join_Product::get( $this->getCode(), $eshop, $product_id );
	}
	
	public function getProductCommonData( EShop $eshop, int $product_id, string $common_data_key ) : mixed
	{
		$cd = MarketplaceIntegration_Join_ProductCommonData::get( $this->getCode(), $eshop, $product_id, $common_data_key );
		
		return $cd?->getCommonData();
	}
	
	public function getProductCommonData_int( EShop $eshop, int $product_id, string $common_data_key ) : ?int
	{
		$co = $this->getProductCommonData( $eshop, $product_id, $common_data_key );

		if(is_array($co)) {
			return (int)$co[0];
		}
		
		return null;
	}
	
	public function getProductCommonData_float( EShop $eshop, int $product_id, string $common_data_key ) : ?float
	{
		$co = $this->getProductCommonData( $eshop, $product_id, $common_data_key );
		
		if(is_array($co)) {
			return (float)$co[0];
		}
		
		return null;
	}
	
	public function getProductCommonData_string( EShop $eshop, int $product_id, string $common_data_key ) : ?string
	{
		$co = $this->getProductCommonData( $eshop, $product_id, $common_data_key );
		
		if(is_array($co)) {
			return (string)$co[0];
		}
		
		return null;
	}
	
	
	public function setProductCommonData( EShop $eshop, int $product_id, string $common_data_key, mixed $common_data ) : void
	{
		$cd = MarketplaceIntegration_Join_ProductCommonData::get( $this->getCode(), $eshop, $product_id, $common_data_key );
		if(!$cd) {
			$cd = new MarketplaceIntegration_Join_ProductCommonData();
			$cd->setEshop( $eshop );
			$cd->setMarketplaceCode( $this->getCode() );
			$cd->setProductId( $product_id );
			$cd->setCommonDataKey( $common_data_key );
		}
		
		$cd->setCommonData( $common_data );
		$cd->save();
	}
	
	public function stopSelling( EShop $eshop, int $product_id ) : void
	{
		$join = MarketplaceIntegration_Join_Product::get( $this->getCode(), $eshop, $product_id );
		$join?->delete();
	}
	
	public function startSelling( EShop $eshop, int $product_id ) : void
	{
		$join = MarketplaceIntegration_Join_Product::get( $this->getCode(), $eshop, $product_id );
		if($join) {
			return;
		}
		
		$join = new MarketplaceIntegration_Join_Product();
		$join->setEshop( $eshop );
		$join->setMarketplaceCode( $this->getCode() );
		$join->setProductId( $product_id );
		$join->save();
	}
	
	public function getSellingProductIds( EShop $eshop ) : array
	{
		return MarketplaceIntegration_Join_Product::getProductIds(
			$this->getCode(),
			$eshop
		);
	}
	
	public function getBrands( EShop $eshop ) : array
	{
		return MarketplaceIntegration_MarketplaceBrand::getBrands( $eshop, $this->getCode() );
	}

	public function getCategories( EShop $eshop ) : array
	{
		return MarketplaceIntegration_MarketplaceCategory::getCategories( $eshop, $this->getCode() );
	}
	
	public function getCategory( EShop $eshop, Product $product ) : ?MarketplaceIntegration_MarketplaceCategory
	{
		$category_id = MarketplaceIntegration_Join_KindOfProduct::get(
			$this->getCode(),
			$eshop,
			$product->getKindId()
		);
		
		if(!$category_id) {
			return null;
		}
		
		return MarketplaceIntegration_MarketplaceCategory::get(
			$eshop,
			$this->getCode(),
			$category_id
		);
	}
	
	public function getParamsEditForm( EShop $eshop, Product $product ) : ?Form
	{
		$key = $eshop->getKey().':'.$product->getId();
		
		if(!isset($this->param_edit_form[$key])) {
			$category_id = MarketplaceIntegration_Join_KindOfProduct::get(
				$this->getCode(),
				$eshop,
				$product->getKindId()
			);
			
			if(!$category_id) {
				return null;
			}
			
			$parameters = MarketplaceIntegration_MarketplaceCategory_Parameter::getForCategory(
				$eshop,
				$this->getCode(),
				$category_id
			);
			
			if(!$parameters) {
				return null;
			}
			
			uasort($parameters, function( MarketplaceIntegration_MarketplaceCategory_Parameter $a, MarketplaceIntegration_MarketplaceCategory_Parameter $b) {
				return strcasecmp($a->getName(), $b->getName());
			});
			
			$values = MarketplaceIntegration_MarketplaceCategory_Parameter_Value::getForProduct(
				$eshop,
				$this->getCode(),
				$category_id,
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
					$value->setEshop( $eshop );
					$value->setMarketplaceCode( $this->getCode() );
					$value->setMarketplaceCategoryId( $category_id );
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
	
	public function catchParamsEditForm( EShop $eshop, Product $product ) : bool
	{
		return $this->getParamsEditForm( $eshop, $product )->catch();
	}
}