<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Payment_Kind;
use JetApplication\Payment_Kind_BankTransfer;

abstract class Core_Payment_Kind_Online extends Payment_Kind {
	public const CODE = 'online_payment';
	
	public function __construct()
	{
		$this->setTitle( 'General online bank transfer' );
		$this->setModuleIsRequired( true );
		$this->setIsOnlinePayment( true );
		$this->setTitleInvoice( 'Bank transfer' );
		
		$this->setAllowedForInvoices( false );
		$this->setAlternativeKindForInvoices( new Payment_Kind_BankTransfer() );
	}
	
}