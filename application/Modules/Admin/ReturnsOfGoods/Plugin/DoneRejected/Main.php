<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;



use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\ReturnOfGoods_Event_DoneRejected;

class Plugin_DoneRejected_Main extends Plugin
{
	
	public const KEY = 'done_rejected';
	
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		$comment = new Form_Field_Textarea('comment', 'Comments:');
		$this->form = new Form('done_rejected_form', [ $comment ]);
		$this->view->setVar('done_rejected_form', $this->form);
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			$event = $this->item->createEvent( ReturnOfGoods_Event_DoneRejected::new() );
			
			$event->setNoteForCustomer( $this->form->field('comment')->getValue() );
			$event->handleImmediately();
			
			UI_messages::success(Tr::_('Return of goods has been done - rejected'));
			Http_Headers::reload();
		}
	}
}