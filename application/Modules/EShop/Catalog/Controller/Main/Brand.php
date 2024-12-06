<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\EShop\Catalog;


use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\MVC;
use JetApplication\Brand_EShopData;

/**
 *
 */
trait Controller_Main_Brand
{
	
	protected static ?Brand_EShopData $brand = null;
	
	public function resolve_brand( int $object_id, array $path ) : bool|string
	{
		
		$URL_path = array_shift( $path );
		
		MVC::getRouter()->setUsedUrlPath($URL_path);
		
		static::$brand = Brand_EShopData::get($object_id);
		
		
		if(static::$brand) {
			
			if(static::$brand->getURLPathPart()!=$URL_path ) {
				MVC::getRouter()->setIsRedirect( static::$brand->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );
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
		
		$this->view->setVar('brand', static::$brand);
		$this->output('brand/not_active');
	}
	
	public function brand_Action(): void
	{
		Navigation_Breadcrumb::addURL(
			static::$brand->getName()
		);
		
		$this->view->setVar('brand', static::$brand);
		
		$this->output('brand/brand');
		
	}
	
}
