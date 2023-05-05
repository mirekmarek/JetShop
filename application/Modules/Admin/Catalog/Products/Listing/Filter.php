<?php

namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Data_Listing_Filter;
use Jet\MVC_View;
use JetApplication\Product;

abstract class Listing_Filter extends Data_Listing_Filter
{
	const CATEGORIES = 'categories';
	const IS_ACTIVE = 'is_active';
	const PRODUCT_KIND = 'product_kind';
	const PRODUCT_TYPE = 'product_type';
	const SEARCH = 'search';
	
	abstract public function getKey(): string;
	
	public function renderForm() : string
	{
		$view = new MVC_View( Product::getManageModule()->getViewsDir() );
		$view->setVar('filter', $this );
		return $view->render('listing/filters/'.$this->getKey());
	}
	
	public function getListing() : Listing
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->listing;
	}
	
}