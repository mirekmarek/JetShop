<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Payment_Kind;
use JetApplication\Payment_Kind_BankTransfer;

abstract class Core_Payment_Kind_LoanOffline extends Payment_Kind {
	public const CODE = 'loan';
	
	public function __construct()
	{
		$this->setTitle( Tr::_('Loan - offline', dictionary: Tr::COMMON_DICTIONARY) );
		$this->setTitleInvoice( Tr::_('Loan', dictionary: Tr::COMMON_DICTIONARY) );
		
		$this->setAllowedForInvoices( false );
		$this->setAlternativeKindForInvoices( new Payment_Kind_BankTransfer() );
	}
}