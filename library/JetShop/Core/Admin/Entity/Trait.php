<?php
namespace JetShop;

use Jet\Attributes;
use Jet\Form;
use JetApplication\EShops;
use JetApplication\JetShopEntity_Definition;
use ReflectionClass;

trait Core_Admin_Entity_Trait {
	
	
	protected ?Form $_add_form = null;
	
	protected ?Form $_edit_form = null;
	
	protected bool $editable;
	
	/**
	 * @var static[][]
	 */
	protected static array $loaded_items = [];
	
	public static function get( int|string $id ) : static|null
	{
		if(!$id) {
			return null;
		}
		
		$id = (int)$id;
		
		if(isset( static::$loaded_items[static::class][$id])) {
			return static::$loaded_items[static::class][$id];
		}
		
		static::$loaded_items[static::class][$id] = static::load( $id );
		
		return static::$loaded_items[static::class][$id];
	}
	
	
	public function isEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = $editable;
	}
	
	public function getEditURL( array $get_params=[] ) : string
	{
		return $this->getAdminManager()->getEditURL( $this, $get_params );
	}
	
	
	
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->setEshop( EShops::getCurrent() );
			
			$this->_add_form = $this->createForm('add_form');
			
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
	
	public function getAdminManagerInterface() : ?string
	{
		$attributes = Attributes::getClassDefinition( new ReflectionClass($this), JetShopEntity_Definition::class );
		
		return $attributes['admin_manager_interface']??null;
	}
	
	
}