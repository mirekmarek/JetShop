<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\SysConf_URI;
use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Admin_Listing_Column;
use JetApplication\CustomerBlacklist;
use JetApplication\Order;

class Listing_Column_Tags extends Admin_Listing_Column
{
	public const KEY = 'tags';
	
	public function getTitle(): string
	{
		return Tr::_('Tags');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function initializer( UI_dataGrid_column $column ): void
	{
		//$column->addCustomCssStyle('width:300px;');
	}
	
	public function render( mixed $item ) : string
	{
		/**
		 * @var Order $item
		 */
		$tags = [];
		
		$res = [];
		
		/** @noinspection PhpArrayIsAlwaysEmptyInspection */
		foreach( $tags as $icon => $title) {
			$res[] = '<img src="'.SysConf_URI::getImages().'admin/icons/'.$icon.'" title="'.$title.'" />';
		}
		
		
		$res = implode(' ', $res);
		return $res;
	}
}