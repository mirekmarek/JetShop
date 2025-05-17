<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\AJAX;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\IO_File;
use Jet\Locale;
use Jet\Logger;
use Jet\MVC;
use Jet\MVC_Base_Interface;
use Jet\MVC_View;
use Jet\MVC_Router;
use Jet\Auth;
use Jet\SysConf_Jet_ErrorPages;
use Jet\SysConf_Jet_Form;
use Jet\SysConf_Jet_UI;
use Jet\Tr;
use Jet\UI_messages;

/**
 *
 */
class Application_Admin
{
	/**
	 * @return string
	 */
	public static function getBaseId() : string
	{
		return 'admin';
	}

	/**
	 * @return MVC_Base_Interface
	 */
	public static function getBase() : MVC_Base_Interface
	{
		return MVC::getBase( static::getBaseId() );
	}

	/**
	 * @param MVC_Router $router
	 */
	public static function init( MVC_Router $router ) : void
	{
		
		static::handleLocaleSwitch( $router );
		
		Application::initErrorPages( $router );
		Logger::setLogger( new Logger_Admin() );
		Auth::setController( new Auth_Controller_Admin() );

		Admin_Managers::UI()->handleCurrentPreferredShop();

		SysConf_Jet_UI::setViewsDir( $router->getBase()->getViewsPath() . 'ui/' );
		SysConf_Jet_Form::setDefaultViewsDir( $router->getBase()->getViewsPath() . 'form/' );
		SysConf_Jet_ErrorPages::setErrorPagesDir( $router->getBase()->getPagesDataPath( $router->getLocale() ) );
		
	}
	
	/**
	 * @return Locale[]
	 */
	public static function getAvlLovales() : array
	{
		$base = static::getBase();
		$_avl = $base->getLocalizedData( $base->getDefaultLocale() )->getParameter('avl_locales', default_value: $base->getDefaultLocale()->toString() );
		$_avl = explode(',', $_avl);
		
		$avl = [];
		
		foreach( $_avl as $locale_str ) {
			$locale = new Locale( $locale_str );
			$avl[$locale_str] = $locale;
		}
		
		return $avl;
	}
	
	protected static function handleLocaleSwitch( MVC_Router $router ): void
	{
		$base = static::getBase();
		$avl_locales = static::getAvlLovales();
		$default_locale = array_values(static::getAvlLovales())[0];
		
		$cookie_name = 'adm_locale';
		
		$setCookie = function( Locale $locale ) use ($cookie_name, $base) : void {
			$URL = 'https://'.$base->getLocalizedData( $base->getDefaultLocale() )->getDefaultURL();
			
			$URL = parse_url( $URL );
			
			setcookie(
				name: $cookie_name,
				value: $locale->toString(),
				expires_or_options: time()+(86400*365*10),
				path: '/', //$URL['path'],
				domain: $URL['host']
			);
			$_COOKIE[$cookie_name] = $locale->toString();
		};
		
		
		
		if(
			!isset($_COOKIE[$cookie_name]) ||
			!isset($avl_locales[$_COOKIE[$cookie_name]])
			) {
			
			$setCookie( $default_locale );
		}
		
		$GET = Http_Request::GET();
		if( $GET->exists( 'set_locale' ) ) {
			$selected_locale = $GET->getString('set_locale', default_value:  $default_locale->toString(), valid_values: array_keys($avl_locales));
			$selected_locale = new Locale( $selected_locale );
			
			$setCookie( $selected_locale );
			
			Http_Headers::reload( unset_GET_params: ['set_locale'] );
		}
		
		$selected_locale_str = $_COOKIE[$cookie_name];
		$selected_locale = new Locale($selected_locale_str);
		
		Locale::setCurrentLocale( $selected_locale );
		Tr::setCurrentLocale( $selected_locale );
		//$router->setLocale( $selected_locale );
	}

	/**
	 * @param string $dialog_id
	 * @param array  $options
	 *
	 * @return null|string
	 */
	public static function requireDialog( string $dialog_id, array $options=[] ) : null|string
	{

		$page = MVC::getPage('dialog-'.$dialog_id);

		if(
			!$page ||
			!$page->getContent()
		) {
			return null;
		}

		$content = $page->getContent()[0];

		$module = $content->getModuleInstance();

		if(!$module) {
			return null;
		}

		$view = new MVC_View( $module->getViewsDir().'admin/dialog-hooks/' );
		foreach( $options as $k=>$v ) {
			$view->setVar( $k, $v );
		}

		return $view->render( $dialog_id );

	}

	public static function handleUploadTooLarge() : void
	{
		if( Http_Request::postMaxSizeExceeded() ) {
			$error_message = 'You are uploading too large files<br/>'
				.'<br/>'
				.'The maximum size of one uploaded file is: <b>%max_upload_size%</b><br/>'
				.'The maximum number of uploaded files is: <b>%max_file_uploads%</b><br/>';

			$error_message = UI_messages::createDanger( Tr::_($error_message, [
				'max_upload_size' => Locale::getCurrentLocale()->formatSize(IO_File::getMaxUploadSize()),
				'max_file_uploads' => Locale::getCurrentLocale()->formatInt(IO_File::getMaxFileUploads())
			], Tr::COMMON_DICTIONARY) )->toString();

			AJAX::operationResponse(false, [
				'system-messages-area' => $error_message
			]);

		}

	}
}