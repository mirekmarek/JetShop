<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Tr;
use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\KindOfProduct;


class Listing_Filter_ProductKind extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'product_kind';
	protected string $label = 'Kind of product';
	
	
	protected function getOptions() : array
	{
		$_options = KindOfProduct::getScope();
		
		$options =
			['-1' => Tr::_('- Not set -')] +
			$_options;
		
		return $options;
	}
	
	
	public function generateWhere(): void
	{
		if($this->value=='') {
			return;
		}
		
		$id = $this->value;
		
		if($id=='-1') {
			$id = 0;
		}
		
		$this->listing->addFilterWhere([
			'kind_id'   => $id,
		]);
	}
	
}