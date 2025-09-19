<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\SMS\Templates;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Layout;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\SMS_Template;
use JetApplication\SMS_TemplateText;
use JetApplication\EShops;

class Controller_Main extends Admin_EntityManager_Controller
{
	/**
	 * @var SMS_Template[]
	 */
	protected array $templates;
	
	public function getTabs(): array
	{
		$tabs = parent::getTabs();
		
		if(isset($tabs['description'])) {
			$tabs['description'] = Tr::_('Content');
		}
		
		return $tabs;
	}
	
	
	public function setupListing(): void
	{
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'internal_name',
			'internal_code',
			'internal_notes',
		]);
		
		$this->templates = SMS_TemplateText::actualizeList();
		$this->view->setVar('templates', $this->templates);
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('sms_preview', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $action=='sms_preview';
			});
		
		$this->router->addAction('send_test_sms', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $action=='send_test_sms';
			});
		
		
	}
	
	
	public function sms_preview_Action() : void
	{
		$eshop_key = Http_Request::GET()->getString('eshop');
		if(!EShops::exists($eshop_key)) {
			return;
		}
		
		$eshop = EShops::get( $eshop_key );
		
		$this->templates = SMS_TemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_sms = $template->createTestSMS( $eshop );
		
		MVC_Layout::getCurrentLayout()->setScriptName('dialog');
		$this->view->setVar('test_sms', $test_sms );
		$this->output('sms-preview');
		
	}
	
	public function send_test_sms_Action() : void
	{
		$eshop_key = Http_Request::GET()->getString('eshop');
		if(!EShops::exists($eshop_key)) {
			return;
		}
		
		$phone_number = Http_Request::GET()->getString('phone_number');
		
		$eshop = EShops::get( $eshop_key );
		
		$this->templates = SMS_TemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_sms = $template->createTestSMS( $eshop );
		$test_sms->setToPhoneNumber($phone_number);
		$test_sms->send();
		
		UI_messages::success(
			Tr::_('Test SMS has been sent to %phone_number%', ['phone_number'=>$phone_number])
		);
		
		Http_Headers::reload(unset_GET_params: [
			'action', 'eshop', 'phone_number'
		]);
	}
}