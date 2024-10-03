<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\Email\Templates;

use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Mailing;
use Jet\MVC_Layout;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_WithShopData_Controller;
use JetApplication\EMail_Template;
use JetApplication\Shops;

class Controller_Main extends Admin_EntityManager_WithShopData_Controller
{
	/**
	 * @var EMail_Template[]
	 */
	protected array $templates;
	
	protected function getTabs() : array
	{
		return [
			'main'   => Tr::_( 'Main data' ),
		];
	}
	
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
		
		$this->templates = EmailTemplateText::actualizeList();
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
		$shop_key = Http_Request::GET()->getString('shop');
		if(!Shops::exists($shop_key)) {
			return;
		}
		
		$shop = Shops::get( $shop_key );
		
		$this->templates = EmailTemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_email = $template->createTestEmail( $shop );
		
		MVC_Layout::getCurrentLayout()->setScriptName('dialog');
		$this->view->setVar('test_email', $test_email);
		$this->output('email-preview');
		
	}
	
	public function send_test_email_Action() : void
	{
		$shop_key = Http_Request::GET()->getString('shop');
		if(!Shops::exists($shop_key)) {
			return;
		}
		
		$email = Http_Request::GET()->getString('email');
		
		$shop = Shops::get( $shop_key );
		
		$this->templates = EmailTemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_email = $template->createTestEmail( $shop );
		$test_email->setTo($email);
		$test_email->send();
		
		UI_messages::success(
			Tr::_('Test e-mail has been sent to %email%', ['email'=>$email])
		);
		
		Http_Headers::reload(unset_GET_params: [
			'action', 'shop', 'email'
		]);
	}
	
	public function download_message_file_Action() : void
	{
		
		$shop_key = Http_Request::GET()->getString('shop');
		if(!Shops::exists($shop_key)) {
			return;
		}
		
		$email = 'test@test.tld';
		
		$shop = Shops::get( $shop_key );
		
		$this->templates = EmailTemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$test_email = $template->createTestEmail( $shop );
		
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