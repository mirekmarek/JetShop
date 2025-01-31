<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Content_MagicTag;

interface Core_EShop_Managers_MagicTags
{
	/**
	 * @return Content_MagicTag[]
	 */
	public function getList() : array;
	
	
	public function init() : void;
	
}