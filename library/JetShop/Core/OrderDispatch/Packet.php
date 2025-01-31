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
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use JetApplication\OrderDispatch;

#[DataModel_Definition(
	name: 'order_dispatch_packet',
	database_table_name: 'order_dispatches_packets',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: OrderDispatch::class
)]
abstract class Core_OrderDispatch_Packet extends DataModel_Related_1toN
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_key: true,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		related_to: 'main.id',
		is_key: true
	)]
	protected int $dispatch_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50
	)]
	protected string $packet_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $size_w = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $size_h = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $size_l = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $volume = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $weight = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $note = '';
	
	protected ?Form $form = null;
	protected ?OrderDispatch $dispatch = null;
	
	
	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}
	
	
	public function getArrayKeyValue() : string
	{
		return $this->id;
	}
	
	public function setDispatch( OrderDispatch $dispatch ): void
	{
		$this->dispatch = $dispatch;
		$this->dispatch_id = $dispatch->getId();
	}
	
	

	public function getDispatchId(): int
	{
		return $this->dispatch_id;
	}
	
	public function setDispatchId( int $dispatch_id ): void
	{
		$this->dispatch_id = $dispatch_id;
	}
	
	
	public function getPacketType(): string
	{
		return $this->packet_type;
	}

	public function setPacketType( string $packet_type ): void
	{
		$this->packet_type = $packet_type;
	}

	public function getSizeW(): float
	{
		return $this->size_w;
	}

	public function setSizeW( float $size_w ): void
	{
		$this->size_w = $size_w;
		$this->calcVolume();
	}

	public function getSizeH(): float
	{
		return $this->size_h;
	}

	public function setSizeH( float $size_h ): void
	{
		$this->size_h = $size_h;
		$this->calcVolume();
	}
	
	public function getSizeL(): float
	{
		return $this->size_l;
	}

	public function setSizeL( float $size_l ): void
	{
		$this->size_l = $size_l;
		$this->calcVolume();
	}
	
	protected function calcVolume() : void
	{
		$this->volume = $this->size_l*$this->size_h*$this->size_w;
	}
	
	public function getVolume(): float
	{
		return $this->volume;
	}
	
	public function getWeight(): float
	{
		return $this->weight;
	}
	
	public function setWeight( float $weight ): void
	{
		$this->weight = $weight;
	}
	
	public function getNote(): string
	{
		return $this->note;
	}
	
	public function setNote( string $note ): void
	{
		$this->note = $note;
	}
	
	public function getForm() : Form
	{
		if(!$this->form) {
			$carrier_service = $this->dispatch->getCarrierService();
			
			
			$packet_types = $carrier_service?->getAvailablePackaging()??[];
			
			$options = [];
			foreach($packet_types as $type) {
				$options[$type->getCode()] = $type->getName();
			}
			
			if(!$this->id) {
				$default_type = null;
				foreach($packet_types as $type) {
					if(!$default_type) {
						$default_type = $type;
					}
				}
				
				if($default_type) {
					$this->packet_type = $default_type->getCode();
					$this->weight = $default_type->getDefaultWeight();
					$this->size_h = $default_type->getDefaultH();
					$this->size_l = $default_type->getDefaultL();
					$this->size_w = $default_type->getDefaultW();
				}
			}
			
			$note = new Form_Field_Input('note', '');
			$note->setDefaultValue( $this->note );
			$note->setFieldValueCatcher( function( string $value ) {
				$this->setNote( $value );
			} );
			
			
			if(count($options)>1) {
				$type = new Form_Field_Select('type', '');
				$type->setSelectOptions( $options );
				$type->setFieldValueCatcher( function( string $value ) : void {
					$this->setPacketType( $value );
				});
			} else {
				$type = new Form_Field_Hidden('type');
				$type->setIsReadonly( true );
			}
			
			$type->setDefaultValue( $this->packet_type );
			
			$form = new Form(
				$this->id ? 'packet_form_'.$this->id : 'add_packet_form',
				[$type, $note]
			);
			
			if( $carrier_service?->getsPackagingHasWeight() ) {
				$weight = new Form_Field_Float('weight', '');
				$weight->setDefaultValue( $this->weight );
				$weight->setIsRequired(true);
				$weight->setErrorMessages([
					Form_Field_Float::ERROR_CODE_EMPTY => 'Please enter weight',
					Form_Field_Float::ERROR_CODE_OUT_OF_RANGE => 'Out of range'
				]);
				$weight->setFieldValueCatcher( function( float $value ) {
					$this->setWeight( $value );
				} );
				
				$form->addField($weight);
			}
			
			if( $carrier_service?->getsPackagingHasDimensions() ) {
				$size_w = new Form_Field_Float('size_w', '');
				$size_w->setDefaultValue( $this->size_w );
				$size_w->setIsRequired(true);
				$size_w->setErrorMessages([
					Form_Field_Float::ERROR_CODE_EMPTY => 'Please enter width',
					Form_Field_Float::ERROR_CODE_OUT_OF_RANGE => 'Out of range'
				]);
				$size_w->setFieldValueCatcher( function( float $value ) {
					$this->setSizeW( $value );
				} );
				$form->addField( $size_w );
				
				$size_h = new Form_Field_Float('size_h', '');
				$size_h->setDefaultValue( $this->size_h );
				$size_h->setIsRequired(true);
				$size_h->setErrorMessages([
					Form_Field_Float::ERROR_CODE_EMPTY => 'Please enter height',
					Form_Field_Float::ERROR_CODE_OUT_OF_RANGE => 'Out of range'
				]);
				$size_h->setFieldValueCatcher( function( float $value ) {
					$this->setSizeH( $value );
				} );
				$form->addField( $size_h );
				
				$size_l = new Form_Field_Float('size_l', '');
				$size_l->setDefaultValue( $this->size_l );
				$size_l->setIsRequired(true);
				$size_l->setErrorMessages([
					Form_Field_Float::ERROR_CODE_EMPTY => 'Please enter length',
					Form_Field_Float::ERROR_CODE_OUT_OF_RANGE => 'Out of range'
				]);
				$size_l->setFieldValueCatcher( function( float $value ) {
					$this->setSizeL( $value );
				} );
				$form->addField( $size_l );
			}
			
			
			
			$this->form = $form;
		}
		
		return $this->form;
	}
	
	public function catchForm() : bool
	{
		$form = $this->getForm();
		if(!$form->catchInput()) {
			return false;
		}
		
		$carrier_service = $this->dispatch->getCarrierService();
		$packet_type = $carrier_service?->getAvailablePackaging()[ $form->getField('type')->getValue() ];
		
		
		if( $carrier_service?->getsPackagingHasWeight() ) {
			/**
			 * @var Form_Field_Float $weight
			 */
			$weight = $form->field('weight');
			
			if($packet_type->getMaxWeight()) {
				$weight->setMaxValue( $packet_type->getMaxWeight() );
			}
			if($packet_type->getMinWeight()) {
				$weight->setMinValue( $packet_type->getMinWeight() );
			}
			if(!$packet_type->getWeightEditable()) {
				$weight->setIsReadonly( true );
			}
		}
		
		if( $carrier_service?->getsPackagingHasDimensions() ) {
			/**
			 * @var Form_Field_Float $size_w
			 * @var Form_Field_Float $size_l
			 * @var Form_Field_Float $size_h
			 */
			$size_w = $form->field('size_w');
			$size_l = $form->field('size_l');
			$size_h = $form->field('size_h');
			
			if($packet_type->getMaxW()) {
				$size_w->setMaxValue( $packet_type->getMaxW() );
			}
			if($packet_type->getMinW()) {
				$size_w->setMinValue( $packet_type->getMinW() );
			}
			if(!$packet_type->getWEditable()) {
				$size_w->setIsReadonly( true );
			}
			
			
			if($packet_type->getMaxL()) {
				$size_l->setMaxValue( $packet_type->getMaxL() );
			}
			if($packet_type->getMinL()) {
				$size_l->setMinValue( $packet_type->getMinL() );
			}
			if(!$packet_type->getLEditable()) {
				$size_l->setIsReadonly( true );
			}
			
			if($packet_type->getMaxH()) {
				$size_h->setMaxValue( $packet_type->getMaxH() );
			}
			if($packet_type->getMinH()) {
				$size_h->setMinValue( $packet_type->getMinH() );
			}
			if(!$packet_type->getHEditable()) {
				$size_h->setIsReadonly( true );
			}
			
		}
		
		if(!$form->validate()) {
			return false;
		}

		$form->catchFieldValues();
		
		$this->form = null;
		
		return true;
	}
	
	public function getRemoveForm() : Form
	{
		return new Form('remove_packet_form_'.$this->id, []);
	}

}