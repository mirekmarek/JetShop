<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\Admin_Listing;
use JetApplication\Admin_Listing_Column;
use JetApplication\Admin_Listing_Schema;


abstract class Core_Admin_Listing_Schema_Manager
{
	
	
	protected Admin_Listing $listing;
	protected string $entity_type;
	protected array $default_col_schema = [];
	
	/**
	 * @var Admin_Listing_Schema[]
	 */
	protected array|null $list = null;
	
	protected ?Form $add_schema_form = null;
	
	public function __construct( Admin_Listing $listing )
	{
		$this->listing = $listing;
		$this->entity_type = $this->listing->getEntityManager()::getEntityInstance()::getEntityType();
	}
	
	
	public function getListing(): Admin_Listing
	{
		return $this->listing;
	}
	
	/**
	 * @return static[]
	 */
	public function getList() : iterable
	{
		if( $this->list===null ) {
			
			$list = Admin_Listing_Schema::fetchInstances( ['entity'=>$this->entity_type] );
			$list->getQuery()->setOrderBy( ['name'] );
			
			
			if(!count($list)) {
				
				$default = new Admin_Listing_Schema();
				$default->setEntity( $this->entity_type );
				$default->setName('Default');
				$default->setCols( $this->default_col_schema );
				$default->setIsDefault(true);
				$default->save();
				
				$this->list = [$default->getId()=>$default];
			} else {
				$this->list = [];
				
				foreach($list as $schema) {
					$this->list[$schema->getId()] = $schema;
				}
			}
			
		}
		
		
		return $this->list;
	}
	
	public function getDefault() : ?Admin_Listing_Schema
	{
		$list = $this->getList();
		foreach($list as $schema) {
			if($schema->getIsDefault()) {
				return $schema;
			}
		}
		
		return null;
	}
	
	
	public function getCurrentColSchema() : array
	{
		$schema = [];
		$_schema = Http_Request::GET()->getString(Admin_Listing_Schema::SCHEMA_GET_PARAM);
		if($_schema) {
			$all_cols = $this->listing->getColumns();
			
			$_schema = explode( Admin_Listing_Schema::COLS_SEPARATOR, $_schema );
			
			foreach($_schema as $col) {
				if(isset($all_cols[$col])) {
					$schema[] = $col;
				}
			}
		}
		
		if(!$schema) {
			return static::getDefault()->getCols();
		}
		
		return $schema;
	}
	
	public function getSelectedSchemaDefinition() : ?Admin_Listing_Schema
	{
		$id = Http_Request::GET()->getString(  Admin_Listing_Schema::SCHEMA_ID_GET_PARAM);
		$list = $this->getList();
		
		return $list[$id]??$this->getDefault();
	}
	
	public function getUrlWithCol( Admin_Listing_Column $col ) : string
	{
		$curr_schema = $this->getCurrentColSchema();
		$key = $col->getKey();
		
		if(!in_array($key, $curr_schema)) {
			$curr_schema[] = $key;
		}
		
		return Http_Request::currentURI([
			Admin_Listing_Schema::SCHEMA_GET_PARAM => implode(Admin_Listing_Schema::COLS_SEPARATOR, $curr_schema)
		]);
		
	}
	
	public function getUrlWithoutCol( Admin_Listing_Column $col ) : string
	{
		$curr_schema = $this->getCurrentColSchema();
		$key = $col->getKey();
		
		$schema = [];
		foreach($curr_schema as $cur_key) {
			if($cur_key!=$key) {
				$schema[] = $cur_key;
			}
		}
		
		
		return Http_Request::currentURI([
			Admin_Listing_Schema::SCHEMA_GET_PARAM => implode(Admin_Listing_Schema::COLS_SEPARATOR, $schema)
		]);
	}
	
	public function getUpdateSchemaForm() : Form
	{
		return $this->getSelectedSchemaDefinition()->getUpdateSchemaForm();
	}
	
	public function catchUpdateSchemaForm() : bool
	{
		if(!$this->getUpdateSchemaForm()->catch()) {
			return false;
		}
		
		$this->getSelectedSchemaDefinition()->save();
		
		return true;
	}
	
	
	public function getAddSchemaForm() : Form
	{
		if($this->add_schema_form===null) {
			$name = new Form_Field_Input('name', 'Name:');
			$name->setIsRequired(true);
			$name->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter name',
			]);
			$cols = new Form_Field_Hidden('cols', '');
			
			$this->add_schema_form = new Form('add_col_schema_form', [
				$name,
				$cols
			]);
			
			
			$this->add_schema_form->setAction( Http_Request::currentURI() );
		}
		
		return $this->add_schema_form;
	}
	
	public function catchAddSchemaForm() : Admin_Listing_Schema|bool
	{
		$form = $this->getAddSchemaForm();
		if(!$form->catch()) {
			return false;
		}
		
		$new_schema = new Admin_Listing_Schema();
		$new_schema->setEntity( $this->entity_type );
		$new_schema->setName( $form->field('name')->getValue() );
		$new_schema->setCols( $form->field('cols')->getValue() );
		$new_schema->save();
		
		return $new_schema;
	}
	
	public function handle() : void
	{
		if( $this->catchAddSchemaForm() ) {
			Http_Headers::reload();
		}
		
		if( $this->getSelectedSchemaDefinition()->catchUpdateSchemaForm()) {
			Http_Headers::reload();
		}
		
		$GET = Http_Request::GET();
		if($GET->exists(Admin_Listing_Schema::SCHEMA_GET_PARAM)) {
			$this->listing->setParam(Admin_Listing_Schema::SCHEMA_GET_PARAM, $GET->getString(Admin_Listing_Schema::SCHEMA_GET_PARAM) );
		}
		if($GET->exists(Admin_Listing_Schema::SCHEMA_ID_GET_PARAM)) {
			$this->listing->setParam(Admin_Listing_Schema::SCHEMA_ID_GET_PARAM, $GET->getInt(Admin_Listing_Schema::SCHEMA_ID_GET_PARAM) );
		}
		
	}
	
	public function getDefaultColSchema(): array
	{
		return $this->default_col_schema;
	}
	
	
	public function setDefaultColSchema( array $default_col_schema ): void
	{
		$this->default_col_schema = $default_col_schema;
	}
	
	
}
