<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Shop\CashDesk;

use Jet\Http_Request;
use JetShop\CashDesk;

trait Controller_Main_Confirm {

	public function getControllerRouter_Confirm() : void
	{

		$GET = Http_Request::GET();
		$action = $GET->getString('action');

		$this->router->addAction('confirm_toggle_agree_flag')->setResolver(function() use ($action) {
			return $action=='confirm_toggle_agree_flag';
		});

		$this->router->addAction('confirm_save_special_requirements')->setResolver(function() use ($action) {
			return $action=='confirm_save_special_requirements';
		});

		$this->router->addAction('confirm_send')->setResolver(function() use ($action) {
			return $action=='confirm_send';
		});


	}

	public function _Action() : void
	{
	}


	public function confirm_toggle_agree_flag_Action() : void
	{
		/**
		 * @var Controller_Main $this
		 */

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();
		$GET = Http_Request::GET();

		$code = $GET->getString('flag');
		$state = $GET->getBool('state');

		$flag = $cash_desk->getAgreeFlag($code);

		if($flag) {
			$cash_desk->setAgreeFlagState( $code, $state );

			if($flag->isMandatory() && !$flag->isChecked()) {
				$flag->setShowError(true);
			}
		}

		$response->response();
	}

	public function confirm_save_special_requirements_Action() : void
	{
		/**
		 * @var Controller_Main $this
		 */

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		$form = $cash_desk->getSpecialRequirementsForm();
		if($form->catchInput() && $form->validate()) {
			$form->catchData();
		}

		$response->response();
	}


	public function confirm_send_Action() : void
	{

		/**
		 * @var Controller_Main $this
		 */

		$response = new Controller_Main_Response( $this );
		$cash_desk = CashDesk::get();

		if(!$cash_desk->saveOrder()) {
			$response->error();
		}


		$response->response();

	}



}
