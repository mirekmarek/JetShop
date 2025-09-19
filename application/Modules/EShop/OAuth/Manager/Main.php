<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\OAuth\Manager;


use Jet\Auth;
use Jet\Data_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use Jet\Session;
use JetApplication\Customer;
use JetApplication\Application_Service_EShop;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\Application_Service_EShop_OAuthBackendModule;
use JetApplication\Application_Service_EShop_OAuth;
use JetApplication\EShop_Pages;
use JetApplication\EShop_OAuth_UserHandler;
use JetApplication\EShops;

class Main extends Application_Service_EShop_OAuth implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected ?array $oauth_modules = null;
	
	/**
	 * @return Application_Service_EShop_OAuthBackendModule[]
	 */
	public function getOAuthModules(): array
	{
		if($this->oauth_modules===null) {
			$this->oauth_modules = [];
			$modules = Application_Service_EShop::OAuthModules();
			
			foreach( $modules as $oauth_module ) {
				$service_id = $oauth_module->getOAuthServiceID();
				
				$oauth_module->setHandlerUrl( EShop_Pages::OAuth()->getURL(path_fragments: [$service_id]) );
				
				$this->oauth_modules[$service_id] = $oauth_module;
				
			}
		}
		
		return $this->oauth_modules;
	}
	
	
	public function handle( Application_Service_EShop_OAuthBackendModule $module ): void
	{
		$this->handleRequest( $module );
		$this->handleOAuthServiceReturn( $module );
	}
	
	protected function getSession() : Session
	{
		return new Session( 'OAuthSession' );
	}
	
	protected function handleRequest( Application_Service_EShop_OAuthBackendModule $module ): void
	{
		$POST = Http_Request::POST();
		
		
		if(
			$POST->exists( 'login' ) &&
			($redirect_page_id = $POST->getString( 'redirect_page_id' )) &&
			($page = MVC::getPage( $redirect_page_id ))
		) {
			$session = $this->getSession();
			
			$session->setValue( 'redirect_page_id', $redirect_page_id );
			$session->setValue( 'path_fragments', $POST->getRaw( 'path_fragments', '' ) );
			
			Http_Headers::movedTemporary( $module->getOAuthServiceAuthorizationLink() );
		}
		
	}
	
	protected function handleOAuthServiceReturn( Application_Service_EShop_OAuthBackendModule $module ): void
	{
		$session = $this->getSession();
		
		if(
			($redirect_page_id = $session->getValue( 'redirect_page_id', '' ))
		) {
			
			$path_fragments = $session->getValue( 'path_fragments', '' );
			
			if( !$path_fragments ) {
				$path_fragments = [];
			} else {
				$path_fragments = explode( '/', $path_fragments );
			}
			
			$user_handler = $this->authUserHandler();
			$user_handler->setOauthService( $module->getOAuthServiceID() );
			
			if( $module->handleOAuthServiceReturn(
				$user_handler
			)) {
				$session->setValue('redirect_page_id', '');
				$session->setValue('path_fragments', '');
				
				$user_handler->handle();
				
				Http_Headers::movedTemporary(
					MVC::getPage( $redirect_page_id )->getURL( path_fragments: $path_fragments )
				);
			} else {
				Http_Headers::movedTemporary('/');
			}
			
			
			
		}
	}
	
	protected function authUserHandler(): EShop_OAuth_UserHandler
	{
		return new class extends EShop_OAuth_UserHandler {
			public function handle(): void
			{
				if( !($customer = Customer::getByOAuth( $this->getOauthService(), $this->getOauthUserId() )) ) {
					
					if( !($customer = Customer::getByEmail( $this->getOauthUserEmail() )) ) {
						$customer = new Customer();
						$customer->setEmail( $this->getOauthUserEmail() );
						$customer->setEshop( EShops::getCurrent() );
						$customer->setRegistrationIp( Http_Request::clientIP() );
						$customer->setRegistrationDateTime( Data_DateTime::now() );
						
					}
					
					$customer->setOauthService( $this->getOauthService() );
					$customer->setOauthKey( $this->getOauthUserId() );
					
					$customer->save();
				}
				
				Auth::loginUser( $customer );
			}
		};
		
	}
	
	
	public function renderLoginButton( Application_Service_EShop_OAuthBackendModule $module ) : string
	{
		$view = $this->getView();
		
		$view->setVar('module', $module);
		
		return $view->render('button');
	}
	
	public function renderLoginButtons() : string
	{
		$view = $this->getView();
		
		$view->setVar('oauth', $this);
		
		return $view->render('buttons');
	}
}