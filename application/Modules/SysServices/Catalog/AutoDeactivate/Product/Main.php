<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Catalog\AutoDeactivate\Product;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Availabilities;
use JetApplication\Product;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


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
			'deactivate_after_sell_out' => true
		]);
		
		$avl = Availabilities::get('b2c_cz');
		
		foreach ($products as $product) {
			$no_avl = $product->getNumberOfAvailable( $avl );
			
			//echo $product->getAdminTitle().": ".$no_avl;
			
			echo mb_substr(mb_str_pad($product->getInternalName(), 50, ' '),0,50).'  ';
			echo mb_str_pad('('.$product->getInternalCode().', '.$product->getId().'): ', 40);
			echo $no_avl;
			echo "\n";
			
			$on_stock = $no_avl>0;
			
			if(!$on_stock) {
				if($product->isActive()) {
					$product->deactivate();
					echo "\tDEACTIVATED\n";
				}
			}
		}

	}
	
}