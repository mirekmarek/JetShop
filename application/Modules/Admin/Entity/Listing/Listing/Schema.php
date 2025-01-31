<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Entity\Listing;


use Jet\DataListing_Column;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Definition;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Http_Headers;
use Jet\Http_Request;


#[DataModel_Definition(
	name: 'listing_schema',
	database_table_name: 'listing_schema',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Listing_Schema extends DataModel
{
	protected static Listing $listing;
	protected static string $entity_type;
	
	protected static array $default_col_schema = [];
	
	public const  COLS_SEPARATOR = '|';
	
	public const  SCHEMA_GET_PARAM = 'schema';
	public const  SCHEMA_ID_GET_PARAM = 'schema_id';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $entity = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Schema name:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter schema name'
		]
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_HIDDEN
	)]
	protected string $cols = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is default',
		is_required: false,
		error_messages: [
		]
	)]
	protected bool $is_default = false;
	
	/**
	 * @var static[]
	 */
	protected static array|null $list = null;
	
	protected static ?Form $add_schema_form = null;
	
	protected ?Form $update_schema_form = null;
	
	public static function getListing(): Listing
	{
		return self::$listing;
	}
	
	public static function setListing( Listing $listing ): void
	{
		static::$listing = $listing;
		static::$entity_type = static::$listing->getEntityManager()::getEntityInstance()::getEntityType();
	}
	
	public static function get( int $id ) : static|null
	{
		return static::load( $id );
	}

	/**
	 * @return static[]
	 */
	public static function getList() : iterable
	{
		if(static::$list===null) {
			
			$list = static::fetchInstances( ['entity'=>static::$entity_type] );
			$list->getQuery()->setOrderBy( ['name'] );
			
			
			if(!count($list)) {
				
				$default = new static();
				$default->entity = static::$entity_type;
				$default->setName('Default');
				$default->setCols( static::$default_col_schema );
				$default->setIsDefault(true);
				$default->save();
				
				static::$list = [$default->getId()=>$default];
			} else {
				static::$list = [];
				foreach($list as $schema) {
					static::$list[$schema->getId()] = $schema;
				}
			}
			
		}
		
		
		return static::$list;
	}
	
	/** @noinspection PhpInconsistentReturnPointsInspection */
	public static function getDefault() : static
	{
		$list = static::getList();
		foreach($list as $schema) {
			if($schema->getIsDefault()) {
				return $schema;
			}
		}
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function setName( string $value ) : void
	{
		$this->name = $value;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function setCols( string|array $value ) : void
	{
		if(is_array($value)) {
			$value = implode(static::COLS_SEPARATOR, $value);
		}
		$this->cols = $value;
	}
	
	public function getCols() : array
	{
		return explode(static::COLS_SEPARATOR, $this->cols);
	}

	public function setIsDefault( bool $value ) : void
	{
		$this->is_default = $value;
	}

	public function getIsDefault() : bool
	{
		return $this->is_default;
	}
	
	public function getURL() : string
	{
		return Http_Request::currentURI([
			static::SCHEMA_ID_GET_PARAM => $this->id,
			static::SCHEMA_GET_PARAM => $this->cols
		]);
	}
	
	public static function getCurrentColSchema() : array
	{
		$schema = [];
		$_schema = Http_Request::GET()->getString(static::SCHEMA_GET_PARAM);
		if($_schema) {
			$all_cols = static::$listing->getColumns();
			
			$_schema = explode(static::COLS_SEPARATOR, $_schema);
			
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
	
	public static function getSelectedSchemaDefinition() : ?static
	{
		$id = Http_Request::GET()->getString(static::SCHEMA_ID_GET_PARAM);
		$list = static::getList();
		if(!isset($list[$id])) {
			return static::getDefault();
		}
		
		return $list[$id];
	}
	
	public static function getUrlWithCol( DataListing_Column $col ) : string
	{
		$curr_schema = static::getCurrentColSchema();
		$key = $col->getKey();

		if(!in_array($key, $curr_schema)) {
			$curr_schema[] = $key;
		}
		
		return Http_Request::currentURI([
			static::SCHEMA_GET_PARAM => implode(static::COLS_SEPARATOR, $curr_schema)
		]);
		
	}
	
	public static function getUrlWithoutCol( DataListing_Column $col ) : string
	{
		$curr_schema = static::getCurrentColSchema();
		$key = $col->getKey();
		
		$schema = [];
		foreach($curr_schema as $cur_key) {
			if($cur_key!=$key) {
				$schema[] = $cur_key;
			}
		}
		
		
		return Http_Request::currentURI([
			static::SCHEMA_GET_PARAM => implode(static::COLS_SEPARATOR, $schema)
		]);
	}
	
	public function getUpdateSchemaForm() : Form
	{
		if($this->update_schema_form===null) {
			$name = new Form_Field_Input('name', 'Name:');
			$name->setDefaultValue($this->getName());
			$name->setFieldValueCatcher( function( $value ) {
				$this->setName( $value );
			} );
			$name->setIsRequired(true);
			$name->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter name',
			]);
			
			$cols = new Form_Field_Hidden('cols', '');
			$cols->setFieldValueCatcher(function( $value ) {
				$this->setCols( $value );
			});
			
			
			$this->update_schema_form = new Form('update_col_schema_form', [
				$name,
				$cols
			]);
			
			
			$this->update_schema_form->setAction( Http_Request::currentURI() );
		}
		
		return $this->update_schema_form;
	}
	
	public function catchUpdateSchemaForm() : bool
	{
		if(!$this->getUpdateSchemaForm()->catch()) {
			return false;
		}
		
		$this->save();
		return true;
	}
	
	
	public static function getAddSchemaForm() : Form
	{
		if(static::$add_schema_form===null) {
			$name = new Form_Field_Input('name', 'Name:');
			$name->setIsRequired(true);
			$name->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter name',
			]);
			$cols = new Form_Field_Hidden('cols', '');
			
			static::$add_schema_form = new Form('add_col_schema_form', [
				$name,
				$cols
			]);
			
			
			static::$add_schema_form->setAction( Http_Request::currentURI() );
		}
		
		return static::$add_schema_form;
	}
	
	public static function catchAddSchemaForm() : static|bool
	{
		$form = static::getAddSchemaForm();
		if(!$form->catch()) {
			return false;
		}
		
		$new_schema = new static();
		$new_schema->entity = static::$entity_type;
		$new_schema->setName( $form->field('name')->getValue() );
		$new_schema->setCols( $form->field('cols')->getValue() );
		$new_schema->save();
		
		return $new_schema;
	}
	
	public static function handle() : void
	{
		if(static::catchAddSchemaForm()) {
			Http_Headers::reload();
		}
		
		if(static::getSelectedSchemaDefinition()->catchUpdateSchemaForm()) {
			Http_Headers::reload();
		}
		
		$GET = Http_Request::GET();
		if($GET->exists(static::SCHEMA_GET_PARAM)) {
			static::$listing->setParam(static::SCHEMA_GET_PARAM, $GET->getString(static::SCHEMA_GET_PARAM) );
		}
		if($GET->exists(static::SCHEMA_ID_GET_PARAM)) {
			static::$listing->setParam(static::SCHEMA_ID_GET_PARAM, $GET->getInt(static::SCHEMA_ID_GET_PARAM) );
		}
		
	}
	
	public static function getDefaultColSchema(): array
	{
		return static::$default_col_schema;
	}
	

	public static function setDefaultColSchema( array $default_col_schema ): void
	{
		static::$default_col_schema = $default_col_schema;
	}
	
	
}
