<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ImageManager;


use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_FileImage;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\MVC_View;
use Jet\SysConf_Path;

class CommonImageManager {
	protected string $entity;
	protected int $entity_id;
	protected MVC_View $view;
	
	public function __construct( string $entity, int $entity_id, MVC_View $view )
	{
		$this->entity = $entity;
		$this->entity_id = $entity_id;
		$this->view = $view;
	}
	
	public function handle() : string
	{
		
		$images = new Form_Field_FileImage( 'image' );
		$images->setErrorMessages( [
			Form_Field_FileImage::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Please upload image',
			Form_Field_FileImage::ERROR_CODE_FILE_IS_TOO_LARGE    => 'File is too large'
		] );
		$images->setAllowMultipleUpload( true );
		
		$form = new Form( 'common_image_manager_upload', [
			$images
		] );
		
		$this->view->setVar( 'form', $form );
		
		$dir = $this->getDir();
		
		if( $form->catchInput() ) {
			$ok = false;
			if( $form->validate() ) {
				$ok = true;
				
				foreach( $images->getValidFiles() as $file ) {
					IO_File::moveUploadedFile(
						source_path: $file->getTmpFilePath(),
						target_path: $dir . $file->getFileName(),
						overwrite_if_exists: true
					);
				}
			}
			
			$this->view->setVar('images', $this->getFiles());
			
			AJAX::operationResponse(
				success: $ok,
				snippets: [
					'common_image_manager_form'   => $this->view->render( 'common-image-manager/form' ),
					'common_image_manager_images' => $this->view->render( 'common-image-manager/images' ),
				]
			);
			
		}
		
		if(($delete_file=Http_Request::GET()->getString('delete_image'))) {
			$this->deleteFile( $delete_file );
			
			$this->view->setVar('images', $this->getFiles());
			
			AJAX::snippetResponse( $this->view->render( 'common-image-manager/images' ) );
		}
		
		$this->view->setVar('images', $this->getFiles());
		
		return $this->view->render( 'common-image-manager' );
		
	}
	
	protected function getDir(): string
	{
		$entity = $this->entity;
		$entity_id = $this->entity_id;
		
		$dir = SysConf_Path::getImages() . $entity . '/' . $entity_id . '/';
		if( !IO_Dir::exists( $dir ) ) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	/**
	 * @return CommonImageManager_Image[]
	 */
	protected function getFiles(): array
	{
		$entity = $this->entity;
		$entity_id = $this->entity_id;
		
		$dir = $this->getDir();
		
		$list = [];
		foreach(IO_Dir::getFilesList( $dir ) as $path=>$name) {
			$list[] = new CommonImageManager_Image(
				$entity, $entity_id, $name
			);
		}
		
		return $list;
	}
	
	
	protected function deleteFile( string $file ): void
	{
		$entity = $this->entity;
		$entity_id = $this->entity_id;
		
		$path = $this->getDir().$file;
		if(IO_File::exists($path)) {
			IO_File::delete( $path );
		}
	}
	
}