<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;

class Handler_Join_Main extends Handler
{
	public const KEY = 'join';
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return true;
	}
	
	public function handle() : void
	{
		$GET = Http_Request::GET();
		
		if(($search_order=$GET->getString('join_order_search'))) {
			
			$join_order = Order::getByNumber( $search_order, $this->order->getShop() );
			if(!$join_order) {
				echo UI_messages::createDanger( Tr::_('Unknown order') )->setCloseable( false );
				Application::end();
			}
			
			if(
				$join_order->getId()==$this->order->getId() ||
				!$join_order->isEditable()
			) {
				echo UI_messages::createDanger( Tr::_('This order cannot be joined') )->setCloseable( false );
				Application::end();
			}
			
			$this->view->setVar('this_order', $this->order);
			$this->view->setVar('join_order', $join_order);
			
			echo $this->view->render('join-info');
			
			Application::end();
		}
		
		if( ($join_order_id = $GET->getInt('join_order')) ) {
			
			$join_order = Order::get( $join_order_id );
			if(
				$join_order &&
				$join_order->isEditable() &&
				$join_order->getId()!=$this->order->getId()
			) {
				if($this->order->join( $join_order )) {
					UI_messages::success(
						Tr::_('Orders has been joined')
					);
				}
			}
			
			Http_Headers::reload(unset_GET_params: ['join_order']);
		}
	}
}