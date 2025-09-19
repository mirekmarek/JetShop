<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Email;
use Jet\Form_Field_File;
use Jet\Form_Field_File_UploadedFile;
use Jet\Form_Field_FileImage;
use Jet\Locale;
use JetApplication\Application_Service_Admin_InappropriateContentReporting;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasImageGallery_Interface;
use JetApplication\EShopEntity_HasImageGallery_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\InappropriateContentReporting;
use JetApplication\InappropriateContentReporting_Event;
use JetApplication\InappropriateContentReporting_Event_New;

#[DataModel_Definition(
	name: 'inappropriate_content_reporting',
	database_table_name: 'inappropriate_content_reporting',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Inappropriate Content Reporting',
	admin_manager_interface: Application_Service_Admin_InappropriateContentReporting::class
)]
abstract class Core_InappropriateContentReporting extends EShopEntity_WithEShopRelation implements
	EShopEntity_Admin_Interface,
	EShopEntity_HasGet_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface,
	EShopEntity_HasImageGallery_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasEvents_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_HasImageGallery_Trait;
	
	protected static array $flags = [
		'assessed',
	];
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Author - name:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter your name',
		]
	)]
	public string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Author - e-mail:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter your e-mail',
			Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter your e-mail',
		]
		
	)]
	public string $email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'URL:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter URL',
		]
		
	)]
	public string $URL = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Problem description:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter problem description',
		]
	)]
	public string $description = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Agree',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'You have to agree with the terms and conditions',
		]
	)]
	public bool $agree = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Summary:'
	)]
	protected string $summary = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $assessed = false;
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getEmail(): string
	{
		return $this->email;
	}
	
	public function setEmail( string $email ): void
	{
		$this->email = $email;
	}
	
	public function getURL(): string
	{
		return $this->URL;
	}
	
	public function setURL( string $URL ): void
	{
		$this->URL = $URL;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ): void
	{
		$this->description = $description;
	}
	
	public function getAgree(): bool
	{
		return $this->agree;
	}
	
	public function setAgree( bool $agree ): void
	{
		$this->agree = $agree;
	}
	
	public function getSummary(): string
	{
		return $this->summary;
	}
	
	public function setSummary( string $summary ): void
	{
		$this->summary = $summary;
	}
	
	public function isAssessed(): bool
	{
		return $this->assessed;
	}
	
	public function setAssessed( bool $assessed ): void
	{
		$this->assessed = $assessed;
	}
	
	
	
	
	public static function getStatusList(): array
	{
		return InappropriateContentReporting::getStatusList();
	}
	
	public function createEvent( EShopEntity_Event $event ): EShopEntity_Event
	{
		/**
		 * @var InappropriateContentReporting_Event $event
		 */
		$event->init( $this->getEshop() );
		$event->setInappropriateContentReporting( $this );
		
		return $event;
	}
	
	public function getHistory(): array
	{
		return InappropriateContentReporting_Event::getEventsList( $this->getId() );
	}
	
	public function getAdminTitle(): string
	{
		return $this->getURL().' '.Locale::dateAndTime($this->created);
	}
	
	public function getEntityTypeForImageGallery(): string
	{
		return $this::getEntityType();
	}
	
	public function getEntityIdForImageGallery(): string|int
	{
		return $this->getId();
	}
	
	public function setupAddForm( Form $form ) : void
	{
		$files = new Form_Field_FileImage('images', 'Files');
		$files->setAllowMultipleUpload( true );
		$files->setErrorMessages([
			Form_Field_File::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Unsupported file type',
			Form_Field_File::ERROR_CODE_FILE_IS_TOO_LARGE => 'File is too large'
		
		]);
		
		$form->addField( $files );
	}
	
	public function processNew() : bool
	{
		$form = $this->getAddForm();
		if(!$form->catch()) {
			return false;
		}
		
		$this->save();
		
		$images = [];
		
		foreach($form->field( 'images' )->getValue() as $file) {
			/**
			 * @var Form_Field_File_UploadedFile $file
			 */
			$images[$file->getTmpFilePath()] = $file->getFileName();
		}
		
		if( $images ) {
			$this->getImageGallery()->uploadImages( $images );
		}
		
		$this->createEvent( InappropriateContentReporting_Event_New::new() );
		
		return true;
	}
	
}