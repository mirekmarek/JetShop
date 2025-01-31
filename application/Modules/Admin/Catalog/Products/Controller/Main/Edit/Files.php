<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_File;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Application_Admin;
use JetApplication\Files;
use JetApplication\Product_KindOfFile;
use JetApplication\Product;


trait Controller_Main_Edit_Files
{
	
	public function edit_files_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Files') );
		
		$this->view->setVar('item', $this->current_item);
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		
		$editable = $product->isEditable();
		
		
		if(
			$product->getType()==Product::PRODUCT_TYPE_VARIANT ||
			$product->getType()==Product::PRODUCT_TYPE_SET
		) {
			$editable = false;
		}
		
		$this->view->setVar('editable', $editable );
		$this->view->setVar('product', $product);
		
		
		
		if($editable) {
			
			$file_field = new Form_Field_File('file');
			$file_field->setErrorMessages([
				Form_Field_File::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Unsupported file type',
				Form_Field_File::ERROR_CODE_FILE_IS_TOO_LARGE => 'File is too large'
			
			]);
			$file_field->setAllowMultipleUpload( true );
			
			$kind_of_file = new Form_Field_Select('kind_of_file', 'Kind of file:');
			$kind_of_file->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
			]);
			$kind_of_file->setSelectOptions( Product_KindOfFile::getScope() );
			
			
			$form = new Form('file_upload_form', [
				$file_field,
				$kind_of_file
			]);
			
			$form->setAction( Http_Request::currentURI(['action'=>'upload_files']) );
			
			$this->view->setVar('upload_form', $form);
			
			
			$GET = Http_Request::GET();
			if( $GET->exists('action') ) {
				
				
				$updated = false;
				switch($GET->getString('action')) {
					case 'upload_files':
						Application_Admin::handleUploadTooLarge();
						
						if($form->catch()) {
							foreach($file_field->getValidFiles() as $file) {
								$file = Files::Manager()->uploadFile(
									$product,
									$file->getFileName(),
									$file->getTmpFilePath()
								);
								$product->addFile(
									$file,
									$kind_of_file->getValue()
								);
							}
							
							$updated = true;
						}
						
						break;
					case 'delete_files':
						foreach(explode(',', $GET->getString('files')) as $file_id) {
							$product->deleteFile( $file_id );
						}
						$updated = true;
						break;
					case 'save_sort_files':
						$product->sortFiles( explode(',', $GET->getString('files')) );
						$updated = true;
						break;
				}
				
				if($updated) {
					$product->save();
					
					AJAX::commonResponse(
						[
							'result' => 'ok',
							'snippets' => [
								'files_list' => $this->view->render('edit/files/list')
							]
						
						]
					);
					
				}
			}
			
		}
		
		
		
		
		
		$this->output( 'edit/files' );
		
	}
}