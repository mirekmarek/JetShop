<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\MarketplaceIntegration\Heureka;
use Exception;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;

class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller {
	
	protected Config_PerShop $config;
	
	public function default_Action() : void
	{
		$eshop = $this->getEshop();
		/**
		 * @var Main $module
		 * @var Config_PerShop $config
		 */
		$module = $this->getModule();
		
		$config = $module->getEshopConfig( $eshop );
		$this->config = $config;
		
		$this->view->setVar('config', $config);
		
		$server_service = $module->getSysServicesDefinitions()[$eshop->getKey()]??null;
		
		if($server_service) {
			$server_base_URL = $server_service->getURL();
			
			$server_API_URLs = [
				$server_base_URL.'/products/availability/' => 'GET products/availability',
				$server_base_URL.'/payment/delivery/' => 'GET payment/delivery',
				$server_base_URL.'/order/send/' => 'POST order/send',
				$server_base_URL.'/order/status/' => 'GET order/status',
				$server_base_URL.'/order/cancel/' => 'PUT order/cancel',
				$server_base_URL.'/payment/status/' => 'PUT payment/status',
			];
		} else {
			$server_API_URLs = [];
		}
		
		$this->view->setVar('server_API_URLs', $server_API_URLs);
		

		
		$this->handleMainCfgForm();
		$this->handleDeliveryMap();
		$this->handlePaymentMap();
		
		$this->output('control-centre/default');
	}
	
	protected function saveConfig() : void
	{
		$ok = true;
		try {
			$this->config->saveConfigFile();
		} catch( Exception $e ) {
			$ok = false;
			UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
		}
		
		if($ok) {
			UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
		}
		
	}
	
	public function handleMainCfgForm() : void
	{
		
		$form = $this->config->createForm('config_form');
		
		if( $form->catch() ) {
			$this->saveConfig();
			Http_Headers::reload();
		}
		
		$this->view->setVar('form', $form);
	}
	
	public function handleDeliveryMap() : void
	{
		$new_item = new Config_DeliveryMapItem();
		$add_delivery_method_form = $new_item->getAddForm();
		$this->view->setVar('add_delivery_method_form', $add_delivery_method_form);
		
		if($add_delivery_method_form->catch()) {
			$this->config->setDeliveryMapItem( $new_item );
			$this->saveConfig();
			Http_Headers::reload();
		}
		
		if( ($id=Http_Request::GET()->getInt('unset_delivery_method')) ) {
			$this->config->unsetDeliveryMapItem( $id );
			$this->saveConfig();
			Http_Headers::reload(unset_GET_params: ['unset_delivery_method']);
		}
		
		foreach($this->config->getDeliveryMap() as $item) {
			if($item->getEditForm()->catch()) {
				$this->saveConfig();
				Http_Headers::reload();
			}
		}
	}
	
	
	public function handlePaymentMap() : void
	{
		$new_item = new Config_PaymentMapItem();
		$add_delivery_method_form = $new_item->getAddForm();
		$this->view->setVar('add_payment_method_form', $add_delivery_method_form);
		
		if($add_delivery_method_form->catch()) {
			$this->config->setPaymentMapItem( $new_item );
			$this->saveConfig();
			Http_Headers::reload();
		}
		
		if( ($id=Http_Request::GET()->getInt('unset_payment_method')) ) {
			$this->config->unsetPaymentMapItem( $id );
			$this->saveConfig();
			Http_Headers::reload(unset_GET_params: ['unset_payment_method']);
		}
		
		foreach($this->config->getPaymentMap() as $item) {
			if($item->getEditForm()->catch()) {
				$this->saveConfig();
				Http_Headers::reload();
			}
		}
	}
	
}