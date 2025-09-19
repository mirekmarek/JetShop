<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ImageManager;


use Jet\Data_Image;
use Jet\Data_Image_Exception;
use Jet\Data_Text;
use Jet\Exception;
use Jet\Form;
use Jet\Form_Field_FileImage;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\Logger;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetApplication\Application_Service_Admin;
use JetApplication\EShop;

class Image {
	
	protected static string $thb_dir = '_thb';
	
	protected static ?string $root_path = null;
	
	protected static ?string $root_url = null;
	
	protected string $entity;
	
	protected string|int $object_id;
	
	protected string $image_class;
	
	protected string $image_title;
	
	protected ?Form $upload_form = null;
	
	protected ?Form $delete_form = null;
	
	/**
	 * @var callable|null
	 */
	protected $image_property_getter;
	
	/**
	 * @var callable|null
	 */
	protected $image_property_setter;
	
	protected ?EShop $eshop;
	
	public static function getRootPath(): string
	{
		if(!static::$root_path) {
			static::$root_path = SysConf_Path::getImages();
		}
		
		return static::$root_path;
	}
	
	public static function setRootPath( string $root_path ) : void
	{
		static::$root_path = $root_path;
	}
	
	
	public static function getThbDir() : string
	{
		return static::$thb_dir;
	}
	
	public static function setThbDir( string $thb_dir ) : void
	{
		static::$thb_dir = $thb_dir;
	}
	
	public static function getRootUrl() : string
	{
		if(!static::$root_url) {
			static::$root_url = Http_Request::baseURL().SysConf_URI::getImages();
		}
		
		return static::$root_url;
	}
	
	public static function setRootUrl( string $root_url ) : void
	{
		static::$root_url = $root_url;
	}
	
	
	
	public function __construct(
		string     $entity,
		string|int $object_id,
		string     $image_class='',
		string     $image_title='',
		?callable  $image_property_getter=null,
		?callable  $image_property_setter=null,
		?EShop     $eshop=null,
	) {
		$this->entity = $entity;
		$this->object_id = $object_id;
		$this->image_class = $image_class;
		$this->image_title = $image_title;
		$this->image_property_getter = $image_property_getter;
		$this->image_property_setter = $image_property_setter;
		
		$this->eshop = $eshop;
	}
	
	public static function generateKey( string $entity, string|int $object_id, string $image_class, ?EShop $eshop=null ) : string
	{
		$key = $entity.'_'.$object_id;
		
		
		if($image_class) {
			$key .= '_'.$image_class;
		}
		
		if($eshop) {
			$key .= '_'.$eshop->getKey();
		}
		
		return $key;
		
	}
	
	public function getKey() : string
	{
		return static::generateKey( $this->entity, $this->object_id, $this->image_class, $this->eshop );
	}
	
	public function getImagePropertyGetter(): ?callable
	{
		return $this->image_property_getter;
	}
	
	public function setImagePropertyGetter( ?callable $image_property_getter ): void
	{
		$this->image_property_getter = $image_property_getter;
	}
	
	public function getImagePropertySetter(): ?callable
	{
		return $this->image_property_setter;
	}
	
	public function setImagePropertySetter( ?callable $image_property_setter ): void
	{
		$this->image_property_setter = $image_property_setter;
	}
	
	
	
	protected function calcNumericPath() : string
	{
		$object_id = $this->object_id;
		
		if(is_string($object_id)) {
			return $object_id;
		}
		
		$map = [];
		
		$numerical_order = 100000;
		
		$number = $object_id;
		while( $numerical_order>=10 ) {
			$c = floor($number/$numerical_order);
			
			$map[$numerical_order] = $c;
			
			$number = $number-($c*$numerical_order);
			
			$numerical_order = $numerical_order/10;
		}
		
		return implode('/',$map);
	}
	
	protected function _getDir() : string
	{
		$dir = '';
		
		if($this->eshop) {
			$dir .= $this->eshop->getKey().'/';
		}
		
		$dir .= $this->entity.'/';
		
		if($this->image_class) {
			$dir .= $this->image_class.'/';
		}
		
		$dir .= $this->calcNumericPath().'/';
		
		return $dir;
	}
	
	
	public function getThbRootDir() : string
	{
		
		$dir = static::getThbDir().'/'.$this->_getDir();
		
		$full_path = static::getRootPath().$dir;
		if(!IO_Dir::exists($full_path)) {
			IO_Dir::create( $full_path );
		}
		
		return $dir;
	}
	
	public function getDirPath() : string
	{
		$dir = $this->_getDir();
		
		$full_path = static::getRootPath().$dir;

		if(!IO_Dir::exists($full_path)) {
			IO_Dir::create( $full_path );
		}
		
		return $dir;
	}
	
	public function upload( string $tmp_path, string $name ) : string
	{
		try {
			$image = new Data_Image( $tmp_path );
		} catch(Exception $e) {
			return '';
		}
		
		$file_name = pathinfo($name)['filename'];
		$file_name = Data_Text::removeAccents($file_name);
		$file_name = str_replace(' ', '_', $file_name);

		$target_path = $this->getDirPath().$file_name.image_type_to_extension( $image->getImgType() );
		
		if(IO_File::exists($target_path)) {
			IO_File::delete($target_path);
		}
		
		IO_File::copy( $tmp_path, static::getRootPath().$target_path );
		
		$this->setImage( $target_path );
		
		return $target_path;
	}
	
	public function delete() : void
	{
		if( !($current_image = $this->getImage()) ) {
			return;
		}
		
		$thb_dir = static::getRootPath().$this->getThbRootDir();

		
		$eshop = $this->eshop;
		$entity = $this->entity;
		$image_class = $this->image_class;
		
		
		
		$thumbnails = IO_Dir::getFilesList( $thb_dir, '*__'.basename($current_image) );
		
		foreach( $thumbnails as $path=>$file_name ) {
			IO_File::delete( $path );
		}
		
		$path = static::getRootPath().$current_image;
		if(IO_File::exists($path)) {
			IO_File::delete( $path );
		}
		
		$this->setImage('');
	}
	
	public function getImage() : string
	{
		$getter = $this->image_property_getter;
		if(!$getter) {
			return '';
		}
		
		return $getter() ? : '';
	}
	
	public function getImageFileName() : string
	{
		return basename($this->getImage());
	}
	
	
	public function setImage( string $value ) : void
	{
		$setter = $this->image_property_setter;
		
		if($setter) {
			$setter( $value );
		}
	}
	
	public function getUrl() : string
	{
		if( !($current_image = $this->getImage()) ) {
			return '';
		}
		
		return static::getRootUrl().$current_image;
	}
	
	public function getThumbnailUrl( int $max_w, int $max_h ) : string
	{
		
		if( !($current_image = $this->getImage()) ) {
			return '';
		}
		
		
		$thb_path = $this->getThbRootDir().$max_w.'x'.$max_h.'__'.basename($current_image);
		
		$thb_source_path = static::getRootPath().$current_image;
		$thb_target_path = static::getRootPath().$thb_path;
		
		$url = static::getRootUrl().$thb_path;
		
		if(!IO_File::exists($thb_source_path)) {
			return '';
		}
		
		if(!IO_File::exists($thb_target_path)) {
			$target_dir = dirname($thb_target_path);
			if(!IO_Dir::exists($target_dir)) {
				IO_Dir::create( $target_dir );
			}
			
			try {
				$image = new Data_Image( $thb_source_path );
				$image->createThumbnail( $thb_target_path, $max_w, $max_h );
			} catch(Data_Image_Exception $e) {
				return '';
			}
		}
		
		
		return $url;
	}
	
	
	public function getUploadForm() : Form
	{
		if(!$this->upload_form) {
			$image_field = new Form_Field_FileImage('image');
			$image_field->setErrorMessages([
				Form_Field_FileImage::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Please upload image',
				Form_Field_FileImage::ERROR_CODE_FILE_IS_TOO_LARGE => 'File is too large'
			
			]);
			
			
			$form = new Form('image_upload_form_'.$this->getKey(), [
				$image_field
			]);
			
			$form->setCustomTranslatorDictionary( Application_Service_Admin::Image()->getModuleManifest()->getName() );
			
			$this->upload_form = $form;
		}
		
		return $this->upload_form;
	}
	
	public function catchUploadForm() : ?bool
	{
		$form = $this->getUploadForm();
		
		if(!$form->catchInput()) {
			return null;
		}
		
		if( !$form->validate() ) {
			return false;
		}
		
		/**
		 * @var Form_Field_FileImage $image_field
		 */
		$image_field = $form->getField('image');
		
		$images = $image_field->getValidFiles();
		
		foreach($images as $image) {
			$image = $this->upload( $image->getTmpFilePath(), $image->getFileName() );
			
			if($image) {
				if($this->eshop) {
					Logger::success(
						event: 'image_uploaded:'.$this->entity.':'.$this->image_class,
						event_message: $this->entity.' '.$this->object_id.' image '.$this->image_class.' uploaded ('.$this->eshop->getKey().')',
						context_object_id: $this->object_id,
						context_object_data: [
							'entity'      => $this->entity,
							'object_id'   => $this->object_id,
							'image_class' => $this->image_class,
							'eshop'       => $this->eshop->getKey(),
							'image'       => $image
						]
					);
				} else {
					Logger::success(
						event: 'image_uploaded:'.$this->entity.':'.$this->image_class,
						event_message: $this->entity.' '.$this->object_id.' image '.$this->image_class.' uploaded',
						context_object_id: $this->object_id,
						context_object_data: [
							'entity'      => $this->entity,
							'object_id'   => $this->object_id,
							'image_class' => $this->image_class,
							'image'       => $image
						]
					);
				}
			}

			break;
		}
		
		return true;
	}
	
	
	public function getDeleteForm() : Form
	{
		if(!$this->delete_form) {
			$this->delete_form = new Form('image_delete_form_'.$this->getKey(), []);
		}
		
		return $this->delete_form;
		
	}
	
	public function catchImageDeleteForm() : ?bool
	{
		$form = $this->getDeleteForm();
		
		if(!$form->catchInput()) {
			return null;
		}
		
		if( !$form->validate() ) {
			return false;
		}
		
		$image = $this->getImage();
		
		if($image) {
			$this->delete();
			
			if($this->eshop) {
				Logger::success(
					event: 'image_deleted:'.$this->entity.':'.$this->image_class,
					event_message: $this->entity.' '.$this->object_id.' image '.$this->image_class.' deleted ('.$this->eshop->getKey().')',
					context_object_id: $this->object_id,
					context_object_data: [
						'entity'      => $this->entity,
						'object_id'   => $this->object_id,
						'image_class' => $this->image_class,
						'eshop'       => $this->eshop->getKey(),
						'image'       => $image
					]
				);
			} else {
				Logger::success(
					event: 'image_deleted:'.$this->entity.':'.$this->image_class,
					event_message: $this->entity.' '.$this->object_id.' image '.$this->image_class.' deleted',
					context_object_id: $this->object_id,
					context_object_data: [
						'entity'      => $this->entity,
						'object_id'   => $this->object_id,
						'image_class' => $this->image_class,
						'image'       => $image
					]
				);
			}
		}
		
		return true;
	}

	public function getEntity(): string
	{
		return $this->entity;
	}
	
	public function getObjectId(): int|string
	{
		return $this->object_id;
	}
	
	public function getImageClass(): string
	{
		return $this->image_class;
	}
	
	public function getImageTitle(): string
	{
		return $this->image_title;
	}
	
	public function getEshop(): ?EShop
	{
		return $this->eshop;
	}
	

	public function getHTMLElementId() : string
	{
		return 'image_'.$this->getKey();
	}
	
}