<?php /** @noinspection SpellCheckingInspection */

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Payment\GP;


use Jet\Tr;

class GPWebPay_PaymentMethod {
	public const CRD = 'CRD';
	public const GPAY = 'GPAY';
	public const APAY = 'APAY';
	
	public static function getList() : array
	{
		
		return [
			static::CRD => Tr::_('Card'),
			static::GPAY => Tr::_('Google Pay'),
			static::APAY => Tr::_('Apple Pay'),
		];
	}
}