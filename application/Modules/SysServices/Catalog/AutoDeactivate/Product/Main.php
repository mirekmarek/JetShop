<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Catalog\AutoDeactivate\Product;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Product;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;
use JetApplication\WarehouseManagement_StockCard;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$auto_append = new SysServices_Definition(
			module: $this,
			name: Tr::_('Catalog - automatic deactivation of sold-out sale products'),
			description: Tr::_('Automatically deactivates products that are marked as on sale, have the automatic deactivation function enabled and are no longer in stock.'),
			service_code: 'deactivate_sold_out_sale_products',
			service: function() {
				$this->deactivateSoldOutSaleProducts();
			}
		);
		
		return [
			$auto_append
		];
	}
	
	public function deactivateSoldOutSaleProducts() : void
	{
		$products = Product::fetchInstances([
			'is_active' => true,
			'AND',
			'is_sale' => true,
			'AND',
			'deactivate_after_sell_out' => true
		]);
		
		foreach ($products as $product) {
			echo $product->getAdminTitle().": ";
			
			$on_stock = false;
			$stock_cards = WarehouseManagement_StockCard::getCardsByProduct( $product->getId() );
			foreach( $stock_cards as $stock_card ) {
				if($stock_card->getInStock()) {
					$on_stock = true;
					break;
				}
			}
			
			if(!$on_stock) {
				echo "\tDEACTIVATED\n";
				$product->deactivate();
			} else {
				echo "\tstill on stock\n";
			}
		}

	}
	
}