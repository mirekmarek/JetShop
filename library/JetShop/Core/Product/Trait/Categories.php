<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

trait Core_Product_Trait_Categories
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999
	)]
	protected string $category_ids = '';
	
	public function setCategoryIds( array $value ) : void
	{
		$this->category_ids = implode(',', $value);
	}
	
	public function getCategoryIds() : array
	{
		if(!$this->category_ids) {
			return [];
		}
		
		return explode(',', $this->category_ids);
	}
	
}