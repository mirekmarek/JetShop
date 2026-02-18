<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

/** @noinspection SpellCheckingInspection */
namespace JetApplicationModule\Payment\GoPay;


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
	public const TWISTO_DEFERRED_PAYMENT = 'TWISTO|DEFERRED_PAYMENT';
	public const TWISTO_PAY_IN_THREE = 'TWISTO|PAY_IN_THREE';
	public const SKIPPAY_DEFERRED_PAYMENT = 'SKIPPAY|DEFERRED_PAYMENT';
	public const SKIPPAY_PAY_IN_THREE = 'SKIPPAY|PAY_IN_THREE';
	
	
	public static function getList() : array
	{
		return [
			static::PAYMENT_CARD => Tr::_('Card'),
			static::BANK_ACCOUNT => Tr::_('Bank Account'),
			static::GOPAY        => Tr::_('GoPay'),
			static::GPAY         => Tr::_('Google Pay'),
			static::TWISTO_DEFERRED_PAYMENT   => Tr::_('Twisto - deferred payment'),
			static::TWISTO_PAY_IN_THREE       => Tr::_('Twisto - pay in three'),
			static::SKIPPAY_DEFERRED_PAYMENT  => Tr::_('Skip Pay - deferred payment'),
			static::SKIPPAY_PAY_IN_THREE      => Tr::_('Skip Pay - pay in three'),
			static::PRSMS        => Tr::_('Premium SMS'),
			static::MPAYMENT     => Tr::_('M Payment'),
			static::PAYSAFECARD  => Tr::_('Pay Safe Card'),
			static::SUPERCASH    => Tr::_('Super Cash'),
			static::PAYPAL       => Tr::_('Pay Pal'),
			static::BITCOIN      => Tr::_('Bit Coin'),
		];
	}
}