<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\PDF\Templates;


use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\PDF_Template;
use JetApplication\PDF_TemplateText;
use JetApplication\EShops;

class Controller_Main extends Admin_EntityManager_Controller
{
	/**
	 * @var PDF_Template[]
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
	
	
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		$this->templates = PDF_TemplateText::actualizeList();
		$this->view->setVar('templates', $this->templates);
		
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('preview', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $action=='preview';
			});

		
		$this->router->addAction('download_message_file', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $action=='download_message_file';
			});
		
	}
	
	
	public function preview_Action() : void
	{
		$eshop_key = Http_Request::GET()->getString('eshop');
		if(!EShops::exists($eshop_key)) {
			return;
		}
		
		$eshop = EShops::get( $eshop_key );
		
		$this->templates = PDF_TemplateText::actualizeList();
		
		$template = $this->templates[$this->current_item->getInternalCode()];
		$template->initTest( $eshop );
		
		//echo $template->preparePDF( $eshop )->getTemplateFooter();
		
		$test_pdf = $template->generatePDF( $eshop );
		
		Http_Headers::sendDownloadFileHeaders(
			file_name: 'test.pdf',
			file_mime: 'application/pdf',
			file_size: strlen($test_pdf),
			force_download: false
		);
		
		echo $test_pdf;

		Application::end();
		
	}
	
	
}