<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Application;
use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\Complaint;

class Plugin_ServiceReport_Main extends Plugin
{
	public const KEY = 'service_report';
	
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return false;
	}
	
	protected function init() : void
	{
		/**
		 * @var Complaint $complaint
		 */
		$complaint = $this->item;
		
		$service_report = new Form_Field_Textarea('service_report', 'Service Report');
		$service_report->setDefaultValue( $complaint->getServiceReport() );
		$service_report->setFieldValueCatcher( function( string $value ) use ($complaint) {
			$complaint->setServiceReport( $value );
			$complaint->save();
		} );
		
		$this->form = new Form( 'service_report_form', [$service_report] );
		
		$this->view->setVar('form', $this->form );
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			Http_Headers::reload();
		}
		
		if(Http_Request::GET()->getString('service_report_action')=='generate') {
			$this->generteServiceReport();
		}
	}
	
	public function renderDialog() : string
	{
		if(
			!$this->canBeHandled()
		) {
			return '';
		}
		
		return $this->view->render('dialog');
	}
	
	public function generteServiceReport() : void
	{
		/**
		 * @var Complaint $complaint
		 */
		$complaint = $this->item;
		
		$template = new PDFTemplate_ServiceReport();
		$template->setComplaint( $complaint );
		
		$pdf = $template->generatePDF( $complaint->getEshop() );
		
		$file_name = 'complaint_service_protocol_'.$complaint->getNumber().'.pdf';
		
		Http_Headers::sendDownloadFileHeaders(
			file_name: $file_name,
			file_mime: 'application/pdf',
			file_size: strlen($pdf),
			force_download: false
		);
		
		echo $pdf;
		
		Application::end();
	}
	
}