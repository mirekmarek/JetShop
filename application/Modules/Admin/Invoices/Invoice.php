<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Invoices;

use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_Date;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use Jet\Form_Field_Textarea;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Invoice as Application_Invoice;
use JetApplication\Entity_Price;
use JetApplication\Payment_Kind;


class Invoice extends Application_Invoice implements Admin_Entity_WithEShopRelation_Interface
{
	protected ?Form $correction_invoice_form = null;
	protected ?Invoice $new_correction_invoice = null;
	
	public function isEditable(): bool
	{
		if( !Main::getCurrentUserCanEdit() ) {
			return false;
		}
		
		return parent::isEditable();
	}
	
	
	public function setEditable( bool $editable ): void
	{
	}
	
	public function getEditURL(): string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function getAddForm(): Form
	{
		return new Form( '', [] );
	}
	
	public function catchAddForm(): bool
	{
		return false;
	}
	
	public function getEditForm(): Form
	{
		return new Form( '', [] );
	}
	
	public function catchEditForm(): bool
	{
		return false;
	}
	
	public function getNewCorrectionInvoice() : static
	{
		if(!$this->new_correction_invoice) {
			$this->new_correction_invoice = $this->prepareCorrectionInvoice();
		}
		
		return $this->new_correction_invoice;
	}
	
	public function getCreateCorrectionInvoiceForm() : Form
	{
		if(!$this->correction_invoice_form) {
			$this->getNewCorrectionInvoice();
			
			$fields = [];
			$pricelist = $this->getPricelist();
			
			$payment_kind = new Form_Field_Select('payment_kind', 'Payment method:');
			$payment_kind->setSelectOptions( Payment_Kind::getInvoiceScope() );
			$payment_kind->setFieldValueCatcher( function( string $value ) {
				$this->new_correction_invoice->setPaymentKind( Payment_Kind::get( $value ) );
			} );
			$fields[] = $payment_kind;
			
			
			$reason = new Form_Field_Textarea('reason', 'Reason of correction:');
			$reason->setIsRequired( true );
			$reason->setErrorMessages([
				Form_Field_Textarea::ERROR_CODE_EMPTY => 'Please enter the reason of invoice correction'
			]);
			$reason->setFieldValueCatcher(function( string $value ) {
				$this->new_correction_invoice->setCorrectionReason( $value );
			});
			$fields[] = $reason;
			
			$invoice_date = new Form_Field_Date('invoice_date', 'Invoice date:');
			$invoice_date->setDefaultValue( Data_DateTime::now() );
			$invoice_date->setFieldValueCatcher( function( Data_DateTime $value ) {
				$this->new_correction_invoice->setInvoiceDate( $value );
			} );
			$fields[] = $invoice_date;
			
			$date_of_taxable_supply = new Form_Field_Date('date_of_taxable_supply', 'Date of taxable supply:');
			$date_of_taxable_supply->setDefaultValue( Data_DateTime::now() );
			$date_of_taxable_supply->setFieldValueCatcher( function( Data_DateTime $value ) {
				$this->new_correction_invoice->setDateOfTaxableSupply( $value );
			} );
			$fields[] = $date_of_taxable_supply;
			
			$due_date = new Form_Field_Date('due_date', 'Due date:');
			$due_date->setDefaultValue( Data_DateTime::now() );
			$due_date->setFieldValueCatcher( function( Data_DateTime $value ) {
				$this->new_correction_invoice->setDueDate( $value );
			} );
			$fields[] = $due_date;
			
			$i = 0;
			foreach( $this->new_correction_invoice->getItems() as $item ) {
				$i++;
				
				$number_of_units = new Form_Field_Float('/number_of_units/'.$i, '' );
				$number_of_units->setDefaultValue( $item->getNumberOfUnits() );
				$number_of_units->input()->setDataAttribute('i', $i);
				$number_of_units->input()->setDataAttribute('vat', $item->getVatRate());
				$number_of_units->input()->setDataAttribute('round_with_vat', $pricelist->getRoundPrecision_WithVAT());
				$number_of_units->input()->setDataAttribute('round_without_vat', $pricelist->getRoundPrecision_WithoutVAT());
				$number_of_units->input()->setDataAttribute('round_vat', $pricelist->getRoundPrecision_VAT());
				$number_of_units->input()->addJsAction('onchange', "CorrectionInvoice.calcItem_numberOfUnits(this);");
				$number_of_units->setFieldValueCatcher( function( float $value ) use ($item) {
					$item->setNumberOfUnits( $value, $item->getMeasureUnit() );
				} );
				$fields[] = $number_of_units;
				
				$per_unit_with_vat = new Form_Field_Float('/price_per_unit_with_vat/'.$i, '' );
				$per_unit_with_vat->setDefaultValue( $pricelist->round_WithVAT( -1*$item->getPricePerUnitWithVat() ) );
				$per_unit_with_vat->input()->setDataAttribute('i', $i);
				$per_unit_with_vat->input()->setDataAttribute('vat', $item->getVatRate());
				$per_unit_with_vat->input()->setDataAttribute('round_with_vat', $pricelist->getRoundPrecision_WithVAT());
				$per_unit_with_vat->input()->setDataAttribute('round_without_vat', $pricelist->getRoundPrecision_WithoutVAT());
				$per_unit_with_vat->input()->setDataAttribute('round_vat', $pricelist->getRoundPrecision_VAT());
				$per_unit_with_vat->input()->addJsAction('onchange', "CorrectionInvoice.calcItem_WithVAT(this);");
				$fields[] = $per_unit_with_vat;
				
				$per_unit_without_vat = new Form_Field_Float('/price_per_unit_without_vat/'.$i, '' );
				$per_unit_without_vat->setDefaultValue( $pricelist->round_WithoutVAT( -1*$item->getPricePerUnitWithoutVat() ) );
				$per_unit_without_vat->input()->setDataAttribute('i', $i);
				$per_unit_without_vat->input()->setDataAttribute('vat', $item->getVatRate());
				$per_unit_without_vat->input()->setDataAttribute('round_with_vat', $pricelist->getRoundPrecision_WithVAT());
				$per_unit_without_vat->input()->setDataAttribute('round_without_vat', $pricelist->getRoundPrecision_WithoutVAT());
				$per_unit_without_vat->input()->setDataAttribute('round_vat', $pricelist->getRoundPrecision_VAT());
				$per_unit_without_vat->input()->addJsAction('onchange', "CorrectionInvoice.calcItem_WithoutVAT(this);");
				$per_unit_without_vat->setFieldValueCatcher( function( float $value ) use ($item) {
					$price = new class extends Entity_Price {};
					
					$price->setPricelistCode( $this->getPricelistCode() );
					$price->setVatRate( $item->getVatRate() );
					
					$price->setPriceWithoutVAT( $value );
					
					$item->setupPricePerUnit( $price );
				} );
				
				$fields[] = $per_unit_without_vat;
				
				$vat_rate = new Form_Field_Float('/vat_rate/'.$i, '' );
				$vat_rate->setDefaultValue( $item->getVatRate() );
				$vat_rate->setIsReadonly( true );
				$fields[] = $vat_rate;
				
				$total_amount_without_vat = new Form_Field_Float('/total_amount_without_vat/'.$i, '' );
				$total_amount_without_vat->setDefaultValue( $pricelist->round_WithoutVAT( -1*$item->getTotalAmountWithoutVat() ) );
				$total_amount_without_vat->setIsReadonly( true );
				$fields[] = $total_amount_without_vat;
				
				$total_amount_with_vat = new Form_Field_Float('/total_amount_with_vat/'.$i, '' );
				$total_amount_with_vat->setDefaultValue( $pricelist->round_WithVAT( -1*$item->getTotalAmountWithVat() ) );
				$total_amount_with_vat->setIsReadonly( true );
				$fields[] = $total_amount_with_vat;
			}
			
			$this->correction_invoice_form = new Form( 'correction_invoice_form', $fields );
		}
		
		return $this->correction_invoice_form;
	}
	
	public function catchCorrectionInvoiceForm(): bool|static
	{
		$form = $this->getCreateCorrectionInvoiceForm();
		if(!$form->catch()) {
			return false;
		}
		
		$correction_invoice = $this->getNewCorrectionInvoice();
		
		foreach( $correction_invoice->items as $k=>$item ) {
			if($item->getNumberOfUnits()==0) {
				unset( $correction_invoice->items[$k] );
			}
		}
		
		$correction_invoice->recalculate();
		
		$correction_invoice->save();
		
		return $correction_invoice;
	}
	
}