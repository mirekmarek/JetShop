<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;

class Handler_Cancel_Main extends Handler {
	public const KEY = 'cancel';
	
	protected Form $form;
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
		$comment = new Form_Field_Textarea('comment', 'Comments:');
		$this->form = new Form('cancel_order_form', [ $comment ]);
		$this->view->setVar('cancel_order_form', $this->form);
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			$this->order->cancel( $this->form->field('comment')->getValue() );
			UI_messages::success(Tr::_('Order has been canceled'));
			Http_Headers::reload();
		}
	}
}