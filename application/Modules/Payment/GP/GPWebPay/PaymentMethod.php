<?php /** @noinspection SpellCheckingInspection */

namespace JetApplicationModule\Payment\GP;

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