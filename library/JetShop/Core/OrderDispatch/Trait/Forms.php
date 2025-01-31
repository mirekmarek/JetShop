<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Input;
use Jet\Form_Field_Textarea;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Packet;

trait Core_OrderDispatch_Trait_Forms {
	
	public function getAddPacketForm() : Form
	{
		if(!$this->new_packet) {
			$this->new_packet = new OrderDispatch_Packet();
			/**
			 * @var OrderDispatch $this
			 */
			$this->new_packet->setDispatch( $this );
		}
		return $this->new_packet->getForm();
	}
	
	public function catchAddPacketForm() : bool
	{
		if( !$this->new_packet->catchForm() ) {
			return false;
		}
		
		
		$this->new_packet->save();
		
		$this->packets[ $this->new_packet->getId() ] = $this->new_packet;
		
		$this->new_packet = null;
		
		return true;
	}
	
	public function getAdditionalConsignmentParametersForm() : Form
	{
		$fields = [];
		
		if($this->getCarrier()) {
			foreach( $this->getCarrier()->getAdditionalConsignmentParameters() as $param ) {
				$chb = new Form_Field_Checkbox( $param->getCode(), '' );
				$chb->setDefaultValue( $this->hasAdditionalConsignmentParameter( $param->getCode() ) );
				$chb->setFieldValueCatcher( function( bool $value ) use ($param) {
					if( $value ) {
						$this->addAdditionalConsignmentParameter( $param->getCode() );
					} else {
						$this->removeAdditionalConsignmentParameter( $param->getCode() );
					}
				} );
				$fields[] = $chb;
			}
		}
		
		
		$form  = new Form('additional_consignment_parameters_form', $fields);
		$form->setDoNotTranslateTexts( true );
		
		return $form;
	}
	
	public function getOurNoteForm() : Form
	{
		$note = new Form_Field_Textarea('our_note', '');
		$note->setDefaultValue( $this->our_note );
		$note->setFieldValueCatcher( function( $value ) {
			$this->our_note = $value;
			$this->save();
		} );
		
		$form = new Form('our_note_form', [$note]);
		return $form;
	}
	
	public function getRecipientNoteForm() : Form
	{
		$note = new Form_Field_Input('recipient_note', '');
		$note->setDefaultValue( $this->recipient_note );
		$note->setFieldValueCatcher( function( $value ) {
			$this->recipient_note = $value;
			$this->save();
		} );
		
		$form = new Form('recipient_note_form', [$note]);
		return $form;
	}
	
	
	public function getDriverNoteForm() : Form
	{
		$note = new Form_Field_Input('driver_note', '');
		$note->setDefaultValue( $this->driver_note );
		$note->setFieldValueCatcher( function( $value ) {
			$this->driver_note = $value;
			$this->save();
		} );
		
		$form = new Form('driver_note_form', [$note]);
		return $form;
	}
	
}