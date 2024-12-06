<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Exports;
use JetApplication\EShops;
use JetApplication\Product;


trait Controller_Main_Edit_Export
{
	
	
	public function export_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Export') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		
		$selected_export = null;
		$selected_export_eshop = null;
		
		$GET = Http_Request::GET();
		
		$export = $GET->getString('export');
		if($export) {
			$selected_export = Exports::getExportModule($export);
			
			if($selected_export) {
				$export_eshop = $GET->getString('export_eshop');
				if($export_eshop) {
					$selected_export_eshop = EShops::get($export_eshop);
					if($selected_export_eshop) {
						if(!$selected_export->isAllowedForShop($selected_export_eshop)) {
							$selected_export = null;
							$selected_export_eshop = null;
						}
					} else {
						$selected_export = null;
					}
				}
			}
		}
		
		$this->view->setVar('selected_export', $selected_export );
		$this->view->setVar('selected_export_eshop', $selected_export_eshop );
		
		$this->view->setVar('selected_export_code', $selected_export?->getCode());
		$this->view->setVar('selected_export_eshop_key', $selected_export_eshop?->getKey() );
		
		$this->view->setVar('item', $product);
		$this->output( 'edit/export' );
	}
	
	
}