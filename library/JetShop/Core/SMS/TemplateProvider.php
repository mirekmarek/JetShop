<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\SMS_Template;

interface Core_SMS_TemplateProvider {
	
	/**
	 * @return SMS_Template[]
	 */
	public function getSMSTemplates() : array;
	
}