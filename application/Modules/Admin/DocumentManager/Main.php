<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\DocumentManager;

use Jet\Factory_MVC;
use Jet\MVC_View;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use Jet\Tr;
use Jet\Translator;
use JetApplication\Admin_Managers_Document;


class Main extends Admin_Managers_Document
{
	protected static array $allowed_mime_types = [
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.oasis.opendocument.text',
		'application/rtf',
		'application/pdf',
		'application/zip',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'application/vnd.oasis.opendocument.spreadsheet',
		'text/csv',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.oasis.opendocument.presentation',
	];
	
	protected static array $mime_type_icons = [
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'file-word',
		'application/vnd.oasis.opendocument.text' => 'file-word',
		'application/rtf' => 'file-lines',
		'application/pdf' => 'file-pdf',
		'application/zip' => 'file-zipper',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'file-excel',
		'application/vnd.oasis.opendocument.spreadsheet' => 'file-excel',
		'text/csv' => 'file-csv',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'file-powerpoint',
		'application/vnd.oasis.opendocument.presentation' => 'file-powerpoint',
	];
	
	public static function getAllowedMimeTypes(): array
	{
		return static::$allowed_mime_types;
	}
	
	public static function getMimeTypeIcons(): array
	{
		return static::$mime_type_icons;
	}
	
	
	
	public static function getRootDirectoryPath(): string
	{
		return SysConf_Path::getBase().'doc/';
	}
	
	public static function getRootDirectoryURI(): string
	{
		return SysConf_URI::getBase().'doc/';
	}
	
	
	protected function initView(): MVC_View
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		return $view;
	}
	
	
	
	public function renderMain(): string
	{
		$res = '';
		Translator::setCurrentDictionaryTemporary( $this->module_manifest->getName(), function() use ( &$res ) {
			$view = $this->initView();
			$res = $view->render( 'main' );
		} );
		
		return $res;
	}
	
	public function renderStandardManagement(): string
	{
		$view = $this->initView();
		$view->setVar( 'manager', $this );
		$res = $view->render( 'standard-management' );
		
		return $res;
	}
	
	
	public function commonDocumentManager( string $entity, int $entity_id ): string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($entity, $entity_id) {
				$manager = new CommonDocumentManager($entity, $entity_id, $this->initView());
				return $manager->handle();
			}
		);
	}
	
}