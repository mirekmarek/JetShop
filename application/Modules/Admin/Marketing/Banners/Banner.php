<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Marketing\Banners;

use Jet\Form;
use Jet\Form_Field_File;
use Jet\Form_Field_File_UploadedFile;
use Jet\Form_Field_FileImage;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_Banner;

class Banner extends Marketing_Banner implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;
	
	public function getEditURL(): string
	{
		return Main::getEditUrl( $this->id );
	}
	
	protected function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupForm( Form $form ) : void
	{
		$form->removeField('relevance_mode');
	}
	
	
	
	
	protected array $upload_forms = [];
	
	protected function catchUploadForm( Form $form ) : bool
	{
		if($form->catchInput()) {
			$form->catch();
			
			return true;
		}
		return false;
	}
	
	protected function getUploadImageForm($form_name, callable $setter ) : Form
	{
		if(!isset($this->upload_forms[$form_name])) {
			$image = new Form_Field_FileImage('image');
			
			$image->setFieldValueCatcher( function() use ($image, $setter) {
				foreach($image->getValidFiles() as $file) {
					$setter( $file );
				}
			} );
			$this->upload_forms[$form_name] = new Form($form_name, [$image]);
		}
		
		return $this->upload_forms[$form_name];
	}
	
	protected function getUploadVideoForm($form_name, callable $setter ) : Form
	{
		if(!isset($this->upload_forms[$form_name])) {
			$video = new Form_Field_File('video');
			$video->setAllowedMimeTypes([
				'video/mp4'
			]);
			$video->setErrorMessages([
				Form_Field_File::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Please upload video'
			]);
			
			$video->setFieldValueCatcher( function() use ($video, $setter) {
				foreach($video->getValidFiles() as $file) {
					$setter( $file );
				}
			} );
			$this->upload_forms[$form_name] = new Form($form_name, [$video]);
		}
		
		return $this->upload_forms[$form_name];
	}
	
	public function getUploadForm_MainImage() : Form
	{
		return $this->getUploadImageForm(
			'upload_form_main_image',
			function(Form_Field_File_UploadedFile $file) {
				$this->setImageMain($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MainImage() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MainImage() );
	}
	
	public function getUploadForm_MobileImage() : Form
	{
		return $this->getUploadImageForm(
			'upload_form_mobile_image',
			function(Form_Field_File_UploadedFile $file) {
				$this->setImageMobile($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MobileImage() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MobileImage() );
	}
	
	
	
	
	public function getUploadForm_MainVideo() : Form
	{
		return $this->getUploadVideoForm(
			'upload_form_main_video',
			function(Form_Field_File_UploadedFile $file) {
				$this->setVideoMain($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MainVideo() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MainVideo() );
	}
	
	public function getUploadForm_MobileVideo() : Form
	{
		return $this->getUploadVideoForm(
			'upload_form_mobile_video',
			function(Form_Field_File_UploadedFile $file) {
				$this->setVideoMobile($file);
				$this->save();
			}
		);
	}
	
	public function catchUploadForm_MobileVideo() : bool
	{
		return $this->catchUploadForm( $this->getUploadForm_MobileVideo() );
	}
	
	
}