<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;

use Jet\AJAX;
use Jet\Application;
use Jet\Auth;
use Jet\Data_DateTime;
use Jet\Data_Text;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_Textarea;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\AdministratorSignatures;
use JetApplication\ReturnOfGoods_Note;

class Handler_Note_Main extends Handler
{
	public const KEY = 'note';
	
	protected bool $has_dialog = true;

	protected ReturnOfGoods_Note $new_note;
	protected ?Form $internal_note_form = null;
	protected ?Form $customer_message_form = null;
	
	protected function init() : void
	{
		$this->new_note = new ReturnOfGoods_Note();
		$this->new_note->setReturnOfGoods( $this->return_of_goods );
		$this->new_note->setDateAdded( Data_DateTime::now() );
		
		$admin = Auth::getCurrentUser();
		if($admin) {
			$this->new_note->setAdministrator( $admin->getName() );
			$this->new_note->setAdministratorId( $admin->getId() );
		}
		
		$this->view->setVar('notes_handler', $this);
		$this->view->setVar('new_note', $this->new_note);
		
		Handler_Note_MessageGenerator::initGenerators( $this->view, $this->return_of_goods );
	}
	
	public function handleOnlyIfReturnOfGoodsIsEditable() : bool
	{
		return false;
	}
	
	
	protected function saveFiles() : void
	{
		$files = [];
		
		$target_dir = $this->getFilesDirPath();
		
		foreach($this->getUploadedFiles() as $path=>$name) {
			IO_File::move( $path, $target_dir.$name );
			$files[] = $name;
		}
		
		$this->new_note->setFiles( $files );
		
	}
	
	protected function uploadFiles() : void
	{
		$dir = $this->getUploadedFilesDirPath();
		
		foreach($_FILES['files']['tmp_name'] as $i=>$tmp_name) {
			$name = Data_Text::removeAccents( $_FILES['files']['name'][$i] );
			
			IO_File::moveUploadedFile( $tmp_name, $dir.$name );
		}
	}
	
	public function getUploadedFiles() : array
	{
		$dir = $this->getUploadedFilesDirPath();
		
		return IO_Dir::getFilesList( $dir );
	}
	
	
	protected function deleteUploadedFile( string $file ) : void
	{
		$files = $this->getUploadedFiles();
		
		foreach($files as $path=>$name) {
			if($name==$file) {
				IO_File::delete( $path );
				return;
			}
		}
	}
	
	
	protected function getUploadedFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'ReturnOfGoods_Note_files_tmp/'.Auth::getCurrentUser()->getId().'/'.$this->return_of_goods->getShop()->getKey().'/'.$this->return_of_goods->getId().'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	protected function getFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'ReturnOfGoods_Note_files/'.Auth::getCurrentUser()->getId().'/'.$this->return_of_goods->getShop()->getKey().'/'.$this->return_of_goods->getId().'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	public function getInternalNoteForm() : Form
	{
		if(!$this->internal_note_form) {
			$new_note_text = new Form_Field_Textarea('new_note_text', '');
			$new_note_text->setFieldValueCatcher(function( $value ) {
				$this->new_note->setNote( $value );
			});
			
			$form = new Form('internal_note_form', [$new_note_text]);
			$form->setAction( Http_Request::currentURI(['note-action'=>'add_note']) );
			$this->internal_note_form = $form;
		}
		
		return $this->internal_note_form;
	}
	
	public function getCustomerMessageForm() : Form
	{
		if(!$this->customer_message_form) {
			$new_note_text = new Form_Field_Textarea('new_note_text', '');
			$new_note_text->setDefaultValue(PHP_EOL.PHP_EOL.PHP_EOL.AdministratorSignatures::getSignature( $this->return_of_goods->getShop() )  );
			$new_note_text->setFieldValueCatcher(function( $value ) {
				$this->new_note->setNote( $value );
			});
			
			$email_subject = new Form_Field_Input('email_subject', 'Subject of the email to the customer:');
			
			
			$template = new Handler_Note_EMailTemplate();
			$template->setReturnOfGoods( $this->return_of_goods );
			
			$email_subject->setDefaultValue( $template->createEmail( $this->return_of_goods->getShop() )->getSubject() );
			
			$form = new Form('customer_message_form', [$new_note_text, $email_subject]);
			$form->setAction( Http_Request::currentURI(['note-action'=>'add_note']) );
			$this->customer_message_form = $form;
		}
		
		return $this->customer_message_form;
	}
	
	
	public function handle() : void
	{
		
		
		Handler_Note_MessageGenerator::handleMessageGenerators();
		
		$return_of_goods = $this->return_of_goods;
		
		if(Main::getCurrentUserCanEdit()) {
			switch(Http_Request::GET()->getString('note-action')) {
				case 'add_note':
					
					if($this->getInternalNoteForm()->catch()) {
						$this->new_note->setSentToCustomer( false );
						$this->saveFiles();
						$this->new_note->save();
					}
					
					if($this->getCustomerMessageForm()->catch()) {
						$this->new_note->setSentToCustomer( true );
						$this->new_note->setCustomerEmailAddress( $return_of_goods->getEmail() );
						$this->saveFiles();
						$this->new_note->save();
						
						
						
						$template = new Handler_Note_EMailTemplate();
						$template->setReturnOfGoods( $this->return_of_goods );
						$template->setMessage( $this->new_note->getNote() );
						
						$email = $template->createEmail( $this->return_of_goods->getShop() );
						$email->setSubject( $this->getCustomerMessageForm()->field('email_subject')->getValue() );
						
						foreach($this->new_note->getFiles() as $file) {
							$email->addAttachments(
								$this->getFilesDirPath().$file
							);
						}
						
						$email->setTo( $this->new_note->getCustomerEmailAddress() );
						$email->send();
						
					}
					
					AJAX::operationResponse(
						true,
						snippets: [
							'return-of-goods-history' => $this->main_view->render('edit/history'),
							'new-note-form' => $this->view->render('add-note/form'),
							'sent-emails' => $this->main_view->render('edit/sent-emails')
						]
					);
					
					break;
				case 'upload_note_files':
					$this->uploadFiles();
					
					AJAX::operationResponse(
						true,
						snippets: [
							'note-uploaded-files' => $this->view->render('add-note/uploaded-files')
						]
					);
					break;
				case 'delete_note_uploaded_file':
					$this->deleteUploadedFile( Http_Request::GET()->getRaw('file') );
					
					echo $this->view->render('add-note/uploaded-files');
					Application::end();
					
					break;
				case 'show_note_tmp_file':
					$file = Http_Request::GET()->getRaw('file');
					
					$files = $this->getUploadedFiles();
					
					foreach($files as $path=>$name) {
						if($name==$file) {
							IO_File::send( $path );
							break;
						}
					}
					Application::end();
					break;
			}
		}
		
		if( Http_Request::GET()->getString( 'note-action' ) == 'show_note_file' ) {
			$file = Http_Request::GET()->getRaw( 'file' );
			
			$files = IO_Dir::getFilesList( $this->getFilesDirPath() );
			
			foreach( $files as $path => $name ) {
				if( $name == $file ) {
					IO_File::send( $path );
					break;
				}
			}
			Application::end();
		}
		
		
	}
	
	/**
	 * @param ReturnOfGoods_Note $note
	 * @return Handler_Note_File[]
	 */
	public function getNoteFiles( ReturnOfGoods_Note $note ) : array
	{
		$files = [];
		
		foreach($note->getFiles() as $file_name) {
			$files[] = new Handler_Note_File( $this->getFilesDirPath(), $file_name );
		}
		
		return $files;
	}
	
}