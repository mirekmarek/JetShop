<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Module;
use Jet\MVC_Page_Content;

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

	public function handleKindOfProductSettings( KindOfProduct $kind_of_product, Shops_Shop $shop ): string
	{
		$content = new class extends MVC_Page_Content {
			public function output( string $output ): void
			{
				$this->output = $output;
			}
		};

		$content->setModuleName( $this->getModuleManifest()->getName() );
		$content->setControllerAction( 'handleKindOfProductSettings' );
		$content->setParameter('shop', $shop);
		$content->setParameter('kind_of_product', $kind_of_product);
		$content->setParameter('export', $this);

		$ns = $this->getModuleManifest()->getNamespace();

		$controller = $ns.'Controller_handleKindOfProductSettings';

		$controller = new $controller( $content );
		$controller->dispatch();

		return $content->getOutput();
	}

	public function handleProductSettings( Product $product, Shops_Shop $shop ): string
	{
		// TODO: Implement handleProductSettings() method.
		return '';
	}

	abstract public function actualizeMetadata( Shops_Shop $shop ) : void;

	abstract public function generateExports( Shops_Shop $shop ) : void;

}