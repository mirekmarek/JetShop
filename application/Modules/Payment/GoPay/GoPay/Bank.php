<?php /** @noinspection SpellCheckingInspection */

namespace JetApplicationModule\Payment\GoPay;

enum GoPay_Bank : string {
	case CEKOCZPP = 'CEKOCZPP'; //ČSOB
	case AGBACZPP = 'AGBACZPP'; //GE Money Bank
	case BACXCZPP = 'BACXCZPP'; //UniCredit Bank
	case GIBACZPX = 'GIBACZPX'; //Česká spořitelna
	case RZBCCZPP = 'RZBCCZPP'; //Raiffeisenbank
	case KOMBCZPP = 'KOMBCZPP'; //Komerční Banka
	case BREXCZPP = 'BREXCZPP'; //mBank
	case FIOBCZPP = 'FIOBCZPP'; //FIO Banka
	case ZUNOCZPP = 'ZUNOCZPP'; //ZUNO
	case CEKOCZPP_ERA = 'CEKOCZPP-ERA'; //ERA
	case SUBASKBX = 'SUBASKBX'; //Všeobecná úverová banka Banka
	case TATRSKBX = 'TATRSKBX'; //Tatra Banka
	case UNCRSKBX = 'UNCRSKBX'; //Unicredit Bank SK
	case GIBASKBX = 'GIBASKBX'; //Slovenská spořitelna
	case OTPVSKBX = 'OTPVSKBX'; //OTP Banka
	case POBNSKBA = 'POBNSKBA'; //Poštová Banka
	case CEKOSKBX = 'CEKOSKBX'; //ČSOB SK
	case LUBASKBX = 'LUBASKBX'; //Sberbank Slovensko
	
	public function name(): string
	{
		return match($this) {
			self::CEKOCZPP => 'ČSOB',
			self::AGBACZPP => 'GE Money Bank',
			self::BACXCZPP => 'UniCredit Bank',
			self::GIBACZPX => 'Česká spořitelna',
			self::RZBCCZPP => 'Raiffeisenbank',
			self::KOMBCZPP => 'Komerční Banka',
			self::BREXCZPP => 'mBank',
			self::FIOBCZPP => 'FIO Banka',
			self::ZUNOCZPP => 'ZUNO',
			self::CEKOCZPP_ERA => 'ERA',
			self::SUBASKBX => 'Všeobecná úverová banka Banka',
			
			self::TATRSKBX => 'Tatra Banka',
			self::UNCRSKBX => 'Unicredit Bank SK',
			self::GIBASKBX => 'Slovenská spořitelna',
			self::OTPVSKBX => 'OTP Banka',
			self::POBNSKBA => 'Poštová Banka',
			self::CEKOSKBX => 'ČSOB SK',
			self::LUBASKBX => 'Sberbank Slovensko',

		};
	}
	
	public static function get( string $bank ) : ?self
	{
		foreach (GoPay_Bank::cases() as $bank ) {
			if( $bank == $bank->name ){
				return $bank;
			}
		}
		
		return null;
	}
}