<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

abstract class Core_Payment_Kind {


	const KIND_COD = 'COD';
	const KIND_ONLINE_PAYMENT = 'online_payment';
	const KIND_CASH = 'cash';
	const KIND_LOAN = 'loan';
	const KIND_LOAN_ONLINE = 'loan_online';
	const KIND_BANK_TRANSFER = 'bank_transfer';




	protected string $code = '';

	protected string $title = '';

	protected bool $module_is_required = false;

	/**
	 * @var Payment_Kind[]|null
	 */
	protected static ?array $list = null;

	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode( string $code ): void
	{
		$this->code = $code;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle( string $title ): void
	{
		$this->title = $title;
	}

	public function moduleIsRequired(): bool
	{
		return $this->module_is_required;
	}

	public function setModuleIsRequired( bool $module_is_required ): void
	{
		$this->module_is_required = $module_is_required;
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

			$COD = new Payment_Kind();
			$COD->setCode( static::KIND_COD );
			$COD->setTitle( Tr::_('COD', [], Payment_Method::getManageModuleName()) );

			$online_payment = new Payment_Kind();
			$online_payment->setCode( static::KIND_ONLINE_PAYMENT );
			$online_payment->setTitle( Tr::_('Online payment', [], Payment_Method::getManageModuleName()) );
			$online_payment->setModuleIsRequired( true );

			$cash = new Payment_Kind();
			$cash->setCode( static::KIND_CASH );
			$cash->setTitle( Tr::_('Cash', [], Payment_Method::getManageModuleName()) );

			$loan = new Payment_Kind();
			$loan->setCode( static::KIND_LOAN );
			$loan->setTitle( Tr::_('Loan', [], Payment_Method::getManageModuleName()) );

			$loan_online = new Payment_Kind();
			$loan_online->setCode( static::KIND_LOAN_ONLINE );
			$loan_online->setTitle( Tr::_('Loan - online', [], Payment_Method::getManageModuleName()) );
			$loan_online->setModuleIsRequired( true );

			$bank_transfer = new Payment_Kind();
			$bank_transfer->setCode( static::KIND_BANK_TRANSFER );
			$bank_transfer->setTitle( Tr::_('Bank transfer', [], Payment_Method::getManageModuleName()) );
			$bank_transfer->setModuleIsRequired( true );

			
			static::$list[$COD->getCode()] = $COD;
			static::$list[$online_payment->getCode()] = $online_payment;
			static::$list[$cash->getCode()] = $cash;
			static::$list[$loan->getCode()] = $loan;
			static::$list[$loan_online->getCode()] = $loan_online;
			static::$list[$bank_transfer->getCode()] = $loan_online;
		}

		return static::$list;
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
}