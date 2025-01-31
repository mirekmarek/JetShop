<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\NoteManager;


use Jet\AJAX;
use Jet\Application;
use Jet\Application_Module;
use Jet\Auth;
use Jet\Data_DateTime;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_Textarea;
use Jet\Http_Request;
use Jet\MVC_View;
use JetApplication\Admin_Managers_Note;
use JetApplication\AdministratorSignatures;
use JetApplication\EShopEntity_Note;
use JetApplication\EShopEntity_Note_MessageGenerator;
use Closure;


class Main extends Application_Module implements Admin_Managers_Note
{
	protected EShopEntity_Note $new_note;
	protected string $generated_subject;
	protected string $customer_email_address;
	protected Closure $after_add;
	
	protected MVC_View $view;
	
	protected array $message_generators = [];
	
	protected ?Form $internal_note_form = null;
	protected ?Form $customer_message_form = null;
	
	public function init( EShopEntity_Note $new_note, string $generated_subject, string $customer_email_address, Closure $after_add ) : void
	{
		$this->after_add = $after_add;
		$this->customer_email_address = $customer_email_address;
		$this->generated_subject = $generated_subject;
		
		$this->new_note = $new_note;
		$this->new_note->setDateAdded( Data_DateTime::now() );
		
		$admin = Auth::getCurrentUser();
		if($admin) {
			$this->new_note->setAdministrator( $admin->getName() );
			$this->new_note->setAdministratorId( $admin->getId() );
		}
		
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$this->view->setVar( 'new_note', $this->new_note );
		
		$this->view->setVar( 'internal_note_form', $this->getInternalNoteForm() );
		$this->view->setVar( 'customer_message_form', $this->getCustomerMessageForm() );
		$this->view->setVar( 'message_generators', $this->message_generators);
	}
	
	public function addMessageGenerator( EShopEntity_Note_MessageGenerator $generator ) : void
	{
		$this->message_generators[$generator->getKey()] = $generator;
		$this->view->setVar('message_generators', $this->message_generators);
	}
	
	/**
	 * @return EShopEntity_Note_MessageGenerator[]
	 */
	public function getMessageGenerators() : array
	{
		return $this->message_generators;
	}
	
	
	protected function getInternalNoteForm() : Form
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
	
	protected function getCustomerMessageForm() : Form
	{
		if(!$this->customer_message_form) {
			
			$new_note_text = new Form_Field_Textarea('new_note_text', '');
			$new_note_text->setDefaultValue(PHP_EOL.PHP_EOL.PHP_EOL.AdministratorSignatures::getSignature( $this->new_note->getEshop() )  );
			$new_note_text->setFieldValueCatcher(function( $value ) {
				$this->new_note->setNote( $value );
			});
			
			
			$email_subject = new Form_Field_Input('email_subject', 'Subject of the email to the customer:');
			$email_subject->setDefaultValue( $this->generated_subject );
			$email_subject->setFieldValueCatcher(function( $value ) {
				$this->new_note->setSubject( $value );
			});
			
			
			$form = new Form('customer_message_form', [$new_note_text, $email_subject]);
			$form->setAction( Http_Request::currentURI(['note-action'=>'add_note']) );
			$this->customer_message_form = $form;
		}
		
		return $this->customer_message_form;
	}

	
	public function handle() : void
	{
		$GET = Http_Request::GET();
		

		switch($GET->getString('note-action')) {
			case 'add_note':
				
				if($this->getInternalNoteForm()->catch()) {
					$this->new_note->setSentToCustomer( false );
					$this->new_note->save();
					$this->new_note->saveFiles();
				}
				
				if($this->getCustomerMessageForm()->catch()) {
					$this->new_note->setSentToCustomer( true );
					$this->new_note->setCustomerEmailAddress( $this->customer_email_address );
					$this->new_note->save();
					$this->new_note->saveFiles();
				}
				
				$snippets = [
					'new-note-form' => $this->view->render('add-note/form')
				];
				
				$after_add = $this->after_add;
				
				$after_add( $this->new_note, $snippets );
				
				AJAX::operationResponse(
					true,
					snippets: $snippets
				);
				
				break;
			case 'upload_note_files':
				$this->new_note->uploadFiles();
				
				AJAX::operationResponse(
					true,
					snippets: [
						'note-uploaded-files' => $this->view->render('add-note/uploaded-files')
					]
				);
				
				break;
			case 'delete_note_uploaded_file':
				$this->new_note->deleteUploadedFile( Http_Request::GET()->getRaw('file') );
				
				echo $this->view->render('add-note/uploaded-files');
				Application::end();
				
				break;
			case 'show_note_tmp_file':
				$file = Http_Request::GET()->getRaw('file');
				
				$this->new_note->showUploadedFile( $file );
				break;
		}

		
		if( $GET->getString( 'note-action' ) == 'show_note_file' ) {
			
			$class = get_class($this->new_note);
			
			$note = $class::load( $GET->getInt('note') );
			
			if($note) {
				$file = Http_Request::GET()->getRaw( 'file' );
				$note->showFile( $file );
			}
		}
		
		$key = $GET->getString('generate_message');

		if(
			$key &&
			isset($this->message_generators[$key])
		) {
			$generator = $this->message_generators[$key];
			
			AJAX::commonResponse([
				'subject' => $generator->generateSubject(),
				'text' => $generator->generateText(),
			]);
		}
		
	}
	
	
	public function showDialog( string $customer_message_generators='' ) : string
	{
		return $this->view->render( 'dialog' );
	}
	
	
	public function showNote( EShopEntity_Note $note ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('note', $note);
		return $view->render( 'note' );
	}
	
}