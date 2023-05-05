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
use Jet\Tr;
use Jet\Http_Request;
use Jet\AJAX;
use JetApplication\Category;
use JetApplication\Navigation_Breadcrumb;

/**
 *
 */
trait Controller_Main_Category
{

	protected static ?Category $category = null;

	public function getControllerRouter_category( int $object_id, array $path ) : void
	{

		$category_URL_path = array_shift( $path );

		MVC::getRouter()->setUsedUrlPath($category_URL_path);

		static::$category = Category::get($object_id);


		if(static::$category) {

			if(static::$category->getURLPathPart()!=$category_URL_path ) {
				MVC::getRouter()->setIsRedirect( static::$category->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );
				return;
			}


			if(!static::$category->isActive()) {
				$this->router->setDefaultAction('category_not_active');
			} else {
				if(static::$category->getVisibleProductsCount()) {
					$listing = static::$category->getProductListing();
					$listing->disableNonRelevantFilters();
					
					if($path) {
						$listing->parseFilterUrl( $path );
						
						$qs = !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '';
						
						$valid_url = $listing->generateUrl(true).$qs;
						
						if($valid_url!=Http_Request::currentURL()) {
							MVC::getRouter()->setIsRedirect( $valid_url, Http_Headers::CODE_301_MOVED_PERMANENTLY );
							return;
						}
						
						MVC::getRouter()->setUsedUrlPath($category_URL_path.'/'.implode('/', $path));
					}
					
					
					$GET = Http_Request::GET();
					if($GET->exists('set_filter')) {
						$this->router->setDefaultAction('category_listing_set_filter');
					} else {
						$this->router->setDefaultAction('category_listing');
					}
					
				} else {
					$this->router->setDefaultAction('category_signpost');
				}
			}
		} else {
			$this->router->addAction('category_unknown')->setResolver(function() {
				return true;
			});
		}

	}

	public static function getCategory() : Category
	{
		return static::$category;
	}

	public function category_unknown_Action() : void
	{
		ErrorPages::handleNotFound();
	}

	public function category_not_active_Action() : void
	{
		Tr::setCurrentDictionary('category.not_active');
		$this->view->setVar('category', static::$category);
		$this->output('category/not_active');
	}

	public function category_listing_Action() : void
	{
		Tr::setCurrentDictionary('category.listing');

		Navigation_Breadcrumb::setByCategory( static::$category );

		$category = static::$category;

		$listing = $category->getProductListing();
		$listing->setFilterView( $this->view );

		$this->view->setVar('category', static::$category);
		$this->view->setVar('listing', $listing);

		$this->output('category/listing');
	}

	public function category_listing_set_filter_Action() : void
	{
		$POST = Http_Request::POST();

		$state_data = json_decode( $POST->getRaw('filter'), true );

		$listing = static::$category->getProductListing();
		$listing->setFilterView( $this->view );

		$listing->initByStateData( $state_data );

		$this->view->setVar('category', static::$category);
		$this->view->setVar('listing', $listing);

		$response = [
			'URL' => $listing->generateUrl(true),
			'filter_snippet' => $this->view->render('product_listing/filter'),
			'list_snippet' => $this->view->render('product_listing/list'),
			'state' => $listing->getStateData()
		];

		AJAX::commonResponse( $response );

	}

	public function category_signpost_Action(): void
	{
		Navigation_Breadcrumb::setByCategory( static::$category );

		Tr::setCurrentDictionary('category/signpost');

		$this->view->setVar('category', static::$category);

		$this->output('category/signpost');

	}

}
