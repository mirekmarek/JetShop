<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\PDF_Template;

interface Core_PDF_TemplateProvider {
	
	/**
	 * @return PDF_Template[]
	 */
	public function getPDFTemplates() : array;
	
}