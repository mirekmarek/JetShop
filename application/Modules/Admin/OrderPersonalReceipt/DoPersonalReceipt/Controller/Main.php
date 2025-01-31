<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderPersonalReceipt\DoPersonalReceipt;


use Jet\AJAX;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use JetApplication\Order;
use JetApplication\OrderPersonalReceipt;
use JetApplication\WarehouseManagement_Warehouse;
use Jet\Navigation_Breadcrumb;


class Controller_Main extends MVC_Controller_Default
{
	
	protected ?OrderPersonalReceipt $dispatch = null;
	protected ?WarehouseManagement_Warehouse $warehouse = null;

	public function resolve(): bool|string
	{
		$GET = Http_Request::GET();
		if(
			($dispatch_id = $GET->getInt('id')) &&
			$dispatch = OrderPersonalReceipt::load( $dispatch_id )
		) {
			$this->dispatch = $dispatch;
			$this->warehouse = $dispatch->getWarehouse();
			
			return 'detail';
		}
		
		$default_wh_id = 0;
		foreach(WarehouseManagement_Warehouse::getScope() as $id=>$name) {
			$default_wh_id = $id;
			break;
		}
		
		$wh_id = $GET->getString('warehouse', default_value: $default_wh_id, valid_values: array_keys(WarehouseManagement_Warehouse::getScope()));
		
		$this->warehouse = WarehouseManagement_Warehouse::get( $wh_id );
		
		return 'list';
	}
	
	public function list_Action() : void
	{
		$this->view->setVar('warehouse', $this->warehouse);
		
		$POST = Http_Request::POST();
		
		if(
			( $action = $POST->getString('list_action') )
		) {
			$dispatches = [];
			
			if(is_array($ids = $POST->getRaw('id'))) {
				foreach($ids as $id) {
					$dispatch = OrderPersonalReceipt::load( $id );
					if($dispatch) {
						$dispatches[$id] = $dispatch;
					}
				}
			}
			
			if($dispatches) {
				switch($action) {
					case 'prepared':
						foreach( $dispatches as $dispatch ) {
							$dispatch->prepared();
						}
						
						break;
					default:
						var_dump($action);
						die();
				}
			}
			
			Http_Headers::reload();
		}

		
		$this->output('list');
	}
	
	public function detail_Action() : void
	{
		$this->view->setVar('dispatch', $this->dispatch);
		
		$action = Http_Request::GET()->getString('action');
		
		
		if($this->dispatch->isEditable()) {
			$response = function( bool $ok=true ) {
				AJAX::operationResponse(
					success: $ok,
					snippets: [
						'main-toolbar'         => $this->view->render( 'detail/toolbar' ),
					]
				);
			};
		}
		
		
		if( $action ) {
			switch($action) {
				case 'set_preparation_started':
					$this->dispatch->preparationStarted();
					break;
				case 'set_prepared':
					$this->dispatch->prepared();
					break;
				case 'confirm_cancellation':
					$this->dispatch->confirmCancellation();
					break;
				case 'rollback':
					$this->dispatch->rollBack();
					break;
				case 'handed_over':
					$this->dispatch->handedOver();
					break;
				case 'cancel':
					if($this->dispatch->getIsCustom()) {
						$this->dispatch->cancel();
					}
					break;
				case 'goto_next_pending':
					$pending = OrderPersonalReceipt::getListOfPending( $this->warehouse );
					foreach($pending as $p) {
						AJAX::commonResponse([
							'URL' => Http_Request::currentUrl(['id'=>$p->getId()], ['action'])
						]);
						
					}
					
					AJAX::commonResponse([
						'done' => true
					]);
					
					break;
				case 'search':
					
					$q = Http_Request::GET()->getString('q');
					
					$dispatch = OrderPersonalReceipt::getByNumber( $q );
					
					if($dispatch) {
						AJAX::commonResponse([
							'found' => true,
							'URL' => Http_Request::currentUrl(['id'=>$dispatch->getId()], ['action'])
						]);
					} else {
						AJAX::commonResponse([
							'found' => false
						]);
					}
					break;
			}
			
			Http_Headers::reload(unset_GET_params: ['action']);
		}
		
		
		
		if(
			$this->dispatch->getOurNoteForm()->catch()
		) {
			AJAX::operationResponse(true);
		}
		
		Navigation_Breadcrumb::addURL(
			title: $this->dispatch->getWarehouse()->getInternalName(),
			URL: Http_Request::currentURI(unset_GET_params: ['id'])
		);
		
		if(
			$this->dispatch->getOrderId() &&
			($order_number = Order::get($this->dispatch->getOrderId())?->getNumber())
		) {
			Navigation_Breadcrumb::addURL( Tr::_('Dispatch %dn% (order %on%)', [
				'dn' => $this->dispatch->getNumber(),
				'on' => $order_number
			]) );
		} else {
			Navigation_Breadcrumb::addURL( Tr::_('Dispatch %dn%', [
				'dn' => $this->dispatch->getNumber()
			]) );
		}
		
		
		$this->output('detail');
	}
	
	
}