<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\InvoiceManager;

use Jet\Locale;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\CompanyInfo;
use JetApplication\EShop;
use JetApplication\EShopEntity_AccountingDocument;
use JetApplication\EShopEntity_AccountingDocument_Item;
use JetApplication\Order;
use JetApplication\PDF;
use JetApplication\PDF_Template;
use JetApplication\Template_Property_Param;
use JetApplication\Invoice_VATOverviewItem;

abstract class PDFTemplate extends PDF_Template {
	
	protected null|EShopEntity_AccountingDocument $invoice = null;
	protected ?CompanyInfo $company_info = null;
	protected Locale $locale;
	
	public function setupPDF( EShop $eshop, PDF $pdf ): void
	{
	}
	
	public function setInvoice( EShopEntity_AccountingDocument $invoice ): void
	{
		$this->invoice = $invoice;
		$this->company_info = CompanyInfo::get( $this->invoice->getEshop() );
		$this->locale = $invoice->getEshop()->getLocale();
	}
	
	public function formatWithCurrency_WithoutVAT( float $value ) : string
	{
		return Application_Service_Admin::PriceFormatter()->formatWithCurrency_WithoutVAT( $this->invoice->getCurrency(), $value );
	}
	
	public function formatWithCurrency_WithVAT( float $value ) : string
	{
		return Application_Service_Admin::PriceFormatter()->formatWithCurrency_WithVAT( $this->invoice->getCurrency(), $value );
	}
	
	public function formatWithCurrency_VAT( float $value ) : string
	{
		return Application_Service_Admin::PriceFormatter()->formatWithCurrency_VAT( $this->invoice->getCurrency(), $value );
	}
	
	protected function initCommonFields(): void
	{
		$this->addCondition('has_VAT', 'Has VAT')
			->setConditionEvaluator( function() : bool {
				return $this->invoice->hasVAT();
			});
		$this->addCondition('stamp', 'Stamp and signature is defined')
			->setConditionEvaluator( function() : bool {
				return (bool)$this->company_info->getStampAndSignature();
			});
		
		$this->addCondition('logo', 'Logo is defined')
			->setConditionEvaluator( function() : bool {
				return (bool)$this->company_info->getLogo();
			});
		
		
		$this->addCondition('has_order', 'Order is associadet to the invoice')
			->setConditionEvaluator( function() : bool {
				return (bool)$this->invoice->getOrderId();
			});
		
		$this->addCondition('is_paid', 'Invoice is paid')
			->setConditionEvaluator( function() : bool {
				return $this->invoice->getIsPaid();
			});
		
		
		$this->addProperty( 'number', Tr::_( 'Document number' ) )
			->setPropertyValueCreator( function() : string {
				return $this->invoice->getNumber();
			} );
		
		$this->addProperty( 'order_number', Tr::_( 'Order number' ) )
			->setPropertyValueCreator( function() : string {
				if(!$this->invoice->getOrderId()) {
					return '';
				}
				
				$order = Order::get( $this->invoice->getOrderId() );
				
				return $order?->getNumber()??'';
			} );
		
		$logo = $this->addProperty('logo', Tr::_('Logo'));
		$logo->setPropertyValueCreator(function() : string {
				return $this->company_info->getLogoThbUrl(
					$params['max_w']??70,
					$params['max_h']??70
				);
			});
		$logo->addParam( Template_Property_Param::TYPE_INT, 'max_w', Tr::_('Maximal image width') );
		$logo->addParam( Template_Property_Param::TYPE_INT, 'max_h', Tr::_('Maximal image height') );
		
		
		$stamp = $this->addProperty('stamp', Tr::_('Stamp'));
		$stamp->setPropertyValueCreator(function( $params ) : string {
				return $this->company_info->getStampAndSignatureThbUrl(
					$params['max_w']??200,
					$params['max_h']??80
				);
			});
		$stamp->addParam( Template_Property_Param::TYPE_INT, 'max_w', Tr::_('Maximal image width') );
		$stamp->addParam( Template_Property_Param::TYPE_INT, 'max_h', Tr::_('Maximal image height') );
		
		$this->addProperty('issuer_company_name', Tr::_('Issuer - company name'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerCompanyName();
			});
		
		$this->addProperty('issuer_address_street_no', Tr::_('Issuer - Address Street No'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerAddressStreetNo();
			});
		
		$this->addProperty('issuer_address_zip', Tr::_('Issuer - Address ZIP'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerAddressZip();
			});
		$this->addProperty('issuer_address_town', Tr::_('Issuer - Address Town'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerAddressTown();
			});
		$this->addProperty('issuer_company_id', Tr::_('Issuer - Company ID'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerCompanyId();
			});
		$this->addProperty('issuer_company_vat_id', Tr::_('Issuer - Company VAT ID'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerCompanyVATId();
			});
		$this->addProperty('issuer_phone', Tr::_('Issuer - Phone'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerPhone();
			});
		
		$this->addProperty('issuer_email', Tr::_('Issuer - email'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerEmail();
			});
		
		$this->addProperty('issuer_info', Tr::_('Issuer - info'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerInfo();
			});
		$this->addProperty('issuer_bank_account', Tr::_('Issuer - bank account'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerBankAccountNumber();
			});
		$this->addProperty('issuer_bank_name', Tr::_('Issuer - bank name'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getIssuerBankName();
			});
		
		
		$this->addProperty('custoner_company_name', Tr::_('Custoner - company name'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerCompanyName();
			});
		
		$this->addProperty('custoner_first_name', Tr::_('Custoner - first name'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerFirstName();
			});
		
		$this->addProperty('custoner_surname', Tr::_('Custoner - surname'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerSurname();
			});
		
		
		$this->addProperty('custoner_address_street_no', Tr::_('Custoner - Address Street No'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerAddressStreetNo();
			});
		
		$this->addProperty('custoner_address_zip', Tr::_('Custoner - Address ZIP'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerAddressZip();
			});
		
		$this->addProperty('custoner_address_town', Tr::_('Custoner - Address Town'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerAddressTown();
			});
		
		$this->addProperty('custoner_company_id', Tr::_('Custoner - Company ID'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerCompanyId();
			});
		
		$this->addProperty('custoner_company_vat_id', Tr::_('Custoner - Company VAT ID'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerCompanyVatId();
			});
		
		$this->addProperty('custoner_phone', Tr::_('Custoner - Phone'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerPhone();
			});
		
		$this->addProperty('custoner_email', Tr::_('Custoner - email'))
			->setPropertyValueCreator(function() : string {
				return $this->invoice->getCustomerEmail();
			});
		
		
		$this->addProperty( 'date_of_issue', Tr::_( 'Date of issue' ) )
			->setPropertyValueCreator( function() : string {
				return $this->locale->formatDate( $this->invoice->getInvoiceDate() );
			} );
		
		$this->addProperty( 'date_of_taxable_supply', Tr::_( 'Date of taxable supply' ) )
			->setPropertyValueCreator( function() : string {
				return $this->locale->formatDate( $this->invoice->getDateOfTaxableSupply() );
			} );
		
		$this->addProperty( 'due_date', Tr::_( 'Due date' ) )
			->setPropertyValueCreator( function() : string {
				return $this->locale->formatDate( $this->invoice->getDueDate() );
			} );
		
		$this->addProperty( 'payment_method', Tr::_( 'Payment method' ) )
			->setPropertyValueCreator( function() : string {
				return $this->invoice->getPaymentKind()?->getTitleInvoice( $this->invoice->getEshop()->getLocale() )??'';
			} );
		
		
		$this->addProperty( 'total_without_vat', Tr::_( 'Total without VAT' ) )
			->setPropertyValueCreator( function() : string {
				return $this->formatWithCurrency_WithoutVAT( $this->invoice->getTotalWithoutVat());
			} );
		
		$this->addProperty( 'total_vat', Tr::_( 'Total VAT' ) )
			->setPropertyValueCreator( function() : string {
				return $this->formatWithCurrency_VAT(  $this->invoice->getTotalVat());
			} );
		
		$this->addProperty( 'total_round', Tr::_( 'Total Round' ) )
			->setPropertyValueCreator( function() : string {
				return $this->formatWithCurrency_VAT(  $this->invoice->getTotalRound());
			} );
		
		
		$this->addProperty( 'total', Tr::_( 'Total' ) )
			->setPropertyValueCreator( function() : string {
				if($this->invoice->hasVAT()) {
					return $this->formatWithCurrency_WithVAT( $this->invoice->getTotal());
				} else {
					return $this->formatWithCurrency_WithoutVAT( $this->invoice->getTotal());
				}
			} );
		
		
		
		
		
		$items_block = $this->addPropertyBlock('items', Tr::_('Items'));
		$items_block->setItemListCreator( function() : iterable {
			return $this->invoice->getItems();
		} );
		
		$items_block->addProperty('title', Tr::_('Title'))
				->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
					return $item->getTitle();
				} );
		
		$items_block->addProperty('number_of_units', Tr::_('Number of units'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->locale->float($item->getNumberOfUnits()).'&nbsp;'.$item->getMeasureUnit()?->getName();
			} );
		
		$items_block->addProperty('price_per_unit_without_vat', Tr::_('Price per Unit - Without VAT'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->formatWithCurrency_WithoutVAT( $item->getPricePerUnit_WithoutVat());
			} );
		$items_block->addProperty('price_per_unit_with_vat', Tr::_('Price per Unit - With VAT'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->formatWithCurrency_WithVAT( $item->getPricePerUnit_WithVat());
			} );
		
		$items_block->addProperty('price_per_unit_vat', Tr::_('Price per Unit - VAT'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->formatWithCurrency_VAT(  $item->getPricePerUnit_Vat());
			} );
		
		$items_block->addProperty('vat_rate', Tr::_('VAT rate'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->locale->float($item->getVatRate());
			} );
		
		$items_block->addProperty('total_amount_without_vat', Tr::_('Total amount - Without VAT'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->formatWithCurrency_WithoutVAT( $item->getTotalAmount_WithoutVat());
			} );
		$items_block->addProperty('total_amount_with_vat', Tr::_('Total amount - With VAT'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->formatWithCurrency_WithVAT( $item->getTotalAmount_WithVat());
			} );
		$items_block->addProperty('total_amount_with_vat', Tr::_('Total amount - VAT'))
			->setPropertyValueCreator( function( EShopEntity_AccountingDocument_Item $item ) : string {
				return $this->formatWithCurrency_VAT( $item->getTotalAmount_Vat());
			} );
		
		
		
		$vat_block = $this->addPropertyBlock('vat_overview', Tr::_('VAT Overview'));
		$vat_block->setItemListCreator( function() : iterable {
			return $this->invoice->getVATOverview();
		} );
		
		$vat_block->addProperty('vat_rate', Tr::_('VAT rate'))
			->setPropertyValueCreator( function( Invoice_VATOverviewItem $vat_ov_item ) : string {
				return $this->locale->float($vat_ov_item->getVatRate());
			} );
		
		$vat_block->addProperty('tax_base', Tr::_('Tax base'))
			->setPropertyValueCreator( function( Invoice_VATOverviewItem $vat_ov_item ) : string {
				return $this->formatWithCurrency_WithoutVAT($vat_ov_item->getTaxBase());
			} );
		
		$vat_block->addProperty('tax', Tr::_('Tax'))
			->setPropertyValueCreator( function( Invoice_VATOverviewItem $vat_ov_item ) : string {
				return $this->formatWithCurrency_VAT($vat_ov_item->getTax());
			} );
		
		
	}
	
}