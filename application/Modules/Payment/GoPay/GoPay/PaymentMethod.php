<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

/** @noinspection SpellCheckingInspection */
namespace JetApplicaTionModule\Payment\GoPay;


use Jet\Tr;

class GoPay_PaymentMethod {
	public const PAYMENT_CARD = 'PAYMENT_CARD';
	public const BANK_ACCOUNT = 'BANK_ACCOUNT';
	public const GOPAY = 'GOPAY';
	public const GPAY = 'GPAY';
	public const PRSMS = 'PRSMS';
	public const MPAYMENT = 'MPAYMENT';
	public const PAYSAFECARD = 'PAYSAFECARD';
	public const SUPERCASH = 'SUPERCASH';
	public const PAYPAL = 'PAYPAL';
	public const BITCOIN = 'BITCOIN';
	
	public static function getList() : array
	{
		return [
			static::PAYMENT_CARD => Tr::_('Card'),
			static::BANK_ACCOUNT => Tr::_('Bank Account'),
			static::GOPAY        => Tr::_('GoPay'),
			static::GPAY         => Tr::_('Google Pay'),
			static::PRSMS        => Tr::_('Premium SMS'),
			static::MPAYMENT     => Tr::_('M Payment'),
			static::PAYSAFECARD  => Tr::_('Pay Safe Card'),
			static::SUPERCASH    => Tr::_('Super Cash'),
			static::PAYPAL       => Tr::_('Pay Pal'),
			static::BITCOIN      => Tr::_('Bit Coin'),
		];
	}
}