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
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\Carrier_Packaging;
use JetApplication\Carrier_Service;
use JsonSerializable;

#[DataModel_Definition(
	name: 'carrier_packaging',
	database_table_name: 'carrier_packaging',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
)]
abstract class Core_Carrier_Packaging extends DataModel implements JsonSerializable, Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	protected Carrier_Service $carrier_service;
	
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
	protected string $carrier_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $carrier_service_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal classification: ',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $internal_classification = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Carrier packing identification code: ',
		error_messages: [
		]
	)]
	protected string $carrier_packing_identification_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal code: ',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name: ',
		is_required: true,
		error_messages: [
		]
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Default width: ',
	)]
	protected float $default_w = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Default length: ',
	)]
	protected float $default_l = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Default height: ',
	)]
	protected float $default_h = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Minimal width: ',
	)]
	protected float $min_w = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Minimal length: ',
	)]
	protected float $min_l = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Minimal height: ',
	)]
	protected float $min_h = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Maximal width: ',
	)]
	protected float $max_w = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Maximal length: ',
	)]
	protected float $max_l = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Maximal height: ',
	)]
	protected float $max_h = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Maximal weight: ',
	)]
	protected float $min_weight = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Default weight: ',
	)]
	protected float $default_weight = 0.0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Maximal weight: ',
	)]
	protected float $max_weight = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Length is editable',
	)]
	protected bool $l_editable = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Width is editable',
	)]
	protected bool $w_editable = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Height is editable',
	)]
	protected bool $h_editable = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Weight is editable',
	)]
	protected bool $weight_editable = true;
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	
	public static function get( Carrier_Service $carrier_service, string $code ) : ?static
	{
		$where = [
			'carrier_code' => $carrier_service->getCarrier()->getCode(),
			'AND',
			'carrier_service_code' => $carrier_service->getCode(),
			'AND',
			'code' => $code
		];
		
		return static::load( $where );
	}
	
	public static function getForCarrierService( Carrier_Service $carrier_service ) : array
	{
		$where = [
			'carrier_code' => $carrier_service->getCarrier()->getCode(),
			'AND',
			'carrier_service_code' => $carrier_service->getCode(),
		];
		
		$list = static::fetch(
			where_per_model: ['' => $where],
			order_by: 'name',
			item_key_generator: function( Carrier_Packaging $p ) : string {
				return $p->getCode();
			}
		);
		
		foreach($list as $item) {
			$item->setCarrierService( $carrier_service );
		}
		
		return $list;
	}
	
	public function setCarrierService( Carrier_Service $carrier_service ): void
	{
		$this->carrier_service = $carrier_service;
		$this->carrier_code = $carrier_service->getCarrier()->getCode();
		$this->carrier_service_code = $carrier_service->getCode();
	}
	
	public function getCarrierService(): Carrier_Service
	{
		return $this->carrier_service;
	}
	
	public function getCarrierCode(): string
	{
		return $this->carrier_code;
	}
	
	public function setCarrierCode( string $carrier_code ): void
	{
		$this->carrier_code = $carrier_code;
	}
	
	public function getCarrierServiceCode(): string
	{
		return $this->carrier_service_code;
	}
	
	public function setCarrierServiceCode( string $carrier_service_code ): void
	{
		$this->carrier_service_code = $carrier_service_code;
	}
	
	public function getCarrierPackingIdentificationCode(): string
	{
		return $this->carrier_packing_identification_code;
	}
	
	public function setCarrierPackingIdentificationCode( string $carrier_packing_identification_code ): void
	{
		$this->carrier_packing_identification_code = $carrier_packing_identification_code;
	}

	public function getId(): int
	{
		return $this->id;
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
	
	public function getInternalClassification(): string
	{
		return $this->internal_classification;
	}
	
	public function setInternalClassification( string $internal_classification ): void
	{
		$this->internal_classification = $internal_classification;
	}
	
	public function getDefaultW(): float
	{
		return $this->default_w;
	}
	
	public function setDefaultW( float $default_w ): void
	{
		$this->default_w = $default_w;
	}
	
	public function getDefaultL(): float
	{
		return $this->default_l;
	}
	
	public function setDefaultL( float $default_l ): void
	{
		$this->default_l = $default_l;
	}
	
	public function getDefaultH(): float
	{
		return $this->default_h;
	}

	public function setDefaultH( float $default_h ): void
	{
		$this->default_h = $default_h;
	}
	

	public function getMaxW(): float
	{
		return $this->max_w;
	}

	public function setMaxW( float $max_w ): void
	{
		$this->max_w = $max_w;
	}

	public function getMaxL(): float
	{
		return $this->max_l;
	}

	public function setMaxL( float $max_l ): void
	{
		$this->max_l = $max_l;
	}

	public function getMaxH(): float
	{
		return $this->max_h;
	}

	public function setMaxH( float $max_h ): void
	{
		$this->max_h = $max_h;
	}

	public function getMinW(): float
	{
		return $this->min_w;
	}

	public function setMinW( float $min_w ): void
	{
		$this->min_w = $min_w;
	}
	
	public function getMinL(): float
	{
		return $this->min_l;
	}

	public function setMinL( float $min_l ): void
	{
		$this->min_l = $min_l;
	}
	
	public function getMinH(): float
	{
		return $this->min_h;
	}
	
	public function setMinH( float $min_h ): void
	{
		$this->min_h = $min_h;
	}
	
	
	
	public function getDefaultWeight(): float
	{
		return $this->default_weight;
	}
	
	public function setDefaultWeight( float $default_weight ): void
	{
		$this->default_weight = $default_weight;
	}
	

	public function getMaxWeight(): float
	{
		return $this->max_weight;
	}
	
	public function setMaxWeight( float $max_weight ): void
	{
		$this->max_weight = $max_weight;
	}
	
	public function getMinWeight(): float
	{
		return $this->min_weight;
	}
	
	public function setMinWeight( float $min_weight ): void
	{
		$this->min_weight = $min_weight;
	}
	
	public function getLEditable(): bool
	{
		return $this->l_editable;
	}
	
	public function setLEditable( bool $l_editable ): void
	{
		$this->l_editable = $l_editable;
	}
	
	public function getWEditable(): bool
	{
		return $this->w_editable;
	}
	
	public function setWEditable( bool $w_editable ): void
	{
		$this->w_editable = $w_editable;
	}
	
	public function getHEditable(): bool
	{
		return $this->h_editable;
	}
	
	public function setHEditable( bool $h_editable ): void
	{
		$this->h_editable = $h_editable;
	}
	
	public function getWeightEditable(): bool
	{
		return $this->weight_editable;
	}
	
	public function setWeightEditable( bool $weight_editable ): void
	{
		$this->weight_editable = $weight_editable;
	}
	
	
	
	
	
	public function jsonSerialize(): array
	{
		return [
			'carrier_code' => $this->carrier_code,
			'carrier_service_code' => $this->carrier_service_code,
			
			'classification' => $this->internal_classification,
			
			'carrier_packing_identification_code' => $this->carrier_packing_identification_code,
			
			'code'  => $this->code,
			'title' => $this->name,
			
			'default_w' => $this->default_w,
			'default_l' => $this->default_l,
			'default_h' => $this->default_h,
			
			'min_w' => $this->min_w,
			'min_l' => $this->min_l,
			'min_h' => $this->min_h,
			
			'max_w' => $this->max_w,
			'max_l' => $this->max_l,
			'max_h' => $this->max_h,
			
			'min_weight'     => $this->min_weight,
			'default_weight' => $this->default_weight,
			'max_weight'     => $this->max_weight,
			
			'l_editable' => $this->l_editable,
			'w_editable' => $this->w_editable,
			'h_editable' => $this->h_editable,
			
			'weight_editable' => $this->weight_editable,
		];
	}
	
	protected function updateForm( Form $form ) : void
	{
	}
	
	public function getEditForm() : Form
	{
		if( $this->edit_form===null ) {
			$form = $this->createForm( 'edit_form' );
			
			$form->field('code')->setIsReadonly( true );
			
			$this->updateForm( $form );
			
			$this->edit_form = $form;
		}
		
		return $this->edit_form;
	}
	
	
	public function getAddForm() : Form
	{
		if( $this->add_form===null ) {
			$form = $this->createForm( 'add_form' );
			
			$code = $form->field('code');
			$code->setErrorMessages([
				Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
				'not_unique' => 'This code is already used'
			]);
			$code->setValidator( function() use ($code) : bool {
				if(Carrier_Packaging::get( $this->carrier_service, $code->getValue() )) {
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