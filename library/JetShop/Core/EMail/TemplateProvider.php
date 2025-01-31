<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EMail_Template;

interface Core_EMail_TemplateProvider {
	
	/**
	 * @return EMail_Template[]
	 */
	public function getEMailTemplates() : array;
	
}