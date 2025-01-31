<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use JetApplication\ProductFilter;

interface Core_Admin_Managers_ProductFilter
{
	public function init( ProductFilter $filter ) : Form;
	
	public function renderFilterForm() : string;
	
	public function handleFilterForm() : bool;
	
	public function getFilter() : ProductFilter;
	
}