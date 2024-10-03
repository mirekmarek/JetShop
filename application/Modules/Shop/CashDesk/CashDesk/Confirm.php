<?php
namespace JetApplicationModule\Shop\CashDesk;

use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\Tr;

use JetApplication\EMailMarketing;
use JetApplication\Order;
use JetApplication\Customer;
use JetApplication\CashDesk_Confirm_AgreeFlag;
use JetApplication\Shop_Pages;

trait CashDesk_Confirm {

	/**
	 * @var CashDesk_Confirm_AgreeFlag[]
	 */
	protected ?array $agree_flags = null;

	protected ?Form $comment_form = null;
	
	
	public function initAgreeFlags() : void
	{

		if($this->agree_flags===null) {
			$this->agree_flags = [];

			$this->initMainAgreeFlags_terns();
			$this->initMainAgreeFlags_survey_disagree();
			$this->initMainAgreeFlags_mailing_subscribe();
		}
	}

	public function initMainAgreeFlags_terns() : void
	{
		$terms = new CashDesk_Confirm_AgreeFlag('terms', Tr::_('I agree with <a href="%link%">terms and conditions</a>', [
			'link' => Shop_Pages::TermsAndConditions()->getURL()
		]));
		
		$terms->setIsMandatory(true);
		$terms->setErrorMessage(Tr::_('To complete the order, it is necessary to agree to the terms and conditions'));
		$this->addAgreeFlag($terms);
	}


	public function initMainAgreeFlags_survey_disagree() : void
	{
		$survey_disagree = new CashDesk_Confirm_AgreeFlag('survey_disagree', Tr::_('I disagree with the survey'));
		$survey_disagree->setOrderStateSetter( function( Order $order, bool $state ) {
			$order->setSurveyDisagreement( !$state );
		} );
		$this->addAgreeFlag($survey_disagree);
	}

	public function initMainAgreeFlags_mailing_subscribe() : void
	{
		$mailing_subscribe = new CashDesk_Confirm_AgreeFlag('mailing_subscribe', Tr::_('I agree with newsletter registration'));
		$mailing_subscribe->setDefaultChecked(true);
		$mailing_subscribe->setOrderStateSetter( function( Order $order, bool $state ) {
			$order->setNewsletterAccepted( $state );
		} );

		$mailing_subscribe->setOnOrderSave(function( Order $order, bool $checked ) {
			$mailing_subscribe_source = 'order:'.$order->getNumber();

			if($checked) {
				EMailMarketing::SubscriptionManager()->subscribe(
					shop: $order->getShop(),
					email_address: $order->getEmail(),
					source: $mailing_subscribe_source
				);
			} /* else {
				EMailMarketing::MailingSubscriptionManager()->unsubscribe(
					shop: $order->getShop(),
					email_address: $order->getEmail(),
					source: $mailing_subscribe_source
				);
			} */
		});

		$mailing_subscribe->setOnCustomerLogin( function( CashDesk $cash_desk, bool $checked ) use ($mailing_subscribe) {
			$customer = Customer::getCurrentCustomer();

			$mailing_subscribe->setIsChecked( $customer->getMailingSubscribed() );
		} );

		$this->addAgreeFlag($mailing_subscribe);
	}


	public function addAgreeFlag( CashDesk_Confirm_AgreeFlag $agree_flag ) : void
	{
		$this->agree_flags[$agree_flag->getCode()] = $agree_flag;
	}

	public function removeAgreeFlag( string $code ) : void
	{
		if(isset($this->agree_flags[$code])) {
			unset($this->agree_flags[$code]);
		}
	}

	public function getAgreeFlag( string $code ) : ?CashDesk_Confirm_AgreeFlag
	{
		$flags = $this->getAgreeFlags();
		if(!isset($flags[$code])) {
			return null;
		}

		return $flags[$code];
	}

	public function getAgreeFlagChecked( string $code ) : bool
	{
		$flag = $this->getAgreeFlag($code);
		if(!$flag) {
			return false;
		}

		return $flag->isChecked();
	}

	/**
	 * @return CashDesk_Confirm_AgreeFlag[]
	 */
	public function getAgreeFlags() : array
	{

		$this->initAgreeFlags();
		
		$session = $this->getSession();

		foreach($this->agree_flags as $flag)
		{

			$session_key = 'agree_flag_'.$flag->getCode();

			$checked = $session->getValue($session_key);

			if($checked===null) {
				$checked = $flag->isDefaultChecked();
				$session->setValue($session_key, $checked);
			}

			$flag->setIsChecked( $checked );
		}

		return $this->agree_flags;
	}

	public function setAgreeFlagState( string $code, bool $state ) : void
	{
		if(!isset($this->agree_flags[$code])) {
			return;
		}

		$this->agree_flags[$code]->setIsChecked($state);
		$this->getSession()->setValue('agree_flag_'.$code, $state);
	}


	public function getSpecialRequirements() : string
	{

		return $this->getSession()->getValue('special_requirements', '');
	}

	public function setSpecialRequirements( string $comment ) : void
	{
		$comment = strip_tags($comment);
		$comment = substr($comment, 0, 3000);

		$this->getSession()->setValue('special_requirements', $comment);
	}

	public function getSpecialRequirementsForm() : Form
	{
		if(!$this->comment_form) {
			$comment = new Form_Field_Textarea('special_requirements', '');
			$comment->setDefaultValue( $this->getSpecialRequirements() );
			$comment->setPlaceholder(Tr::_('Do you have any special requests?'));
			$comment->setFieldValueCatcher(function($value) {
				$this->setSpecialRequirements($value);
			});

			$this->comment_form = new Form('cash_desk_special_requirements_form', [$comment] );
		}

		return $this->comment_form;
	}
}