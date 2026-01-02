<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Form_Field_Select_Option;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;

trait Core_Admin_EntityManager_EditorPlugin_ForceStatus
{
	/**
	 * @var EShopEntity_Status[]
	 */
	protected array $statuses = [];
	
	protected Form $form;
	
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
		$item = $this->item;
		/**
		 * @var EShopEntity_HasStatus_Interface $item
		 */
		$this->statuses = $item::getStatusList();
		
		$fields = [];
		
		$status_field = new Form_Field_Select('status', 'Force status:');
		$options = [];
		foreach($this->statuses as $status ) {
			if($status::isHidden()) {
				continue;
			}
			
			$options[$status::getCode()] = new Form_Field_Select_Option( $status->getTitle() );
			$options[$status::getCode()]->setSelectOptionCssClass( $status->getShowAdminCSSClass() );
			$options[$status::getCode()]->setSelectOptionCssStyle( $status->getShowAdminCSSStyle() );
		}
		$status_field->setSelectOptions( $options );
		
		$fields[] = $status_field;
		
		$internal_note = new Form_Field_Textarea('internal_note', 'Internal notes:');
		$fields[] = $internal_note;
		
		
		$this->form = new Form( 'force_status_form', $fields );
		
		$this->view->setVar('form', $this->form);
	}
	
	public function handle(): void
	{
		if($this->form->catch()) {
			/**
			 * @var EShopEntity_HasStatus_Interface $item
			 * @var ?EShopEntity_Status $future_status
			 */
			$item = $this->item;
			$params = [];
			
			$future_status = null;
			foreach($this->statuses as $status) {
				if($status::getCode()==$this->form->field('status')->getValue()) {
					$future_status = $status;
					break;
				}
			}
			
			
			$item->setStatus(
				$future_status,
				handle_event: true,
				params: $params,
				event_setup: function( EShopEntity_Event $event ) use ( $future_status) : void {
					$event->setInternalNote( $this->form->field('internal_note')->getValue() );
					
					$event->setNoteForCustomer( '' );
					$event->setDoNotHandleExternals( true );
					$event->setDoNotSendNotification( true );
				}
			);
			
			
			Http_Headers::reload();
		}
	}
	
	public function renderButton() : string
	{
		return Application_Service_Admin::EntityEdit()->renderEntityForceStatusButton();
	}
	
	public function renderDialog() : string
	{
		return Application_Service_Admin::EntityEdit()->renderEntityForceStatusDialog( $this->form );
	}
	
}