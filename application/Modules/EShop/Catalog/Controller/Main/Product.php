<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Catalog;


use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use Jet\MVC_Layout;
use JetApplication\Product_EShopData;


trait Controller_Main_Product
{

	protected static ?Product_EShopData $product = null;

	public function resolve_product( int $object_id, array $path ) : bool|string
	{
		MVC::getRouter()->setUsedUrlPath( $path[0]);

		static::$product = Product_EShopData::get($object_id);

		if(static::$product) {
			if(!static::$product->checkURL( $path[0] )) {
				return false;
			}

			$GET = Http_Request::GET();
			
			if(
				($category_id=$GET->getInt('c')) &&
				($category = static::$product->getCategories()[$category_id]??null)
			) {
				static::$category = $category;
			} else {
				foreach( static::$product->getCategories() as $category ) {
					static::$category = $category;
					break;
				}
			}
			
			
			return 'product_detail';
		} else {
			return 'unknown_product';
		}
	}

	public static function getProduct() : Product_EShopData
	{
		return static::$product;
	}

	public function unknown_product_Action() : void
	{
		ErrorPages::handleNotFound();
	}

	protected function _initBreadcrumbNavigation() : void
	{
		if(static::$category) {
			Navigation_Breadcrumb::setByCategory( static::$category );
		}

		Navigation_Breadcrumb::addURL(
			static::$product->getFullName()
		);
		MVC_Layout::getCurrentLayout()->setVar('title', static::$product->getFullName() );
	}



	public function product_detail_Action(): void
	{
		if(static::$product->isVariantMaster()) {
			foreach(static::$product->getVariants() as $variant) {
				Http_Headers::movedTemporary( $variant->getURL() );
			}
		}
		
		$this->view->setVar( 'product', static::$product );
		$this->_initBreadcrumbNavigation();

		
		$this->output( 'product/detail' );
	}
	
}

