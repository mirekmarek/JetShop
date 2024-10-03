<?php
/**
 *
 */

namespace JetShop;

use JetApplication\EMail_Template;

interface Core_EMail_TemplateProvider {
	
	/**
	 * @return EMail_Template[]
	 */
	public function getEMailTemplates() : array;
	
}