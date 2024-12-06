<?php
namespace JetShop;

use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\Availabilities;
use JetApplication\WarehouseManagement_Warehouse;

abstract class Core_Availability implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal code: ',
	)]
	protected string $code = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal name: ',
	)]
	protected string $name = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Warehouses: ',
		select_options_creator: [
			WarehouseManagement_Warehouse::class,
			'getScope'
		],
	
	)]
	protected array $warehouse_ids = [];
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	public function __construct( ?array $data=null )
	{
		if($data) {
			$this->setCode( $data['code']??'' );
			$this->setName( $data['name']??'' );
			$this->setWarehouseIds( $data['warehouse_ids']??[] );
		}
	}
	
	public function toArray() : array
	{
		return [
			'code' => $this->code,
			'name' => $this->name,
			'warehouse_ids' => $this->warehouse_ids,
		];
	}
	
	public function getCode(): string
	{
		return $this->code;
	}
	
	public function setCode( string $code ): void
	{
		$this->code = $code;
	}
	
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getWarehouseIds(): array
	{
		return $this->warehouse_ids;
	}
	
	public function setWarehouseIds( array $warehouse_ids ): void
	{
		$this->warehouse_ids = $warehouse_ids;
	}
	
	
	
	protected function updateForm( Form $form ) : void
	{
	}
	
	public function getEditForm() : Form
	{
		if( $this->edit_form===null ) {
			$form = $this->createForm( 'cfg_form' );
			
			$form->field('code')->setIsReadonly( true );
			
			$this->updateForm( $form );
			
			$this->edit_form = $form;
		}
		
		return $this->edit_form;
	}
	
	
	public function getAddForm() : Form
	{
		if( $this->add_form===null ) {
			$form = $this->createForm( 'cfg_form' );
			
			$code = $form->field('code');
			$code->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
				'not_unique' => 'This code is already used'
			]);
			$code->setValidator( function() use ($code) : bool {
				if(Availabilities::exists( $code->getValue() )) {
					$code->setError('not_unique');
					return false;
				}
				
				return true;
			} );
			
			
			$this->updateForm( $form );
			
			$this->add_form = $form;
		}
		
		return $this->add_form;
	}
	
	
}