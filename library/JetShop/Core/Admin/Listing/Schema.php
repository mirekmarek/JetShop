<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Definition;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Http_Request;


#[DataModel_Definition(
	name: 'listing_schema',
	database_table_name: 'listing_schema',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Core_Admin_Listing_Schema extends DataModel
{
	public const COLS_SEPARATOR = '|';
	public const SCHEMA_GET_PARAM = 'schema';
	public const SCHEMA_ID_GET_PARAM = 'schema_id';
	
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
	
	
	protected ?Form $update_schema_form = null;
	
	public static function get( int $id ) : static|null
	{
		return static::load( $id );
	}
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function getEntity(): string
	{
		return $this->entity;
	}
	
	public function setEntity( string $entity ): void
	{
		$this->entity = $entity;
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
	
}
