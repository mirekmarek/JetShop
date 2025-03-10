<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Order;

class Plugin_Join_Main extends Plugin
{
	public const KEY = 'join';
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return true;
	}
	
	public function handle() : void
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		$GET = Http_Request::GET();
		
		if(($search_order=$GET->getString('join_order_search'))) {
			
			$join_order = Order::getByNumber( $search_order, $item->getEshop() );
			if(!$join_order) {
				echo UI_messages::createDanger( Tr::_('Unknown order') )->setCloseable( false );
				Application::end();
			}
			
			if(
				$join_order->getId()==$this->item->getId() ||
				!$join_order->isEditable()
			) {
				echo UI_messages::createDanger( Tr::_('This order cannot be joined') )->setCloseable( false );
				Application::end();
			}
			
			$this->view->setVar('this_order', $this->item);
			$this->view->setVar('join_order', $join_order);
			
			echo $this->view->render('join-info');
			
			Application::end();
		}
		
		if( ($join_order_id = $GET->getInt('join_order')) ) {
			
			$join_order = Order::get( $join_order_id );
			if(
				$join_order &&
				$join_order->isEditable() &&
				$join_order->getId()!=$item->getId()
			) {
				if($item->join( $join_order )) {
					UI_messages::success(
						Tr::_('Orders has been joined')
					);
				}
			}
			
			Http_Headers::reload(unset_GET_params: ['join_order']);
		}
	}
}