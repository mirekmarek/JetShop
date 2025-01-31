<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_EShopData;

interface Core_EShop_Managers_ProductQuestions {
	
	public function renderQuestions( Product_EShopData $product ): string;
	
}