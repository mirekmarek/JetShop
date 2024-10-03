<?php
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