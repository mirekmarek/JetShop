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

use JetApplication\Exports_Definition;
use JetApplication\Product;
use JetApplication\Exports;
use JetApplication\EShop;
use JetApplication\KindOfProduct;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_Module_Controller_KindOfProductSettings;
use JetApplication\Exports_Module_Controller_ProductSettings;
use JetApplication\Exports_Join_Cache;
use JetApplication\Exports_Join_Product;
use JetApplication\Exports_Join_ProductCommonData;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_ExportCategory_Parameter;
use JetApplication\Exports_ExportCategory_Parameter_Value;

abstract class Core_Exports_Module extends Application_Module
{
	/**
	 * @var Form[]
	 */
	protected array $param_edit_form = [];
	
	public function getCode() : string
	{
		$code = $this->getModuleManifest()->getName();
		
		$prefix = Exports::getModuleNamePrefix();
		
		return substr($code, strlen($prefix));
	}
	
	abstract public function getTitle() : string;
	
	abstract public function isAllowedForShop( EShop $eshop ) : bool;
	
	public function handleKindOfProductSettings( KindOfProduct $kind_of_product, EShop $eshop ): void
	{
		$content = new MVC_Page_Content();
		
		$content->setModuleName( $this->getModuleManifest()->getName() );
		
		/**
		 * @var Exports_Module_Controller_KindOfProductSettings $controller
		 */
		$ns = $this->getModuleManifest()->getNamespace();
		$controller = $ns.'Controller_KindOfProductSettings';
		
		$controller = new $controller( $content );
		/** @noinspection PhpParamsInspection */
		$controller->init(
			$kind_of_product,
			$eshop,
			$this
		);
		$content->setControllerAction( $controller->resolve() );
		$controller->dispatch();
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
		 * @var Exports_Module_Controller_ProductSettings $controller
		 */
		$ns = $this->getModuleManifest()->getNamespace();
		$controller = $ns.'Controller_ProductSettings';
		
		$controller = new $controller( $content );
		/** @noinspection PhpParamsInspection */
		$controller->init(
			$product,
			$eshop,
			$this
		);
		$content->setControllerAction( $controller->resolve() );
		$controller->dispatch();
	}
	
	
	abstract public function actualizeCategories( EShop $eshop ) : void;
	
	abstract public function actualizeCategory( EShop $eshop, string $category_id ) : void;
	
	
	public function getCache( EShop $eshop, string $cache_key ) : mixed
	{
		$cd = Exports_Join_Cache::get( $this->getCode(), $eshop, $cache_key );
		return $cd?->getCacheData();
	}
	
	public function setCache( EShop $eshop, string $cache_key, mixed $cache_data ) : void
	{
		$cd = Exports_Join_Cache::get( $this->getCode(), $eshop, $cache_key );
		if(!$cd) {
			$cd = new Exports_Join_Cache();
			$cd->setEshop( $eshop );
			$cd->setExportCode( $this->getCode() );
			$cd->setCacheKey( $cache_key );
		}
		
		$cd->setCacheData( $cache_data );
		$cd->save();
	}
	
	
	
	
	public function getProductIsSelling( EShop $eshop, int $product_id ) : bool
	{
		return (bool)Exports_Join_Product::get( $this->getCode(), $eshop, $product_id );
	}
	
	public function getProductCommonData( EShop $eshop, int $product_id, string $common_data_key ) : mixed
	{
		$cd = Exports_Join_ProductCommonData::get( $this->getCode(), $eshop, $product_id, $common_data_key );
		
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
		$cd = Exports_Join_ProductCommonData::get( $this->getCode(), $eshop, $product_id, $common_data_key );
		if(!$cd) {
			$cd = new Exports_Join_ProductCommonData();
			$cd->setEshop( $eshop );
			$cd->setExportCode( $this->getCode() );
			$cd->setProductId( $product_id );
			$cd->setCommonDataKey( $common_data_key );
		}
		
		$cd->setCommonData( $common_data );
		$cd->save();
	}
	
	public function stopSelling( EShop $eshop, int $product_id ) : void
	{
		$join = Exports_Join_Product::get( $this->getCode(), $eshop, $product_id );
		$join?->delete();
	}
	
	public function startSelling( EShop $eshop, int $product_id ) : void
	{
		$join = Exports_Join_Product::get( $this->getCode(), $eshop, $product_id );
		if($join) {
			return;
		}
		
		$join = new Exports_Join_Product();
		$join->setEshop( $eshop );
		$join->setExportCode( $this->getCode() );
		$join->setProductId( $product_id );
		$join->save();
	}
	
	public function getSellingProductIds( EShop $eshop ) : array
	{
		return Exports_Join_Product::getProductIds(
			$this->getCode(),
			$eshop
		);
	}
	
	
	public function getCategories( EShop $eshop ) : array
	{
		return Exports_ExportCategory::getCategories( $eshop, $this->getCode() );
	}
	
	public function getCategory( EShop $eshop, Product $product ) : ?Exports_ExportCategory
	{
		$category_id = Exports_Join_KindOfProduct::get(
			$this->getCode(),
			$eshop,
			$product->getKindId()
		);
		
		if(!$category_id) {
			return null;
		}
		
		return Exports_ExportCategory::get(
			$eshop,
			$this->getCode(),
			$category_id
		);
	}
	
	public function getParamsEditForm( EShop $eshop, Product $product ) : ?Form
	{
		$key = $eshop->getKey().':'.$product->getId();
		
		if(!isset($this->param_edit_form[$key])) {
			$category_id = Exports_Join_KindOfProduct::get(
				$this->getCode(),
				$eshop,
				$product->getKindId()
			);
			
			if(!$category_id) {
				return null;
			}
			
			$parameters = Exports_ExportCategory_Parameter::getForCategory(
				$eshop,
				$this->getCode(),
				$category_id
			);
			
			if(!$parameters) {
				return null;
			}
			
			$values = Exports_ExportCategory_Parameter_Value::getForProduct(
				$eshop,
				$this->getCode(),
				$category_id,
				$product->getId()
			);
			
			$fields = [];
			foreach($parameters as $param_id=>$param) {
				
				$label = $param->getName().($param->getUnits()?' ('.$param->getUnits().')': '').'<br><span style="font-size: 10px;color: #666666">'.$param_id.'</span>';
				$value = $values[$param_id]??null;
				if(!$value) {
					$value = new Exports_ExportCategory_Parameter_Value();
					$value->setEshop( $eshop );
					$value->setExportCode( $this->getCode() );
					$value->setExportCategoryId( $category_id );
					$value->setExportParameterId( $param_id );
					$value->setProductId( $product->getId() );
					$value->save();
					
				}
				
				switch( $param->getType() ) {
					case Exports_ExportCategory_Parameter::PARAM_TYPE_NUMBER:
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
					case Exports_ExportCategory_Parameter::PARAM_TYPE_STRING:
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
					case Exports_ExportCategory_Parameter::PARAM_TYPE_OPTIONS:
						$field = new Form_Field_MultiSelect( $param_id );
						$field->setErrorMessages([
							Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
						]);
						$field->setLabel( $label);
						$field->setDoNotTranslateLabel( true );
						$field->setSelectOptions( $param->getOptions() );
						$field->setDefaultValue( $value->getValue()?explode('|', $value->getValue()):[] );
						$field->setFieldValueCatcher( function() use ($value, $field) {
							$value->setValue( implode('|', $field->getValue()) );
							$value->save();
						} );
						$field->input()->addCustomCssStyle("height:400px;");
						
						
						
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
	
	/**
	 * @return Exports_Definition[]
	 */
	abstract public function getExportsDefinitions() : array;

}