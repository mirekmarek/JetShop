<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Exports\Heureka;

use JetApplication\Exports_CategoriesCache;
use JetApplication\Exports_Module;
use JetApplication\Shops_Shop;
use SimpleXMLElement;

/**
 *
 */
class Main extends Exports_Module
{
	protected array $allowed_locales = ['cs_CZ', 'sk_SK'];

	protected ?array $export_categories = null;

	public function getTitle(): string
	{
		return 'Heureka';
	}

	public function isAllowedForShop( Shops_Shop $shop ): bool
	{
		$locale = $shop->getLocale()->toString();

		return in_array($locale, $this->allowed_locales);
	}

	public function joinCategoriesAllowed(): bool
	{
		return true;
	}

	public function joinProductsAllowed(): bool
	{
		return true;
	}

	public function __getExportCategories( Shops_Shop $shop ) : array
	{
		$context = stream_context_create(['http'=> ['timeout' => 2]]);
		$xml = @file_get_contents( Config::getCategoriesURL($shop) , false, $context);
		if(!$xml) {
			return [];
		}

		$xml = new SimpleXMLElement($xml);

		$export_categories = [];

		$addCategory = null;

		$addCategory = function( SimpleXMLElement $xml, $parent_id='', array $path=[] ) use ( &$addCategory, &$export_categories ) {
			foreach( $xml->CATEGORY as $node ) {

				$category = new HeurekaCategory( $node, $parent_id, $path );

				$export_categories[$category->getId()] = $category;
				$next_path = $path;

				$next_path[] = $category->getId();
				$addCategory($node, $category->getId(), $next_path);

			}

		};

		$addCategory( $xml );

		return $export_categories;
	}

		/**
	 * @param Shops_Shop $shop
	 *
	 * @return HeurekaCategory[]
	 */
	public function getExportCategories( Shops_Shop $shop ) : array
	{

		if($this->export_categories===null) {
			$this->export_categories = Exports_CategoriesCache::get( $this->getCode(), $shop, function() use ($shop) {
				return $this->__getExportCategories( $shop );
			});
		}


		return $this->export_categories;

	}

	public function actualizeMetadata( Shops_Shop $shop ): void
	{
		Exports_CategoriesCache::reset( $this->getCode(), $shop );
		Exports_CategoriesCache::get( $this->getCode(), $shop, function() use ($shop) {
			return $this->__getExportCategories( $shop );
		} );
	}

	public function generateExports( Shops_Shop $shop ): void
	{
		// TODO: Implement generateExports() method.
	}
}