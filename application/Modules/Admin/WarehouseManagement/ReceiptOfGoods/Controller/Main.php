<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\AJAX;
use Jet\Data_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Session;
use Jet\Tr;
use Jet\UI;
use Jet\UI_messages;
use JetApplication\WarehouseManagement_Item_Event;
use JetApplication\WarehouseManagement_Item_Event_Type;
use JetApplication\WarehouseManagement_Warehouse;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$GET = Http_Request::GET();
		
		$warehouses = WarehouseManagement_Warehouse::getScope();
		
		if(!$warehouses) {
			return;
		}

		$tabs = UI::tabs(
			tabs: $warehouses,
			tab_url_creator: function( $code ) {
				return Http_Request::currentURL(['warehouse'=>$code]);
			},
			selected_tab_id: $GET->getString('warehouse')
		);

		$warehouse_code = $tabs->getSelectedTabId();



		$new_item = new WarehouseManagement_Item_Event();
		$new_item->setEvent( WarehouseManagement_Item_Event_Type::RECEIPT_OF_GOODS );
		$new_item->setWarehouseCode( $warehouse_code );

		$session = new Session('whm_receipt_of_goods_'.$warehouse_code);
		$items = $session->getValue('items', []);
		register_shutdown_function(function() use ($session, &$items) {
			$session->setValue('items', $items);
		});


		$add_form = $new_item->getAddForm();
		$this->view->setVar('add_form', $add_form);
		$this->view->setVar('new_item', $new_item);
		$this->view->setVar('tabs', $tabs);
		$this->view->setVar('items', $items);


		if($add_form->catchInput()) {
			$ok = $add_form->validate();
			$snippets = [];

			$product_id = (int)$add_form->field('product_id')->getValue();

			if(!$product_id) {
				$ok = false;
				$add_form->setCommonMessage( UI_messages::createDanger(Tr::_('Please select product')) );
			}

			if($ok) {
				$add_form->catchFieldValues();

				$context = $new_item->getContext();
				$context_type = $new_item->getContextType();

				$items[] = $new_item;

				$new_item = new WarehouseManagement_Item_Event();
				$new_item->setContext($context);
				$new_item->setContextType($context_type);

				$add_form = $new_item->getAddForm();
				$this->view->setVar('add_form', $add_form);
			}

			$snippets['add_form_area'] = $this->view->render('add_form');

			if($ok) {
				$this->view->setVar('items', $items);
				$snippets['items_area'] = $this->view->render('items');
			}

			AJAX::operationResponse($ok, $snippets);
		}


		if($GET->exists('action')) {

			switch($GET->getString('action')) {
				case 'delete':
					$d_i = $GET->getInt('index');
					$new_items = [];
					foreach($items as $i=>$item) {
						if($i!=$d_i) {
							$new_items[] = $item;
						}
					}

					$items = $new_items;
					$this->view->setVar('items', $items);


				break;
				case 'done':

					foreach($items as $item) {
						$item->setIsNew();
						$item->setDateTime( Data_DateTime::now() );
						$item->save();
					}

					$items = [];
				break;
			}

			Http_Headers::reload([], ['action', 'index']);
		}


		$this->output('default');

	}
}