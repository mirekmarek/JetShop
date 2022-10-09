<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\AJAX;
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
		Application::initErrorPages( $router );
		Logger::setLogger( new Logger_Admin() );
		Auth::setController( new Auth_Controller_Admin() );

		Shops::handleCurrentAdminShop();

		SysConf_Jet_UI::setViewsDir( $router->getBase()->getViewsPath() . 'ui/' );
		SysConf_Jet_Form::setDefaultViewsDir( $router->getBase()->getViewsPath() . 'form/' );
		SysConf_Jet_ErrorPages::setErrorPagesDir( $router->getBase()->getPagesDataPath( $router->getLocale() ) );
		
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

	public static function handleUploadTooLarge()
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