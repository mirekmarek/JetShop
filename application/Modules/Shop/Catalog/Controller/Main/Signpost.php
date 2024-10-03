<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\Catalog;


use Jet\ErrorPages;
use Jet\Http_Headers;
use Jet\MVC;
use JetApplication\Signpost_ShopData;

/**
 *
 */
trait Controller_Main_Signpost
{
	
	protected static ?Signpost_ShopData $signpost = null;
	
	public function resolve_signpost( int $object_id, array $path ) : bool|string
	{
		
		$URL_path = array_shift( $path );
		
		MVC::getRouter()->setUsedUrlPath($URL_path);
		
		static::$signpost = Signpost_ShopData::get($object_id);
		
		
		if(static::$signpost) {
			
			if(static::$signpost->getURLPathPart()!=$URL_path ) {
				MVC::getRouter()->setIsRedirect( static::$signpost->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );
				return false;
			}
			
			if(!static::$signpost->isActive()) {
				return 'signpost_not_active';
			} else {
				return 'signpost';
			}
		} else {
			return 'signpost_unknown';
		}
		
	}
	
	public static function getSignpost() : Signpost_ShopData
	{
		return static::$signpost;
	}
	
	public function signpost_unknown_Action() : void
	{
		ErrorPages::handleNotFound();
	}
	
	public function signpost_not_active_Action() : void
	{
		Navigation_Breadcrumb::addURL(
			static::$signpost->getName()
		);
		
		$this->view->setVar('signpost', static::$signpost);
		$this->output('signpost/not_active');
	}
	
	public function signpost_Action(): void
	{
		Navigation_Breadcrumb::addURL(
			static::$signpost->getName()
		);
		
		$this->view->setVar('signpost', static::$signpost);
		
		$this->output('signpost/signpost');
		
	}
	
}
