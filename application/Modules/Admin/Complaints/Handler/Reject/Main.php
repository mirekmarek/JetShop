<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Complaints;



use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;

class Handler_Reject_Main extends Handler
{
	
	public const KEY = 'reject';
	
	protected Form $form;
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
		$comment = new Form_Field_Textarea('comment', 'Comments:');
		$this->form = new Form('done_rejected_form', [ $comment ]);
		$this->view->setVar('done_rejected_form', $this->form);
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			$this->complaint->rejected( $this->form->field('comment')->getValue() );
			
			UI_messages::success(Tr::_('Complaint has been done - rejected'));
			Http_Headers::reload();
		}
	}
}