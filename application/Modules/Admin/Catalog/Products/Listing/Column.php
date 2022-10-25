<?php

namespace JetShopModule\Admin\Catalog\Products;

use Jet\BaseObject;
use Jet\MVC_View;
use Jet\UI_dataGrid_column;
use JetShop\Product;

abstract class Listing_Column extends BaseObject
{
	protected Listing $listing;
	
	public function __construct( Listing $listing )
	{
		$this->listing = $listing;
	}
	
	abstract public static function getKey(): string;
	
	abstract public static function getTitle(): string;
	
	public function isMandatory() : bool
	{
		return false;
	}
	
	public function getDisallowSort(): bool
	{
		return false;
	}
	
	public function getOrderByAsc(): array|string
	{
		return '+'.$this->getKey();
	}
	
	public function getOrderByDesc(): array|string
	{
		return '-'.$this->getKey();
	}
	
	
	public function getAsGridColumnDefinition() : array
	{
		return [
			'title' => $this->getTitle(),
			'disallow_sort' => $this->getDisallowSort(),
		];
	}
	
	public function render( Product $item ) : string
	{
		$view = new MVC_View( Product::getManageModule()->getViewsDir() );
		$view->setVar('item', $item);
		$view->setVar('listing', $this->listing );
		return $view->render('listing/grid/columns/'.$this->getKey());
	}
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
	}
	
	public function getExportHeader() : null|string|array
	{
		return null;
	}
	
	public function getExportData( Product $item ) : float|int|bool|string|array
	{
		return '';
	}
	
}