<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Payment_Kind;
use JetApplication\Payment_Kind_CardOffline;

abstract class Core_Payment_Kind_CardOnline extends Payment_Kind {
	public const CODE = 'card_online';
	
	public function __construct()
	{
		$this->setTitle( Tr::_('Card - online', dictionary: Tr::COMMON_DICTIONARY) );
		$this->setTitleInvoice( Tr::_('Card', dictionary: Tr::COMMON_DICTIONARY) );
		
		$this->setModuleIsRequired( true );
		$this->setIsOnlinePayment( true );
		
		$this->setAllowedForInvoices( false );
		$this->setAlternativeKindForInvoices( new Payment_Kind_CardOffline() );
		
	}
	
}