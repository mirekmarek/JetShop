<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\Mvc_Page_Content;

abstract class Core_Exports_Module extends Application_Module
{

	public function getCode() : string
	{
		$code = $this->getModuleManifest()->getName();

		$prefix = Exports::getModuleNamePrefix();

		return substr($code, strlen($prefix));
	}

	abstract public function getTitle() : string;

	abstract public function isAllowedForShop( Shops_Shop $shop ) : bool;

	abstract public function joinCategoriesAllowed() : bool;

	abstract public function joinProductsAllowed() : bool;

	public function handleCategorySettings( Category $category, Shops_Shop $shop ): string
	{
		$content = new class extends Mvc_Page_Content {
			public function output( string $output ): void
			{
				$this->output = $output;
			}
		};

		$content->setModuleName( $this->getModuleManifest()->getName() );
		$content->setControllerAction( 'handleCategorySettings' );
		$content->setParameter('shop', $shop);
		$content->setParameter('category', $category);
		$content->setParameter('export', $this);

		$ns = $this->getModuleManifest()->getNamespace();

		$controller = $ns.'Controller_handleCategorySettings';

		$controller = new $controller( $content );
		$controller->dispatch();

		return $content->getOutput();
	}

	abstract public function handleProductSettings( Product $product, Shops_Shop $shop ) : string;

	abstract public function actualizeMetadata( Shops_Shop $shop ) : void;

	abstract public function generateExports() : void;

}