<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderDispatch\DoDispatch;

use Jet\AJAX;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_Managers;
use JetApplication\Carrier;
use JetApplication\Order;
use JetApplication\OrderDispatch;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplicationModule\Shop\Catalog\Navigation_Breadcrumb;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	
	protected ?OrderDispatch $dispatch = null;
	protected ?WarehouseManagement_Warehouse $warehouse = null;

	public function resolve(): bool|string
	{
		$GET = Http_Request::GET();
		if(
			($dispatch_id = $GET->getInt('id')) &&
			$dispatch = OrderDispatch::load( $dispatch_id )
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
		Admin_Managers::UI()->initBreadcrumb();
		
		$this->view->setVar('warehouse', $this->warehouse);
		
		$POST = Http_Request::POST();
		
		if(
			( $action = $POST->getString('list_action') )
		) {
			$dispatches = [];
			
			if(is_array($ids = $POST->getRaw('id'))) {
				foreach($ids as $id) {
					$dispatch = OrderDispatch::load( $id );
					if($dispatch) {
						$dispatches[$id] = $dispatch;
					}
				}
			}
			
			if($dispatches) {
				switch($action) {
					case 'create':
					case 'try_again':
						foreach( $dispatches as $dispatch ) {
							$dispatch->createConsignment();
						}
						break;
					case 'show_labels':
						if(
							( $carrier_code = $POST->getString('carrier', valid_values: array_keys(Carrier::getScope())) ) &&
							( $carrier = Carrier::get( $carrier_code ) )
						) {
							$error_message = '';
							$labels = $carrier->getPacketLabels( $dispatches, $error_message );
							
							if(!$labels) {
								UI_messages::danger( $error_message );
							} else {
								$labels->show();
							}
						}
						break;
					case 'show_delivery_notes':
						if(
							( $carrier_code = $POST->getString('carrier', valid_values: array_keys(Carrier::getScope())) ) &&
							( $carrier = Carrier::get( $carrier_code ) )
						) {
							$error_message = '';
							$labels = $carrier->getDeliveryNote( $dispatches, $error_message );
							
							if(!$labels) {
								UI_messages::danger( $error_message );
							} else {
								$labels->show();
							}
						}
						break;
					case 'sent':
						foreach( $dispatches as $dispatch ) {
							$dispatch->sent();
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
						'packet-list'          => $this->view->render( 'detail/packets/enabled/packet-list' ),
						'add-packet-form-area' => $this->view->render( 'detail/packets/enabled/add-packet-form' ),
					]
				);
			};
			
			$add_packet_form = $this->dispatch->getAddPacketForm();
			if($add_packet_form->catchInput()) {
				$ok = $this->dispatch->catchAddPacketForm();
				$response();
			}
			
			foreach($this->dispatch->getPackets() as $p) {
				$form = $p->getForm();
				if($form->catchInput()) {

					if($p->catchForm()) {
						$p->save();
						
						AJAX::operationResponse(
							success: true,
							snippets: [
								'main-toolbar'         => $this->view->render( 'detail/toolbar' ),
							]
						);
					} else {
						AJAX::operationResponse(
							success: false,
							snippets: [
								'main-toolbar'         => $this->view->render( 'detail/toolbar' ),
								'packet-list'          => $this->view->render( 'detail/packets/enabled/packet-list' ),
								'add-packet-form-area' => $this->view->render( 'detail/packets/enabled/add-packet-form' ),
							]
						);
						
					}
				}
				
				if($p->getRemoveForm()->catch()) {
					$this->dispatch->removePacket( $p->getId() );
					$response();
				}
			}
			
			if(
				$this->dispatch->getRecipientNoteForm()->catch() ||
				$this->dispatch->getDriverNoteForm()->catch() ||
				$this->dispatch->getAdditionalConsignmentParametersForm()->catch()
			) {
				AJAX::operationResponse(true);
			}
		}
		
		
		if( $action ) {
			switch($action) {
				case 'set_prepared':
					$this->dispatch->setIsPrepared();
					break;
				case 'create_consignment':
				case 'try_again':
					if( $this->dispatch->createConsignment() ) {
						UI_messages::success( Tr::_('Consignment has been created') );
					}
					break;
				case 'confirm_cancellation':
					$this->dispatch->confirmCancellation();
					break;
				case 'actualize_tracking':
					$error_message = '';
					if(!$this->dispatch->actualizeTracking( $error_message )) {
						UI_messages::danger( $error_message );
					}
					break;
				case 'get_label':
					if($this->dispatch->isConsignmentCreated()) {
						$error_message = '';
						$label = $this->dispatch->getLabel( $error_message );
						if(!$label) {
							UI_messages::danger( Tr::_('Unable to get label. Error: %error%', ['error'=>$error_message]) );
						} else {
							$label->show();
						}
						
					}
					break;
				case 'rollback':
					$this->dispatch->rollBack( Http_Request::GET()->getBool('ignore_error') );
					break;
				case 'cancel':
					if($this->dispatch->getIsCustom()) {
						$this->dispatch->cancel();
					}
					break;
				case 'goto_next_pending':
					$pending = OrderDispatch::getListOfPending( $this->warehouse );
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
					
					$dispatch = OrderDispatch::getByNumber( $q );
					
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
		
		Admin_Managers::UI()->initBreadcrumb();
		
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