<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication;


interface Payment_Method_Module_HasCalculator
{
	
	public function getCalcUrl( Product_EShopData $product, ?Pricelist $pricelist=null  ) : string;
	public function getCalcDefaultTxt( Product_EShopData $product, ?Pricelist $pricelist=null  ) : string;
	public function renderCalcJavaScript( Product_EShopData $product, ?Pricelist $pricelist=null  ) : string;
}