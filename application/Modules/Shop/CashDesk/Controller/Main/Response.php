<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Shop\CashDesk;

use Jet\AJAX;
use JetShop\CashDesk;

class Controller_Main_Response {

	protected Controller_Main $controller;

	protected string $state = '';

	protected bool $ok = true;

	protected array $snippets = [];

	protected array $data = [];

	protected array $errors = [];

	public function __construct( Controller_Main $controller )
	{
		$this->controller = $controller;
	}

	public function error( string $message='' ) : void
	{
		$this->ok = false;
		$this->errors[] = $message;
	}

	public function addSnippet( string $id, string|object $view='' ) : void
	{
		$this->snippets[$id] = $view;
	}

	public function setData( string $key, mixed $data ) : void
	{
		$this->data[$key] = $data;
	}

	public function response()
	{
		$cash_desk = CashDesk::get();

		$this->addSnippet('confirm');
		$this->addSnippet('status_bar');

		$response = [
			'ok' => $this->ok,
			'step' => $cash_desk->getCurrentStep(),
			'billing_address_editable' => $cash_desk->isBillingAddressEditable(),
			'delivery_address_editable' => $cash_desk->isDeliveryAddressEditable(),
			'snippets' => [],
			'data' => $this->data,
			'errors' => $this->errors,
		];

		$response['selected_delivery'] = $cash_desk->getSelectedDeliveryMethod()->getCode();
		//$response['selected_payment'] = $cash_desk->getSelectedPaymentMethod()->getCode();


		foreach( $this->snippets as $id=>$view_script ) {
			if(!$view_script) {
				$view_script = $id;
				$id = 'cash_desk_'.$id;
			}

			if(is_object($view_script)) {
				$response['snippets'][$id] = (string)$view_script;

			} else {
				$response['snippets'][$id] = $this->controller->getView()->render( $view_script );
			}
		}


		AJAX::commonResponse($response);

	}
}