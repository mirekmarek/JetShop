<?php
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