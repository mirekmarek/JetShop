<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\Paid;

use JetApplication\EMail_TemplateProvider;
use JetApplication\MarketplaceIntegration;
use JetApplication\Order;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\Order_Status_Delivered;
use JetApplication\Order_Status_ReadyForDispatch;
use JetApplication\Order_Status_WaitingForGoodsToBeStocked;
use JetApplication\Product_Availability;


class Main extends Order_Event_HandlerModule implements EMail_TemplateProvider
{
	
	public function handleExternals(): bool
	{
		$res = MarketplaceIntegration::handleOrderEvent( $this->event );
		if($res!==null) {
			return $res;
		}
		
		return true;
	}

	public function handleInternals(): bool
	{
		if(!$this->order->getPaid()) {
			$this->order->setPaid( true );
			$this->order->save();
		}
		
		if($this->order->getDeliveryMethod()->isEDelivery()) {
			$this->handleVirtualProducts();
			
			$this->order->setStatus( Order_Status_Delivered::get() );
		} else {
			$all_items_available = true;
			foreach($this->order->getItems() as $item) {
				if(!$item->isPhysicalProduct()) {
					continue;
				}
				
				$avl = Product_Availability::get( $this->order->getAvailability(), $item->getItemId() );
				
				if($avl->getNumberOfAvailable()<$item->getNumberOfUnits()) {
					$all_items_available = false;
					break;
				}
			}
			
			if($all_items_available) {
				$this->order->setStatus( Order_Status_ReadyForDispatch::get() );
			} else {
				$this->order->setStatus( Order_Status_WaitingForGoodsToBeStocked::get() );
			}
			
		}
		
		
		
		
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return $this->sendEMail( new EMailTemplate() );
	}
	
	
	public function getEMailTemplates(): array
	{
		$template = new EMailTemplate();
		
		return [$template];
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order paid';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-paid';
	}
	
	public function sendOrderPaidEmail( Order $order ) : bool
	{
		$template = new EMailTemplate();
		$template->setOrder( $order );
		$email = $template->createEmail( $order->getEshop() );
		
		return $email?->send()??true;
	}
	
}