<?php
namespace JetApplication;

use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Form_Field_Input;

trait Admin_Entity_Common_Trait {
	use Admin_Entity_FulltextSearchIndexDataProvider_Trait;
	
	/**
	 * @var static[][]
	 */
	protected static array $loaded_items = [];
	
	protected ?Form $_add_form = null;
	
	protected ?Form $_edit_form = null;
	
	protected bool $editable;
	
	public function __construct()
	{
		/** @noinspection PhpMultipleClassDeclarationsInspection */
		parent::__construct();
		
		$this->afterLoad();
	}
	
	public function afterLoad() : void
	{
	}
	
	public function isEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = $editable;
	}
	
	
	
	
	
	public static function get( int $id ) : static|null
	{
		if(!$id) {
			return null;
		}
		
		if(isset( static::$loaded_items[static::class][$id])) {
			return static::$loaded_items[static::class][$id];
		}
		
		static::$loaded_items[static::class][$id] = static::load( $id );
		
		return static::$loaded_items[static::class][$id];
	}
	
	
	public function handleImages() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $this->isEditable() );
		$manager->handleSelectImageWidgets();
	}
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			$internal_code = $this->_add_form->getField('internal_code');
			
			$internal_code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if($value==='') {
					return true;
				}
				
				if(static::internalCodeUsed($value)) {
					$field->setError('code_used');
					
					return false;
				}
				
				return true;
			});
			
			$this->setupAddForm( $this->_add_form );
			
		}
		
		return $this->_add_form;
	}
	
	protected function setupAddForm( Form $form ) : void
	{
	
	}
	
	
	public function getEditForm() : Form
	{

		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			
			$internal_code = $this->_edit_form->getField('internal_code');
			
			$internal_code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if($value==='') {
					return true;
				}
				
				if(static::internalCodeUsed($value, $this->getId())) {
					$field->setError('code_used');
					
					return false;
				}
				
				return true;
			});
			
			if(!$this->isEditable()) {
				$this->_edit_form->setIsReadonly();
			}
			
			
			$this->setupEditForm( $this->_edit_form );
		}
		
		return $this->_edit_form;
	}
	
	protected function setupEditForm( Form $form ) : void
	{
	
	}
	
	
	
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}
	
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}
	
	/**
	 *
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getList() : DataModel_Fetch_Instances|iterable
	{
		$where = [];
		
		return static::fetchInstances( [] );
	}
	
	
	
	public function getAdminTitle() : string
	{
		$code = $this->internal_code?:$this->id;
		
		return $this->internal_name.' ('.$code.')';
	}
	
	public function isItPossibleToDelete() : bool
	{
		return true;
	}
}