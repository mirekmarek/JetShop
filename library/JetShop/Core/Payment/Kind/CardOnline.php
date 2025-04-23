<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Payment_Kind;
use JetApplication\Payment_Kind_CardOffline;

abstract class Core_Payment_Kind_CardOnline extends Payment_Kind {
	public const CODE = 'card_online';
	
	public function __construct()
	{
		$this->setTitle( 'Card - online' );
		$this->setTitleInvoice( 'Card' );
		
		$this->setModuleIsRequired( true );
		$this->setIsOnlinePayment( true );
		
		$this->setAllowedForInvoices( false );
		$this->setAlternativeKindForInvoices( new Payment_Kind_CardOffline() );
		
	}
	
}