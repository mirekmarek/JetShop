<?php
/**
 *
 */

namespace JetShop;


use Jet\Tr;

abstract class Core_Payment_Kind {

	protected string $code = '';

	protected string $title = '';


	/**
	 * @var Payment_Kind[]|null
	 */
	protected static ?array $list = null;

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode( string $code ): void
	{
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle( string $title ): void
	{
		$this->title = $title;
	}

	/**
	 * @return Payment_Kind[]
	 */
	public static function getList() : array
	{
		if(static::$list===null) {
			static::$list = [];

			$COD = new Payment_Kind();
			$COD->setCode('COD');
			$COD->setTitle( Tr::_('COD', [], Payment_Method::getManageModuleName()) );

			$online_payment = new Payment_Kind();
			$online_payment->setCode('online_payment');
			$online_payment->setTitle( Tr::_('Online payment', [], Payment_Method::getManageModuleName()) );

			$cash = new Payment_Kind();
			$cash->setCode('cash');
			$cash->setTitle( Tr::_('Cash', [], Payment_Method::getManageModuleName()) );

			$loan = new Payment_Kind();
			$loan->setCode('loan');
			$loan->setTitle( Tr::_('Loan', [], Payment_Method::getManageModuleName()) );

			$loan_online = new Payment_Kind();
			$loan_online->setCode('loan_online');
			$loan_online->setTitle( Tr::_('Loan - online', [], Payment_Method::getManageModuleName()) );

			$bank_transfer = new Payment_Kind();
			$bank_transfer->setCode('bank_transfer');
			$bank_transfer->setTitle( Tr::_('Bank transfer', [], Payment_Method::getManageModuleName()) );

			
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

		foreach($list as $kind) {
			$res[$kind->getCode()] = $kind->getTitle();
		}

		return $res;
	}
}