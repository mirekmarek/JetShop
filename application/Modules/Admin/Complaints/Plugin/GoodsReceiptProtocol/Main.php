<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Application;
use Jet\Form;
use Jet\Form_Field_File_UploadedFile;
use Jet\Form_Field_Select;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\IO_File;
use Jet\SysConf_Path;
use Jet\Tr;
use JetApplication\Complaint;
use JetApplication\Complaint_Image;
use JetApplication\Complaint_Status_BeingProcessed;
use JetApplication\Complaint_Status_GoodsReceived;
use JetApplication\Complaint_Status_PickupOrdered;

class Plugin_GoodsReceiptProtocol_Main extends Plugin
{
	public const KEY = 'goods_receipt_protocol';
	
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		/**
		 * @var Complaint $complaint
		 */
		$complaint = $this->item;
		
		
		$yes = Tr::_('Yes');
		$no = Tr::_('No');
		$goods_received_in_full = new Form_Field_Select('goods_received_in_full', 'Goods received in full');
		$goods_received_in_full->setSelectOptions([
			$yes => $yes,
			$no =>  $no
		]);
		
		$new = Tr::_('New goods');
		$used = Tr::_('Used goods');
		$condition_of_goods = new Form_Field_Select('condition_of_goods', 'Condition of goods');
		$condition_of_goods->setSelectOptions([
			$new => $new,
			$used => $used,
		]);
		
		
		$taken_parts = new Form_Field_Textarea('taken_parts', 'Taken parts');
		
		
		$this->form = new Form( 'goods_receipt_protocol_form', [$goods_received_in_full, $condition_of_goods, $taken_parts] );
		
		$this->view->setVar('form', $this->form );
	}
	
	public function canBeHandled() : bool
	{
		if(!parent::canBeHandled()) {
			return false;
		}
		
		/**
		 * @var Complaint $complaint
		 */
		$complaint = $this->item;
		
		return in_array(
			$complaint->getStatus()::getCode(),
			[
				Complaint_Status_BeingProcessed::getCode(),
				Complaint_Status_PickupOrdered::getCode(),
				Complaint_Status_GoodsReceived::getCode()
			]
		);
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
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
		
		$template = new PDFTemplate_GoodsReceiptProtocol();
		$template->setComplaint( $complaint );
		$template->setConditionOfGoods( $this->form->field('condition_of_goods')->getValue() );
		$template->setGoodsReceivedInFull( $this->form->field('goods_received_in_full')->getValue() );
		$template->setTakenParts( nl2br($this->form->field('taken_parts')->getValue()) );
		
		$pdf = $template->generatePDF( $complaint->getEshop() );
		
		$file_name = 'complaint_service_protocol_'.$complaint->getNumber().'.pdf';
		
		Http_Headers::sendDownloadFileHeaders(
			file_name: $file_name,
			file_mime: 'application/pdf',
			file_size: strlen($pdf),
			force_download: false
		);
		
		echo $pdf;
		
		$file_name = $complaint->getNumber().'_prevzeti_'.date('YmdHis').'.pdf';
		$file_path = SysConf_Path::getTmp().$file_name;
		
		IO_File::write( $file_path, $pdf );
		
		$file = new Form_Field_File_UploadedFile(
			file_name: $file_name,
			tmp_file_path: $file_path
		);
		
		Complaint_Image::uploadImage(
			complaint: $complaint,
			file: $file,
			notification_sent: true
		);
		
		IO_File::delete( $file_path );
		
		Application::end();
	}
	
}