<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Catalog;



use Jet\ErrorPages;
use Jet\MVC;
use Jet\MVC_Layout;
use JetApplication\Brand_EShopData;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_EShop_ProductListing;
use JetApplication\Product;


trait Controller_Main_Brand
{
	
	protected static ?Brand_EShopData $brand = null;
	
	public function resolve_brand( int $object_id, array $path ) : bool|string
	{
		
		$URL_path = array_shift( $path );
		
		MVC::getRouter()->setUsedUrlPath($URL_path);
		
		static::$brand = Brand_EShopData::get($object_id);
		
		
		if(static::$brand) {
			
			
			if(!static::$brand->checkURL( $URL_path )) {
				return false;
			}
			if(!static::$brand->isActive()) {
				return 'brand_not_active';
			} else {
				return 'brand';
			}
		} else {
			return 'brand_unknown';
		}
		
	}
	
	public static function getBrand() : Brand_EShopData
	{
		return static::$brand;
	}
	
	public function brand_unknown_Action() : void
	{
		ErrorPages::handleNotFound();
	}
	
	public function brand_not_active_Action() : void
	{
		Navigation_Breadcrumb::addURL(
			static::$brand->getName()
		);
		MVC_Layout::getCurrentLayout()->setVar('title', static::$brand->getName() );
		
		$this->view->setVar('brand', static::$brand);
		$this->output('brand/not_active');
	}
	
	public function brand_Action(): void
	{
		Navigation_Breadcrumb::addURL(
			static::$brand->getName()
		);
		MVC_Layout::getCurrentLayout()->setVar('title', static::$brand->getName() );
		
		$liting = Application_Service_EShop::ProductListing();
		/**
		 * @var Application_Service_EShop_ProductListing $liting
		 */
		$liting->init(
			Product::dataFetchCol(
				select: ['id'],
				where: ['brand_id' => static::$brand->getId()],
				raw_mode: true
			)
		);
		$liting->handle();
		
		$this->view->setVar('listing', $liting);
		$this->view->setVar('brand', static::$brand);
		
		$this->output('brand/brand');
		
	}
	
}
