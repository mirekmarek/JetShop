<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\DataList;
use JetApplication\EShopEntity_Address;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop_Managers;
use JetApplication\EShop;

use Jet\Form;

#[DataModel_Definition(
	name: 'company_info',
	database_table_name: 'company_info',
)]
abstract class Core_CompanyInfo extends EShopEntity_WithEShopRelation {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_EMAIL,
		label: 'Contact e-mail:'
	)]
	protected string $email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Contact phone::'
	)]
	protected string $phone = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company name:'
	)]
	protected string $company_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company ID:'
	)]
	protected string $company_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Company VAT ID:'
	)]
	protected string $company_vat_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Contact person - first name:'
	)]
	protected string $first_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Contact person - surname:'
	)]
	protected string $surname = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Street address:'
	)]
	protected string $address_street_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Town:'
	)]
	protected string $address_town = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'ZIP:'
	)]
	protected string $address_zip = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Country:',
		select_options_creator: [
			DataList::class,
			'countries'
		],
		
	)]
	protected string $address_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Invoice company info:'
	)]
	protected string $invoice_info = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is VAT payer'
	)]
	protected bool $is_vat_payer = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $logo = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $stamp_and_signature = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Bank - name:'
	)]
	protected string $bank_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Bank - account number:'
	)]
	protected string $bank_account_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Text before invoice items:'
	)]
	protected string $text_before_invoice_items = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Text after invoice items:'
	)]
	protected string $text_aftere_invoice_items = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Text before proforma invoice items:'
	)]
	protected string $text_before_proforma_invoice_items = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Text after proforma invoice items:'
	)]
	protected string $text_aftere_proforma_invoice_items = '';
	
	
	protected ?Form $edit_form = null;
	
	public static function get( EShop $eshop ) : static
	{
		$info = static::load( $eshop->getWhere() );
		if(!$info) {
			$info = new static();
			$info->setEshop( $eshop );
			$info->save();
		}
		
		return $info;
	}
	
	public function getEmail(): string
	{
		return $this->email;
	}
	
	public function setEmail( string $email ): void
	{
		$this->email = $email;
	}
	
	public function getPhone(): string
	{
		return $this->phone;
	}
	
	public function setPhone( string $phone ): void
	{
		$this->phone = $phone;
	}
	
	public function getCompanyName(): string
	{
		return $this->company_name;
	}
	
	public function setCompanyName( string $company_name ): void
	{
		$this->company_name = $company_name;
	}
	
	public function getCompanyId(): string
	{
		return $this->company_id;
	}
	
	public function setCompanyId( string $company_id ): void
	{
		$this->company_id = $company_id;
	}
	
	public function getCompanyVatId(): string
	{
		return $this->company_vat_id;
	}
	
	public function setCompanyVatId( string $company_vat_id ): void
	{
		$this->company_vat_id = $company_vat_id;
	}
	
	public function getFirstName(): string
	{
		return $this->first_name;
	}
	
	public function setFirstName( string $first_name ): void
	{
		$this->first_name = $first_name;
	}
	
	public function getSurname(): string
	{
		return $this->surname;
	}
	
	public function setSurname( string $surname ): void
	{
		$this->surname = $surname;
	}
	
	public function getAddressStreetNo(): string
	{
		return $this->address_street_no;
	}
	
	public function setAddressStreetNo( string $address_street_no ): void
	{
		$this->address_street_no = $address_street_no;
	}
	
	public function getAddressTown(): string
	{
		return $this->address_town;
	}
	
	public function setAddressTown( string $address_town ): void
	{
		$this->address_town = $address_town;
	}
	
	public function getAddressZip(): string
	{
		return $this->address_zip;
	}
	
	public function setAddressZip( string $address_zip ): void
	{
		$this->address_zip = $address_zip;
	}
	
	public function getAddressCountry(): string
	{
		return $this->address_country;
	}
	
	public function setAddressCountry( string $address_country ): void
	{
		$this->address_country = $address_country;
	}
	
	public function getInvoiceInfo(): string
	{
		return $this->invoice_info;
	}
	
	public function setInvoiceInfo( string $invoice_info ): void
	{
		$this->invoice_info = $invoice_info;
	}
	
	public function getIsVatPayer(): bool
	{
		return $this->is_vat_payer;
	}
	
	public function setIsVatPayer( bool $is_vat_payer ): void
	{
		$this->is_vat_payer = $is_vat_payer;
	}
	
	
	
	public function getLogo(): string
	{
		return $this->logo;
	}
	
	public function setLogo( string $logo ): void
	{
		$this->logo = $logo;
	}
	
	public function getLogoThbUrl( int $max_w, $max_h ) : string
	{
		if(!$this->logo) {
			return '';
		}
		return EShop_Managers::Image()->getThumbnailUrl( $this->logo, $max_w, $max_h );
	}
	
	public function getLogoUrl() : string
	{
		if(!$this->logo) {
			return '';
		}
		return EShop_Managers::Image()->getUrl( $this->logo );
	}
	
	public function getStampAndSignature(): string
	{
		return $this->stamp_and_signature;
	}
	
	public function setStampAndSignature( string $stamp_and_signature ): void
	{
		$this->stamp_and_signature = $stamp_and_signature;
	}
	
	public function getStampAndSignatureThbUrl( int $max_w, $max_h ) : string
	{
		if(!$this->stamp_and_signature) {
			return '';
		}
		return EShop_Managers::Image()->getThumbnailUrl( $this->stamp_and_signature, $max_w, $max_h );
	}
	
	public function getStampAndSignatureUrl() : string
	{
		if(!$this->stamp_and_signature) {
			return '';
		}
		return EShop_Managers::Image()->getUrl( $this->stamp_and_signature );
	}
	
	public function getBankName(): string
	{
		return $this->bank_name;
	}
	
	public function setBankName( string $bank_name ): void
	{
		$this->bank_name = $bank_name;
	}
	
	public function getBankAccountNumber(): string
	{
		return $this->bank_account_number;
	}
	
	public function setBankAccountNumber( string $bank_account_number ): void
	{
		$this->bank_account_number = $bank_account_number;
	}
	
	public function getTextBeforeInvoiceItems(): string
	{
		return $this->text_before_invoice_items;
	}
	
	public function setTextBeforeInvoiceItems( string $text_before_invoice_items ): void
	{
		$this->text_before_invoice_items = $text_before_invoice_items;
	}
	
	public function getTextAftereInvoiceItems(): string
	{
		return $this->text_aftere_invoice_items;
	}
	
	public function setTextAftereInvoiceItems( string $text_aftere_invoice_items ): void
	{
		$this->text_aftere_invoice_items = $text_aftere_invoice_items;
	}
	
	public function getTextBeforeProformaInvoiceItems(): string
	{
		return $this->text_before_proforma_invoice_items;
	}
	
	public function setTextBeforeProformaInvoiceItems( string $text_before_proforma_invoice_items ): void
	{
		$this->text_before_proforma_invoice_items = $text_before_proforma_invoice_items;
	}
	
	public function getTextAftereProformaInvoiceItems(): string
	{
		return $this->text_aftere_proforma_invoice_items;
	}
	
	public function setTextAftereProformaInvoiceItems( string $text_aftere_proforma_invoice_items ): void
	{
		$this->text_aftere_proforma_invoice_items = $text_aftere_proforma_invoice_items;
	}
	
	
	
	
	public function setAddress( EShopEntity_Address $address ) : void
	{
		$this->setCompanyName( $address->getCompanyName() );
		$this->setCompanyId( $address->getCompanyId() );
		$this->setCompanyVatId( $address->getCompanyVatId() );
		$this->setFirstName( $address->getFirstName() );
		$this->setSurname( $address->getSurname() );
		$this->setAddressStreetNo( $address->getAddressStreetNo() );
		$this->setAddressTown( $address->getAddressTown() );
		$this->setAddressZip( $address->getAddressZip() );
		$this->setAddressCountry( $address->getAddressCountry() );
	}
	
	public function getAddress() : EShopEntity_Address
	{
		$address = new EShopEntity_Address();
		
		$address->setCompanyName( $this->getCompanyName( ) );
		$address->setCompanyId( $this->getCompanyId( ) );
		$address->setCompanyVatId( $this->getCompanyVatId( ) );
		$address->setFirstName( $this->getFirstName( ) );
		$address->setSurname( $this->getSurname( ) );
		$address->setAddressStreetNo( $this->getAddressStreetNo( ) );
		$address->setAddressTown( $this->getAddressTown( ) );
		$address->setAddressZip( $this->getAddressZip( ) );
		$address->setAddressCountry( $this->getAddressCountry( ) );
		
		return $address;
	}
	
	public function getEditForm() : Form
	{
		if(!$this->edit_form) {
			$this->edit_form = $this->createForm('edit_form');
		}
		
		return $this->edit_form;
	}
}