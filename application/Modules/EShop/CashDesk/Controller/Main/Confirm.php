<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;


use Jet\Http_Request;

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
		$response = new Controller_Main_Response( $this );
		$cash_desk = $this->cash_desk;
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
		
		$this->cash_desk->resetDiscounts();
		
		$response->addSnippet('delivery');
		$response->addSnippet('overview');
		

		$response->response();
	}

	public function confirm_save_special_requirements_Action() : void
	{
		$response = new Controller_Main_Response( $this );

		$form = $this->cash_desk->getSpecialRequirementsForm();
		$form->catch();
		
		$response->response();
	}


	public function confirm_send_Action() : void
	{

		$response = new Controller_Main_Response( $this );

		if(!($order=$this->cash_desk->saveOrder())) {
			$response->error();
		} else {
			$response->redirect( $order->getPaymentPageURL() );
		}
		
		$response->response();

	}



}
