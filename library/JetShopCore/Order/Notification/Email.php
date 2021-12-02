<?php
/**
 *
 */

namespace JetShop;

use Jet\Mailing_Email;
use Jet\Mailing_Email_Template;
use Jet\MVC_View;

abstract class Core_Order_Notification_Email extends Order_Notification {

	protected string $sender_name = '';

	protected string $sender_email = '';

	protected string $mail_to = '';

	protected string $subject_view_script = Mailing_Email_Template::SUBJECT_VIEW;

	protected string $html_view_script = Mailing_Email_Template::BODY_HTML_VIEW;

	protected string $text_view_script = Mailing_Email_Template::BODY_TXT_VIEW;


	protected array $attachments = [];

	public function getSenderName(): string
	{
		return $this->sender_name;
	}

	public function setSenderName( string $sender_name ): void
	{
		$this->sender_name = $sender_name;
	}

	public function getSenderEmail(): string
	{
		return $this->sender_email;
	}

	public function setSenderEmail( string $sender_email ): void
	{
		$this->sender_email = $sender_email;
	}


	public function getSubjectViewScript(): string
	{
		return $this->subject_view_script;
	}

	public function setSubjectViewScript( string $subject_view_script ): void
	{
		$this->subject_view_script = $subject_view_script;
	}

	public function getHtmlViewScript(): string
	{
		return $this->html_view_script;
	}

	public function setHtmlViewScript( string $html_view_script ): void
	{
		$this->html_view_script = $html_view_script;
	}

	public function getTextViewScript(): string
	{
		return $this->text_view_script;
	}

	public function setTextViewScript( string $text_view_script ): void
	{
		$this->text_view_script = $text_view_script;
	}

	public function getMailTo(): string
	{
		return $this->mail_to;
	}

	public function setMailTo( string $mail_to ): void
	{
		$this->mail_to = $mail_to;
	}


	public function addAttachments( string $file_path, string $file_name = '' ): void
	{

		if( !$file_name ) {
			$file_name = basename( $file_path );
		}

		$this->attachments[$file_name] = $file_path;
	}


	public function generateText() : string
	{
		$view = new MVC_View( $this->getViewRootDir() );

		$view->setVar( 'email', $this );

		foreach( $this->getViewData() as $k=>$v ) {
			$view->setVar( $k, $v );
		}

		$text = $view->render( $this->getTextViewScript() );

		return $text;
	}

	public function generateHtml() : string
	{
		$view = new MVC_View( $this->getViewRootDir() );

		$view->setVar( 'email', $this );

		foreach( $this->getViewData() as $k=>$v ) {
			$view->setVar( $k, $v );
		}

		$text = $view->render( $this->getHtmlViewScript() );

		return $text;
	}


	public function generateSubject() : string
	{
		$view = new MVC_View( $this->getViewRootDir() );

		$view->setVar( 'email', $this );

		foreach( $this->getViewData() as $k=>$v ) {
			$view->setVar( $k, $v );
		}

		$text = $view->render( $this->getSubjectViewScript() );

		return $text;
	}


	public function getEmail() : Mailing_Email
	{
		$email = new Mailing_Email();

		$email->setBodyTxt( $this->generateText() );
		$email->setBodyHtml( $this->generateHtml() );

		$email->setSubject( $this->generateSubject() );

		$email->setSenderEmail( $this->getSenderEmail() );
		$email->setSenderName( $this->getSenderName() );

		foreach($this->attachments as $file_name=>$file_path) {
			$email->addAttachments( $file_path, $file_name );
		}

		return $email;
	}


	public function send() : void
	{
		$email = $this->getEmail();
		$email->setTo( $this->mail_to );
		$email->send();

		//TODO: sent
	}

}