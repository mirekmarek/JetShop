<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Email\Templates;


use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Mailing;
use Jet\MVC_Layout;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\EMail_Template;
use JetApplication\EMail_TemplateText;
use JetApplication\EShops;

class Controller_Main extends Admin_EntityManager_Controller
{
	/**
	 * @var EMail_Template[]
	 */
	protected array $templates;
	
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Sender() );
		$this->listing_manager->addColumn( new Listing_Column_Layout() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'internal_name',
			'internal_code',
			'internal_notes',
			'sender',
			'layout'
		]);
		
		$this->templates = EMail_TemplateText::actualizeList();
		$this->view->setVar('templates', $this->templates);
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('email_preview', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $action=='email_preview';
			});
		
		$this->router->addAction('send_test_email', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $action=='send_test_email';
			});
		
		$this->router->addAction('download_message_file', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $action=='download_message_file';
			});
		
	}
	
	
	public function email_preview_Action() : void
	{
		$eshop_key = Http_Request::GET()->getString('eshop');
		if(!EShops::exists($eshop_key)) {
			return;
		}
		
		$eshop = EShops::get( $eshop_key );
		
		$this->templates = EMail_TemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_email = $template->createTestEmail( $eshop );
		
		MVC_Layout::getCurrentLayout()->setScriptName('dialog');
		$this->view->setVar('test_email', $test_email);
		$this->output('email-preview');
		
	}
	
	public function send_test_email_Action() : void
	{
		$eshop_key = Http_Request::GET()->getString('eshop');
		if(!EShops::exists($eshop_key)) {
			return;
		}
		
		$email = Http_Request::GET()->getString('email');
		
		$eshop = EShops::get( $eshop_key );
		
		$this->templates = EMail_TemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_email = $template->createTestEmail( $eshop );
		$test_email->setTo($email);
		$test_email->send();
		
		UI_messages::success(
			Tr::_('Test e-mail has been sent to %email%', ['email'=>$email])
		);
		
		Http_Headers::reload(unset_GET_params: [
			'action', 'eshop', 'email'
		]);
	}
	
	public function download_message_file_Action() : void
	{
		
		$eshop_key = Http_Request::GET()->getString('eshop');
		if(!EShops::exists($eshop_key)) {
			return;
		}
		
		$email = 'test@test.tld';
		
		$eshop = EShops::get( $eshop_key );
		
		$this->templates = EMail_TemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_email = $template->createTestEmail( $eshop );
		
		$message = '';
		$header = '';
		
		$test_email->parseImages();
		
		Mailing::getBackend()->prepareMessage( $test_email, $message, $header );
		
		$eml = $header."\n".$message;
		
		Http_Headers::sendDownloadFileHeaders(
			file_name: 'test.eml',
			file_mime: 'message/rfc822',
			file_size: strlen($eml),
			force_download: true
		);
		
		echo $eml;
		
		Application::end();
	}
	
}