<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Complaint_ComplaintType;

class Listing_Filter_Type extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'type';
	protected string $label = 'Type';
	
	
	protected function getOptions(): array
	{
		return Complaint_ComplaintType::getScope();
	}
	
	public function generateWhere(): void
	{
		if($this->value) {
			$this->listing->addFilterWhere([
				'complaint_type_code' => $this->value
			]);
		}
	}
}