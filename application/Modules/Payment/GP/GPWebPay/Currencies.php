<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\GP;


class GPWebPay_Currencies {
	public static function getList() : array
	{
		return [
			'CZK' => 203,
			'EUR' => 978,
			'GBP' => 826,
			'HUF' => 348,
			'PLN' => 985,
			'RUB' => 643,
			'USD' => 840
		];
	}
}