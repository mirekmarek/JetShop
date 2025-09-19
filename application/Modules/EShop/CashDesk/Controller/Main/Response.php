<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\CashDesk;


use Jet\AJAX;
use JetApplication\Application_Service_EShop;

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
	
	public function redirect( string $url ) : void
	{
		$response = [
			'redirect' => $url
		];
		
		AJAX::commonResponse( $response );
	}

	public function response() : void
	{
		$cash_desk = $this->controller->getCashDesk();

		$this->addSnippet('confirm');
		$this->addSnippet('status_bar');

		$response = [
			'ok' => $this->ok,
			'is_ready' => $cash_desk->isReady(),
			'step' => $cash_desk->getCurrentStep(),
			'billing_address_editable' => $cash_desk->isBillingAddressEditable(),
			'delivery_address_editable' => $cash_desk->isDeliveryAddressEditable(),
			'snippets' => [],
			'data' => $this->data,
			'errors' => $this->errors,
		];

		$response['selected_delivery'] = $cash_desk->getSelectedDeliveryMethod()->getId();
		$response['selected_payment'] = $cash_desk->getSelectedPaymentMethod()->getId();
		
		$magic_tags_module = Application_Service_EShop::MagicTags();

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
			
			if($magic_tags_module) {
				$response['snippets'][$id]  = $magic_tags_module->processText( $response['snippets'][$id] );
			}
			
		}
		
		$response['snippets']['_measuring_codes_'] = Application_Service_EShop::AnalyticsManager()?->checkoutInProgress( $cash_desk );
		
		AJAX::commonResponse($response);

	}
}