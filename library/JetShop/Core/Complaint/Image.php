<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Data_DateTime;
use Jet\Data_Image;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

use Jet\Form_Field_File_UploadedFile;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\Complaint_Image;
use JetApplication\EShopEntity_Basic;
use JetApplication\Complaint;

#[DataModel_Definition(
	name: 'complaints_images',
	database_table_name: 'complaints_images',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name' => 'id'],
	parent_model_class: Complaint::class
)]
abstract class Core_Complaint_Image extends EShopEntity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $complaint_id = 0;
	
	protected ?Complaint $complaint = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_added = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $mime_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $size = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $locked = false;
	
	protected function getImagesDirPath() : string
	{
		$dir = SysConf_Path::getData().'complaint_images/'.$this->complaint->getEshop()->getKey().'/'.$this->complaint->getId().'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	public function getPath() : string
	{
		return $this->getImagesDirPath().$this->name;
	}
	
	public function getThbPath() : string
	{
		$thb_path = $this->getImagesDirPath().'thb___'.$this->name;
		if(IO_File::exists( $thb_path )) {
			return $thb_path;
		}
		
		$path = $this->getPath();
		if(!IO_File::isReadable($path)) {
			return $thb_path;
		}
		
		$image = new Data_Image( $path );
		$image->createThumbnail( $thb_path, 150, 150 );
		
		return $thb_path;
	}
	
	public function getComplaintId(): int
	{
		return $this->complaint_id;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->complaint = $complaint;
		$this->complaint_id = $complaint->getId();
	}
	
	
	public function getDateAdded(): ?Data_DateTime
	{
		return $this->date_added;
	}
	
	public function setDateAdded( Data_DateTime|string|null $date_added ): void
	{
		$this->date_added = Data_DateTime::catchDateTime( $date_added );
	}
	
	public function getName(): string
	{
		return $this->name;
	}

	public function setName( string $name ): void
	{
		$this->name = $name;
	}

	public function getMimeType(): string
	{
		return $this->mime_type;
	}

	public function setMimeType( string $mime_type ): void
	{
		$this->mime_type = $mime_type;
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function setSize( int $size ): void
	{
		$this->size = $size;
	}
	
	public function getURL() : string
	{
		return $this->complaint->getURL().'&img='.$this->id;
	}
	
	public function getThbURL() : string
	{
		return $this->complaint->getURL().'&thb='.$this->id;
	}
	
	public function isLocked(): bool
	{
		return $this->locked;
	}
	
	public function setLocked( bool $locked ): void
	{
		$this->locked = $locked;
	}
	
	
	
	/**
	 *
	 * @return static[]
	 */
	public static function getForComplaint( Complaint $complaint ): array
	{
		$images = static::fetch(
			[
				'complaints_images' => [
					'complaint_id' => $complaint->getId()
				]
			],
			order_by: ['-date_added'],
			item_key_generator: function( Complaint_Image $image ) {
				return $image->getId();
			}
		);
		
		foreach($images as $img) {
			$img->setComplaint( $complaint );
		}
		
		return $images;
	}
	
	public static function uploadImage( Complaint $complaint, Form_Field_File_UploadedFile $file ) : void
	{
		foreach($complaint->getImages() as $e_img) {
			if($e_img->getName()==$file->getFileName()) {
				if($e_img->isLocked()) {
					return;
				}
				
				$e_img->setName( $file->getFileName() );
				$e_img->setSize( $file->getSize() );
				$e_img->setMimeType( $file->getMimeType() );
				
				IO_File::moveUploadedFile( $file->getTmpFilePath(), $e_img->getPath() );
				
				$e_img->save();

				return;
			}
		}
		
		$img = new static();
		$img->setComplaint( $complaint );
		$img->setName( $file->getFileName() );
		$img->setSize( $file->getSize() );
		$img->setMimeType( $file->getMimeType() );
		
		IO_File::moveUploadedFile( $file->getTmpFilePath(), $img->getPath() );
		
		$img->save();
	}
	
	public function show() : void
	{
		IO_File::send(
			$this->getPath()
		);
	}
	
	public function showThb() : void
	{
		IO_File::send(
			$this->getThbPath()
		);
	}
	
	public function afterDelete(): void
	{
		$file = $this->getPath();
		
		if(IO_File::exists($file)) {
			IO_File::delete( $file );
		}
		
		$file = $this->getThbPath();
		
		if(IO_File::exists($file)) {
			IO_File::delete( $file );
		}
		
	}
}