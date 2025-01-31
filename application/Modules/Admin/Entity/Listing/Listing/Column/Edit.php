<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\UI_dataGrid_column;

class Listing_Column_Edit extends Listing_Column_Abstract
{
	public const KEY = '_edit_';
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return '';
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	public function isMandatory(): bool
	{
		return true;
	}
	
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
		$column->addCustomCssStyle( 'width:30px;' );
	}
	
}