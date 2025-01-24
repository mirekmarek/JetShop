<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\MarketplaceIntegration\Mall;

use Exception;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Availability;
use JetApplication\MarketplaceIntegration_Join_Brand;
use JetApplication\MarketplaceIntegration_Join_KindOfProduct;
use JetApplication\MarketplaceIntegration_MarketplaceBrand;
use JetApplication\MarketplaceIntegration_MarketplaceCategory;
use JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter;
use JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter_Value;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\Order_Event;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShop;


class Main extends MarketplaceIntegration_Module implements Admin_ControlCentre_Module_Interface, EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface
{
	public const IMPORT_SOURCE = 'Mall';
	
	use Admin_ControlCentre_Module_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	
	public function getTitle(): string
	{
		return 'Mall';
	}
	
	public function isAllowedForShop( EShop $eshop ): bool
	{
		return $this->getConfig( $eshop )->getClientId();
	}
	
	public function actualizeBrands( EShop $eshop ): void
	{
		if(!$this->isAllowedForShop($eshop)) {
			return;
		}
		
		$config = $this->getEshopConfig( $eshop );
		$client = $this->getClient( $eshop );
		
		$_brands = $client->getObject('brands', '');
		if(!$_brands) {
			throw new Exception('Unable to get brands! [1]');
		}
		
		
		foreach($_brands as $brand) {
			$m_b = MarketplaceIntegration_MarketplaceBrand::get( $eshop, $this->getCode(), $brand['brand_id'] );
			
			if($m_b) {
				if( $brand['title']!=$m_b->getName() ) {
					$m_b->setName( $brand['title'] );
					$m_b->save();
				}
				
			} else {
				$m_b = new MarketplaceIntegration_MarketplaceBrand();
				$m_b->setEshop( $eshop );
				$m_b->setMarketplaceCode( $this->getCode() );
				$m_b->setBrandId( $brand['brand_id'] );
				$m_b->setName( $brand['title'] );
				
				$m_b->save();
			}

		}
	}
	
	public function actualizeCategories( EShop $eshop ): void
	{
		if(!$this->isAllowedForShop($eshop)) {
			return;
		}
		
		$config = $this->getConfig( $eshop );
		$client = $this->getClient( $eshop );
		
		$_categories = $client->getObject('categories/tree/'.$config->getCountryCode(), '');
		if(!$_categories) {
			throw new Exception('Unable to get categories! [1]');
		}
		
		$categories = [];
		$getItems = null;
		$getItems = function( $item, $parent_id, array $parent_name ) use (&$getItems, &$categories) {

				if(!$item['categoryVisible']) {
					return;
				}
				$name = $parent_name;
			
				$name[] = $item['title'];
				
				if(
					isset($item['sapCategories'][0]['productTypeId'])
				) {
					$id = $item['sapCategories'][0]['productTypeId'];
					$parent_id = $id;
					
					$categories[$id] = [
						'parent_id' => '',
						'id' => $id,
						'name' => implode(' / ', $name),
						'menu_item_id' => $item['menuItemId'],
					];
					
				}
			
			if(isset($item['items'])) {
				foreach( $item['items'] as $item ) {
					$getItems($item, $parent_id, $name);
				}
			}
		};
		
		
		foreach($_categories[0]['items'] as $item) {
			$getItems( $item, '', [] );
		}
		
		foreach($categories as $cat) {
			
			$m_c = MarketplaceIntegration_MarketplaceCategory::get( $eshop, $this->getCode(), $cat['id'] );
			if($m_c) {
				$updated = false;
				
				if( $cat['menu_item_id']!=$m_c->getCategorySecondaryId() ) {
					$m_c->setCategorySecondaryId( $cat['menu_item_id'] );
					$updated = true;
				}
				
				if( $cat['parent_id']!=$m_c->getParentCategoryId() ) {
					$m_c->setParentCategoryId( $cat['parent_id'] );
					$updated = true;
				}
				
				if( $cat['name']!=$m_c->getName() ) {
					$m_c->setName( $cat['name'] );
					$updated = true;
				}
				
				if($updated) {
					$m_c->save();
				}
				
			} else {
				$m_c = new MarketplaceIntegration_MarketplaceCategory();
				$m_c->setEshop( $eshop );
				$m_c->setMarketplaceCode( $this->getCode() );
				$m_c->setCategoryId( $cat['id'] );
				
				$m_c->setCategorySecondaryId( $cat['menu_item_id'] );
				$m_c->setParentCategoryId( $cat['parent_id'] );
				$m_c->setName( $cat['name'] );
				
				$m_c->save();
			}
		}
		
	}
	
	public function actualizeCategory( EShop $eshop, string $category_id ) : void
	{
		$m_c = MarketplaceIntegration_MarketplaceCategory::get( $eshop, $this->getCode(), $category_id );
		if(!$m_c) {
			return;
		}
		
		$client = $this->getClient( $eshop );
		
		$collectParams = function( array $params ) {
			$res = [];
			foreach($params as $param) {
				
				if($param['unit']=='NULL') {
					$param['unit'] = '';
				}
				
				$param_id = $param['value'];
				$res[$param_id] = [
					'text' => $param['text']??'',
					'unit' => $param['unit']??'',
					'options' => []
				];
				
				if(isset($param['options'])) {
					foreach($param['options'] as $opt) {
						$res[$param_id]['options'][$opt['value']] = $opt['text'];
					}
				}
			}
			
			return $res;
		};
		

		$category_data = $client->getObject('categories/detail/'.$m_c->getCategorySecondaryId(), '');
		if(!$category_data) {
			return;
		}
		
		
		$params = $collectParams( $category_data['otherParameters'] );

		foreach($params as $param_id=>$param) {
			$e_p = MarketplaceIntegration_MarketplaceCategory_Parameter::get(
				$eshop,
				$this->getCode(),
				$m_c->getCategoryId(),
				$param_id
			);
			
			if( !$e_p ) {
				$e_p = new MarketplaceIntegration_MarketplaceCategory_Parameter();
				$e_p->setEshop( $eshop );
				$e_p->setMarketplaceCode( $this->getCode() );
				$e_p->setMarketplaceCategoryId( $m_c->getCategoryId() );
				$e_p->setMarketplaceParameterId( $param_id );
			}
			
			$e_p->setType(
				count($param['options']) ?
					MarketplaceIntegration_MarketplaceCategory_Parameter::PARAM_TYPE_OPTIONS
					:
					MarketplaceIntegration_MarketplaceCategory_Parameter::PARAM_TYPE_NUMBER
			);
			$e_p->setName( $param['text'] );
			$e_p->setOptions( $param['options'] );
			$e_p->setUnits( $param['unit'] );
			
			$e_p->save();
		}
	}
	
	public function getConfig( EShop $eshop ) : Config_PerShop|EShopConfig_ModuleConfig_PerShop
	{
		return $this->getEshopConfig( $eshop );
	}
	
	public function getClient( EShop $eshop ) : Client
	{
		$client = new Client( $this->getConfig( $eshop ) );
		
		return $client;
	}
	
	public function getLabels( EShop $eshop, bool $reload = false ) : array
	{

		$_labels = $this->getCache($eshop, 'labels');
		if(!$_labels || $reload) {
			$client = $this->getClient( $eshop );
			
			$_labels = $client->getObject('labels', '');
			$this->setCache($eshop, 'labels', $_labels);
		}
		
		
		
		$labels = [];
		foreach($_labels as $l) {
			if(!$l['visible']) {
				continue;
			}
			
			$labels[$l['id']] = $l['title'];
		}
		
		return $labels;
	}
	
	/** @noinspection SpellCheckingInspection */
	public function productToData( EShop $eshop, int $product_id, Pricelist $pricelist, Availability $availability ) : array
	{
		$product = Product_EShopData::get( $product_id, $eshop );
		
		$category_id = MarketplaceIntegration_Join_KindOfProduct::get(
			$this->getCode(),
			$eshop,
			$product->getKindId()
		);
		
		$parameters = [];
		$values = [];
		
		if($category_id) {
			$parameters = MarketplaceIntegration_MarketplaceCategory_Parameter::getForCategory(
				$eshop,
				$this->getCode(),
				$category_id
			);
			$values = MarketplaceIntegration_MarketplaceCategory_Parameter_Value::getForProduct(
				$eshop,
				$this->getCode(),
				$category_id,
				$product->getId()
			);
			
		}
		
		$product_data = [
			'id' => $product->getId(),
			'category_id' => (string)$category_id,
			
			'title' => $product->getName(),
			'shortdesc' => strip_tags( $product->getDescription() ),
			'longdesc' => $product->getDescription(),
			'priority' => $this->getProductCommonData_int($eshop, $product_id, 'priority'),
			'package_size' =>  $this->getProductCommonData_string($eshop, $product_id, 'package_size'),
			'free_delivery' => false,
			'vat' => $product->getVatRate( $pricelist ),
			'price' => $product->getPrice( $pricelist ),
			'dimensions' => [],
			'media' => [],
			'parameters' => [],
			'availability' => [
				'status' => $product->isActive(),
				'in_stock' => $product->getNumberOfAvailable( $availability )
			],
			'labels' => []
		];
		
		$brand = MarketplaceIntegration_Join_Brand::get(
			$this->getCode(),
			$eshop,
			$product->getBrandId()
		);
		
		if($brand) {
			$product_data['brand'] = $brand->toString();
		}
		
		if($product->getEan()) {
			$product_data['barcode'] = $product->getEan();
		}
		if(!isset($product_data['priority'])) {
			$product_data['priority'] = 1;
		}
		
		foreach( $product->getBoxes() as $box ) {
			if(empty($product_data['dimensions'])) {
				$product_data['dimensions'] = [
					'width' => 0,
					'height' => 0,
					'length' => 0,
					'weight' => 0
				];
			}
			$product_data['dimensions']['width'] += $box->getWeight();
			$product_data['dimensions']['height'] += $box->getHeight();
			$product_data['dimensions']['length'] += $box->getLength();
			$product_data['dimensions']['weight'] += $box->getWeight();
		}
		
		for($i=0;;$i++) {
			$img = $product->getImgUrl( $i );
			if(!$img) {
				break;
			}
			
			$product_data['media'][] = [
				'url' => rtrim($eshop->getHomepage()->getURL(), '/').$img,
				'main' => $i==0
			];
		}
		
		foreach($parameters as $param_id=>$param) {
			$value = $values[$param_id]??null;
			if(
				!$value ||
				$value->getValue()===''
			) {
				continue;
			}
			
			$product_data['parameters'][$param_id] = match ($param->getType()) {
				MarketplaceIntegration_MarketplaceCategory_Parameter::PARAM_TYPE_OPTIONS => explode( '|', $value->getValue() ),
				MarketplaceIntegration_MarketplaceCategory_Parameter::PARAM_TYPE_NUMBER => [(float)$value->getValue()],
				default => [$value->getValue()],
			};
		}
		
		$active_labels = $this->getProductCommonData( $eshop, $product_id, 'active_labels' );
		if($active_labels) {
			foreach($active_labels as $label) {
				/**
				 * @var ActiveLabel $label
				 */
				if($label->expired()) {
					continue;
				}
				
				$product_data['labels'][] = [
					'label' => $label->getId(),
					'from'  => $label->getFrom() ? $label->getFrom()->format('Y-m-d H:i:s') : '',
					'to'    => $label->getTill() ? $label->getTill()->format('Y-m-d H:i:s') : ''
				
				];
			}
		}
		
		foreach( $product_data['labels'] as $label_data ) {
			if($label_data['label']=='FDEL') {
				$product_data['free_delivery'] = true;
			}
		}

		
		if($product->isVariantMaster()) {
			$product_data['variants'] = [];
			$product_data['variable_parameters'] = [];
			
			
			foreach( $product->getVariants() as $variant ) {
				if(
					$variant->isActive() &&
					$this->getProductIsSelling( $eshop, $variant->getId() )
				) {
					$product_data['variants'][] = $this->productToData(
						$eshop,
						$variant->getId(),
						$pricelist,
						$availability
					);
				}
				
			}
			
			$_v_params = [];
			
			foreach( $product_data['variants'] as $variant ) {
				foreach( $variant['parameters'] as $parameter=>$values ) {
					$values = implode('|', $values);
					
					if(!isset($_v_params[$parameter])) {
						$_v_params[$parameter] = [];
					}
					
					if(!in_array( $values, $_v_params[$parameter] )) {
						$_v_params[$parameter][] = $values;
					}
					
				}
			}
			
			$allowed = ['COLOR', 'SIZE'];
			
			foreach( $_v_params as $parameter=>$values ) {
				if(
					count($values)>1 &&
					in_array($parameter, $allowed)
				) {
					$product_data['variable_parameters'][] = $parameter;
					
					unset($product_data['parameters'][$parameter]);
				}
			}
			
		}
		
		
		return $product_data;
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MARKET_PLACE_INTEGRATION;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Mall';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'cloud-arrow-up';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
	
	public function handleOrderEvent( Order_Event $order_event ): bool
	{
		// TODO: Implement handleOrderEvent() method.
		return true;
	}
	
}