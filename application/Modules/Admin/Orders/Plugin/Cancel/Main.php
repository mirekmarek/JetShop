<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;



use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Order;

class Plugin_Cancel_Main extends Plugin {
	public const KEY = 'cancel';
	
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		$comment = new Form_Field_Textarea('comment', 'Comments:');
		$this->form = new Form('cancel_order_form', [ $comment ]);
		$this->view->setVar('cancel_order_form', $this->form);
	}
	
	public function handle() : void
	{
		/**
		 * @var Order $order
		 */
		$order = $this->item;
		
		if($this->form->catch()) {
			$order->cancel( $this->form->field('comment')->getValue() );
			UI_messages::success(Tr::_('Order has been canceled'));
			Http_Headers::reload();
		}
	}
}