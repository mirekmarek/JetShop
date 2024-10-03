<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Exports;
use JetApplication\Shops;


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
		$selected_export_shop = null;
		
		$GET = Http_Request::GET();
		
		$export = $GET->getString('export');
		if($export) {
			$selected_export = Exports::getExportModule($export);
			
			if($selected_export) {
				$export_shop = $GET->getString('export_shop');
				if($export_shop) {
					$selected_export_shop = Shops::get($export_shop);
					if($selected_export_shop) {
						if(!$selected_export->isAllowedForShop($selected_export_shop)) {
							$selected_export = null;
							$selected_export_shop = null;
						}
					} else {
						$selected_export = null;
					}
				}
			}
		}
		
		$this->view->setVar('selected_export', $selected_export );
		$this->view->setVar('selected_export_shop', $selected_export_shop );
		
		$this->view->setVar('selected_export_code', $selected_export?->getCode());
		$this->view->setVar('selected_export_shop_key', $selected_export_shop?->getKey() );
		
		$this->view->setVar('item', $product);
		$this->output( 'edit/export' );
	}
	
	
}