<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Payment_Kind;

class Listing_Filter_Kind extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'kind';
	protected string $label = 'Kind of payment method';
	
	
	protected function getOptions(): array
	{
		return Payment_Kind::getScope();
	}
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'kind'   => $this->value,
		]);
	}
}