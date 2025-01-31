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
use Jet\Form_Field;
use Jet\Form_Field_Select;
use JetApplication\Carrier;
use JetApplication\Carrier_Packaging;
use JetApplication\Carrier_Service;
use JetApplication\Delivery_Kind;

#[DataModel_Definition(
	name: 'carrier_services',
	database_table_name: 'carrier_services',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
)]
abstract class Core_Carrier_Service extends DataModel
{
	protected Carrier $carrier;
	
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
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Kind of delivery: ',
		is_required: true,
		select_options_creator: [
			Delivery_Kind::class,
			'getScope'
		],
		error_messages: [
		]
	)]
	protected string $compatible_kind_of_delivery = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Carrier service identification code: ',
		error_messages: [
		]
	)]
	protected string $carrier_service_identification_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Packaging has dimensions',
	)]
	protected bool $packaging_has_dimensions = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Packaging has weight',
	)]
	protected bool $packaging_has_weight = true;
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;

	
	public static function get( Carrier $carrier, string $code ) : ?static
	{
		$where = [
			'carrier_code' => $carrier->getCode(),
			'AND',
			'code' => $code
		];
		
		return static::load( $where );
	}
	
	public static function getForCarrier( Carrier $carrier ) : array
	{
		$where = [
			'carrier_code' => $carrier->getCode()
		];
		
		$list = static::fetch(
			where_per_model: ['' => $where],
			order_by: 'name',
			item_key_generator: function( Carrier_Service $p ) : string {
				return $p->getCode();
			}
		);
		
		foreach($list as $item) {
			$item->setCarrier( $carrier );
		}
		
		return $list;
	}
	

	public function setCarrier( Carrier $carrier ): void
	{
		$this->carrier = $carrier;
		$this->carrier_code = $carrier->getCode();
	}
	
	public function getCarrier(): Carrier
	{
		return $this->carrier;
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
	
	public function getCompatibleKindOfDelivery(): string
	{
		return $this->compatible_kind_of_delivery;
	}
	
	public function setCompatibleKindOfDelivery( string $compatible_kind_of_delivery ): void
	{
		$this->compatible_kind_of_delivery = $compatible_kind_of_delivery;
	}


	
	/**
	 * @return Carrier_Packaging[]
	 */
	public function getAvailablePackaging(): array
	{
		/**
		 * @var Carrier_Service $this
		 */
		return Carrier_Packaging::getForCarrierService( $this );
	}
	
	public function getsPackagingHasDimensions(): bool
	{
		return $this->packaging_has_dimensions;
	}
	
	public function setPackagingHasDimensions( bool $packaging_has_dimensions ): void
	{
		$this->packaging_has_dimensions = $packaging_has_dimensions;
	}
	
	public function getsPackagingHasWeight(): bool
	{
		return $this->packaging_has_weight;
	}
	
	public function setPackagingHasWeight( bool $packaging_has_weight ): void
	{
		$this->packaging_has_weight = $packaging_has_weight;
	}
	

	public function getCarrierCode(): string
	{
		return $this->carrier_code;
	}

	public function getCarrierServiceIdentificationCode(): string
	{
		return $this->carrier_service_identification_code;
	}
	
	public function setCarrierServiceIdentificationCode( string $carrier_service_identification_code ): void
	{
		$this->carrier_service_identification_code = $carrier_service_identification_code;
	}
	
	
	
	protected function updateForm( Form $form ) : void
	{
		/**
		 * @var Form_Field_Select $carrier_service_identification_code
		 */
		$carrier_service_identification_code = $form->getField('carrier_service_identification_code');
		
		$options = [''=>''];
		foreach($this->carrier->getCarrierServiceOptions() as $code=>$name) {
			$options[$code] = $name;
		}

		$carrier_service_identification_code->setSelectOptions( $options );
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
				if(Carrier_Service::get( $this->carrier, $code->getValue() )) {
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