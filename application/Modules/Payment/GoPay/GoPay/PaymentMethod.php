<?php /** @noinspection SpellCheckingInspection */

namespace JetApplicationModule\Payment\GoPay;

enum GoPay_PaymentMethod : string {
	case PAYMENT_CARD = 'PAYMENT_CARD';
	case BANK_ACCOUNT = 'BANK_ACCOUNT';
	case GOPAY = 'GOPAY';
	case GPAY = 'GPAY';
	case PRSMS = 'PRSMS';
	case MPAYMENT = 'MPAYMENT';
	case PAYSAFECARD = 'PAYSAFECARD';
	case SUPERCASH = 'SUPERCASH';
	case PAYPAL = 'PAYPAL';
	case BITCOIN = 'BITCOIN';
	
}