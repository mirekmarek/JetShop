<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\HomeCredit;


class Exception extends \Exception {
	public const CODE_LOGIN_FAILED = 1;
	public const CODE_CURL_ERROR = 2;
	public const CODE_RESPONSE_ERROR = 3;
	public const CODE_BAD_RESPONSE_NOT_JSON = 4;
	public const CODE_REQUEST_WITHOUT_LOGIN = 5;
	public const CODE_BAD_RESPONSE = 6;
}