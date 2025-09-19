<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Autoloader;
use Jet\IO_Dir;

use Jet\Locale;
use Jet\Tr;
use JetApplication\Payment_Kind;


abstract class Core_Payment_Kind {
	public const CODE = null;

	protected string $title = '';
	protected string $title_invoice = '';

	protected bool $module_is_required = false;
	
	protected bool $is_online_payment = false;
	
	protected bool $is_cod = false;
	
	protected bool $allowed_for_invoices = true;
	
	protected bool $is_bank_transfer = false;
	protected bool $is_loan = false;
	
	protected string $alternative_kind_for_invoices = '';

	/**
	 * @var Payment_Kind[]|null
	 */
	protected static ?array $list = null;

	public function getCode(): string
	{
		return static::CODE;
	}

	public function getTitle( ?Locale $locale=null ): string
	{
		$locale = $locale??Locale::getCurrentLocale();
		
		return Tr::_( $this->title, dictionary: Tr::COMMON_DICTIONARY, locale: $locale );
	}

	protected function setTitle( string $title ): void
	{
		$this->title = $title;
	}
	
	public function getTitleInvoice( ?Locale $locale=null ): string
	{
		if(!$this->title_invoice) {
			return $this->getTitle( $locale );
		}
		
		$locale = $locale??Locale::getCurrentLocale();
		
		return Tr::_( $this->title_invoice, dictionary: Tr::COMMON_DICTIONARY, locale: $locale );
	}
	
	public function setTitleInvoice( string $title_invoice ): void
	{
		$this->title_invoice = $title_invoice;
	}
	
	

	public function moduleIsRequired(): bool
	{
		return $this->module_is_required;
	}

	protected function setModuleIsRequired( bool $module_is_required ): void
	{
		$this->module_is_required = $module_is_required;
	}
	
	public function isOnlinePayment(): bool
	{
		return $this->is_online_payment;
	}
	
	protected function setIsOnlinePayment( bool $is_online_payment ): void
	{
		$this->is_online_payment = $is_online_payment;
	}
	
	public function isBankTransfer(): bool
	{
		return $this->is_bank_transfer;
	}
	
	public function setIsBankTransfer( bool $is_bank_transfer ): void
	{
		$this->is_bank_transfer = $is_bank_transfer;
	}
	
	public function isLoan(): bool
	{
		return $this->is_loan;
	}
	
	public function setIsLoan( bool $is_loan ): void
	{
		$this->is_loan = $is_loan;
	}
	
	
	
	public function isCOD(): bool
	{
		return $this->is_cod;
	}
	
	protected function setIsCOD( bool $is_cod ): void
	{
		$this->is_cod = $is_cod;
	}
	
	public function isAllowedForInvoices(): bool
	{
		return $this->allowed_for_invoices;
	}
	
	protected function setAllowedForInvoices( bool $allowed_for_invoices ): void
	{
		$this->allowed_for_invoices = $allowed_for_invoices;
	}
	
	public function getAlternativeKindForInvoices(): ?Payment_Kind
	{
		if(!$this->alternative_kind_for_invoices) {
			return null;
		}
		
		return Payment_Kind::get($this->alternative_kind_for_invoices);
	}
	
	public function setAlternativeKindForInvoices( ?Payment_Kind $alternative_kind_for_invoices ): void
	{
		if(!$alternative_kind_for_invoices) {
			$this->alternative_kind_for_invoices = '';
		} else {
			$this->alternative_kind_for_invoices = $alternative_kind_for_invoices->getCode();
		}
		
	}
	
	
	
	
	public static function get( string $code ) : ?Payment_Kind
	{
		$list = Payment_Kind::getList();
		if(!isset($list[$code])) {
			return null;
		}
		
		return $list[$code];
	}

	/**
	 * @return Payment_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];
			
			$path = substr( Autoloader::getScriptPath( Payment_Kind::class ), 0, -4).'/';
			
			$files = IO_Dir::getFilesList( $path, '*.php' );
			
			foreach($files as $file) {
				$class_name = Payment_Kind::class.'_'.basename( $file, '.php' );
				
				static::add( new $class_name() );
			}

			
		}

		return static::$list;
	}
	
	protected static function add( Payment_Kind $kind ) : void
	{
		static::$list[$kind->getCode()] = $kind;
	}

	public static function getScope() : array
	{
		$list = Payment_Kind::getList();


		$res = [];

		foreach($list as $item) {
			$res[$item->getCode()] = $item->getTitle();
		}

		return $res;
	}
	
	
	public static function getInvoiceScope() : array
	{
		$list = Payment_Kind::getList();
		
		
		$res = [];
		
		foreach($list as $item) {
			if($item->isAllowedForInvoices()) {
				$res[$item->getCode()] = $item->getTitleInvoice();
			}
		}
		
		return $res;
	}
	
}