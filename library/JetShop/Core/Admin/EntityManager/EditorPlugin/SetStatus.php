<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;

trait Core_Admin_EntityManager_EditorPlugin_SetStatus
{
	/**
	 * @var EShopEntity_Status_PossibleFutureStatus[]
	 */
	protected array $future_statuses = [];
	
	/**
	 * @var Form[]
	 */
	protected array $forms = [];
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	public function handleOnlyIfItemIsEditable() : bool
	{
		return false;
	}
	
	protected function init() : void
	{
		/**
		 * @var EShopEntity_HasStatus_Interface $item
		 */
		$item = $this->item;
		$this->future_statuses = $item->getStatus()->getPossibleFutureStatuses();
		
		foreach( $this->future_statuses as $future_state) {
			$code = $future_state->getStatus()::getCode();
			
			$fields = [];
			
			if( $future_state->doNotHandleEventSwitchEnabled() ) {
				$do_not_handle_event = new Form_Field_Checkbox('do_not_handle_event', $future_state->doNotHandleEventSwitchLabel() );
				$fields[] = $do_not_handle_event;
			}
			
			if( $future_state->internalNoteEnabled() ) {
				$internal_note = new Form_Field_Textarea('internal_note', 'Internal notes:');
				$fields[] = $internal_note;
			}
			
			if( $future_state->noteForCustomerEnabled() ) {
				$note_for_custommer = new Form_Field_Textarea('note_for_custommer', 'Note for custommer:');
				$fields[] = $note_for_custommer;
			}
			
			if( $future_state->doNotHandleExternalsSwitchEnabled() ) {
				$do_not_handle_externals = new Form_Field_Checkbox('do_not_handle_externals', $future_state->doNotHandleExternalsSwitchLabel() );
				$fields[] = $do_not_handle_externals;
			}
			
			if( $future_state->doNotSendNotificationsSwitchEnabled() ) {
				$do_not_send_notifications = new Form_Field_Checkbox('do_not_send_notifications', $future_state->doNotSendNotificationsSwitchLabel() );
				$fields[] = $do_not_send_notifications;
			}
			
			$form = new Form('set_state_'.$code, $fields);
			
			$this->forms[$code] = $form;
		}
		
		$this->view->setVar('future_states', $this->future_statuses);
		$this->view->setVar('forms', $this->forms);
	}
	
	
	public function handle(): void
	{
		if( !$this->canBeHandled() ) {
			return;
		}
		
		foreach( $this->future_statuses as $future_state ) {
			$code = $future_state->getStatus()::getCode();
			
			$form = $this->forms[$code];
			
			if($form->catch()) {
				$handle_event = true;
				
				if($form->fieldExists('do_not_handle_event')) {
					$handle_event = !$form->field('do_not_handle_event')->getValue();
				}
				
				$params = [];
				
				/**
				 * @var EShopEntity_HasStatus_Interface $item
				 */
				$item = $this->item;
				
				$item->setStatus(
					$future_state->getStatus(),
					handle_event: $handle_event,
					params: $params,
					event_setup: function( EShopEntity_Event $event ) use ($form, $future_state) : void {
						if( $future_state->internalNoteEnabled() ) {
							$event->setInternalNote( $form->field('internal_note')->getValue() );
						}
						if( $future_state->noteForCustomerEnabled() ) {
							$event->setNoteForCustomer( $form->field('note_for_custommer')->getValue() );
						}
						
						if( $future_state->doNotHandleExternalsSwitchEnabled() ) {
							$event->setDoNotHandleExternals( $form->field('do_not_handle_externals')->getValue() );
						}
						
						if( $future_state->doNotSendNotificationsSwitchEnabled() ) {
							$event->setDoNotSendNotification( $form->field('do_not_send_notifications')->getValue() );
						}
					}
				);
				
				Http_Headers::reload();
			}
		}
	}
	
	public function renderButton() : string
	{
		if( !$this->canBeHandled() ) {
			return '';
		}
		
		return Application_Service_Admin::EntityEdit()->renderEntitySetStatusButtons( $this->future_statuses );
	}
	
	public function renderDialog() : string
	{
		if( !$this->canBeHandled() ) {
			return '';
		}
		
		return Application_Service_Admin::EntityEdit()->renderEntitySetStatusDialogs( $this->future_statuses, $this->forms );
	}
	
}