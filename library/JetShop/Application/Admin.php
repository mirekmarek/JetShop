<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek.2m@gmail.com>
 *
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 */
namespace JetShop;

use Jet\AJAX;
use Jet\Http_Request;
use Jet\IO_File;
use Jet\Locale;
use Jet\Logger;
use Jet\Mvc_Site;
use Jet\Mvc_Page;
use Jet\Mvc_View;
use Jet\Mvc_Router;
use Jet\Auth;
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
	public static function getSiteId() : string
	{
		return 'admin';
	}

	/**
	 * @return Mvc_Site
	 */
	public static function getSite() : Mvc_Site
	{
		return Mvc_Site::get( static::getSiteId() );
	}

	/**
	 * @param Mvc_Router $router
	 */
	public static function init( Mvc_Router $router ) : void
	{
		Application::initErrorPages( $router );
		Logger::setLogger( new Logger_Admin() );
		Auth::setController( new Auth_Controller_Admin() );

		//TOOD: vyber vychoziho shopu ...
		//TODO: serazeni shopu ...

		Shops::setCurrent( Shops::getDefault()->getCode() );

	}

	/**
	 * @param string $dialog_id
	 * @param array  $options
	 *
	 * @return null|string
	 */
	public static function requireDialog( string $dialog_id, array $options=[] ) : null|string
	{

		$page = Mvc_Page::get('dialog-'.$dialog_id);

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

		$view = new Mvc_View( $module->getViewsDir().'admin/dialog-hooks/' );
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
			], Tr::COMMON_NAMESPACE) )->toString();

			AJAX::formResponse(false, [
				'system-messages-area' => $error_message
			]);

		}

	}
}