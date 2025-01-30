<?php
namespace JetShop;

use Jet\Application;
use Jet\Data_DateTime;
use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;


use Jet\IO_Dir;
use Jet\IO_File;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\EShopEntity_Note_File;

#[DataModel_Definition(
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_EShopEntity_Note extends EShopEntity_Basic implements EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $entity_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_added = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $sent_to_customer = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_email_address = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $subject = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999
	)]
	protected string $note = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999
	)]
	protected string $files = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $administrator = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $administrator_id = 0;
	
	
	
	public function getDateAdded(): ?Data_DateTime
	{
		return $this->date_added;
	}
	
	public function setDateAdded( Data_DateTime|string|null $date_added ): void
	{
		$this->date_added = Data_DateTime::catchDateTime( $date_added );
	}
	
	public function getSentToCustomer(): bool
	{
		return $this->sent_to_customer;
	}
	
	public function setSentToCustomer( bool $sent_to_customer ): void
	{
		$this->sent_to_customer = $sent_to_customer;
	}
	
	public function getCustomerEmailAddress(): string
	{
		return $this->customer_email_address;
	}
	
	public function setCustomerEmailAddress( string $customer_email_address ): void
	{
		$this->customer_email_address = $customer_email_address;
	}
	
	public function getSubject(): string
	{
		return $this->subject;
	}
	
	public function setSubject( string $subject ): void
	{
		$this->subject = $subject;
	}
	
	
	public function getNote(): string
	{
		return $this->note;
	}
	
	public function setNote( string $note ): void
	{
		$this->note = $note;
	}
	
	public function getAdministrator(): string
	{
		return $this->administrator;
	}
	
	public function setAdministrator( string $administrator ): void
	{
		$this->administrator = $administrator;
	}
	
	public function getAdministratorId(): int
	{
		return $this->administrator_id;
	}
	
	public function setAdministratorId( int $administrator_id ): void
	{
		$this->administrator_id = $administrator_id;
	}
	
	abstract protected function getUploadedFilesDirPath() : string;
	
	abstract protected function getFilesDirPath() : string;
	
	/**
	 * @return EShopEntity_Note_File[]
	 */
	public function getFiles() : array
	{
		if(!$this->files) {
			return [];
		}
		
		$_files =  explode(',', $this->files);
		
		$files = [];
		
		foreach($_files as $file_name) {
			$f = new EShopEntity_Note_File( $this->getFilesDirPath(), $file_name );
			$f->setNoteId( $this->id );
			$files[] = $f;
		}
		
		return $files;
	}
	
	public function setFiles( array $files ): void
	{
		$this->files = implode(',', $files);
		$this->save();
	}
	
	
	/**
	 * @param int $entity_id
	 *
	 * @return static[]
	 */
	public static function getNotesList( int $entity_id ) : array
	{
		return static::fetch(
			[''=>[
				'entity_id' => $entity_id
			]],
			order_by: ['-date_added']
		);
	}
	
	
	public function uploadFiles() : void
	{
		$dir = $this->getUploadedFilesDirPath();
		
		foreach($_FILES['files']['tmp_name'] as $i=>$tmp_name) {
			$name = Data_Text::removeAccents( $_FILES['files']['name'][$i] );
			
			IO_File::moveUploadedFile( $tmp_name, $dir.$name );
		}
	}
	
	/**
	 * @return EShopEntity_Note_File[]
	 */
	public function getUploadedFiles() : array
	{
		$dir = $this->getUploadedFilesDirPath();
		
		$files = [];
		
		foreach(IO_Dir::getFilesList($dir) as $file_name) {
			$files[] = new EShopEntity_Note_File( $dir, $file_name );
		}
		
		return $files;
	}
	
	
	public function deleteUploadedFile( string $file ) : void
	{
		foreach($this->getUploadedFiles() as $f) {
			if($f->getName()==$file) {
				IO_File::delete( $f->getPath() );
				return;
			}
		}
	}
	
	public function showUploadedFile( string $file ) : void
	{
		foreach($this->getUploadedFiles() as $f) {
			if($f->getName()==$file) {
				IO_File::send( $f->getPath() );
				break;
			}
		}
		
		Application::end();
	}
	
	public function saveFiles() : void
	{
		$files = [];
		
		$target_dir = $this->getFilesDirPath();
		
		foreach($this->getUploadedFiles() as $f ) {
			IO_File::move( $f->getPath(), $target_dir.$f->getName() );
			
			$files[] = $f->getName();
		}
		
		$this->setFiles( $files );
	}
	
	public function showFile( string $file ) : void
	{
		
		foreach( $this->getFiles() as $f ) {
			if( $f->getName()==$file ) {
				IO_File::send( $f->getPath() );
				break;
			}
		}
		Application::end();
		
	}
	
}