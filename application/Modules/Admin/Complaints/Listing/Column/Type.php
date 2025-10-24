<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\Complaint;

class Listing_Column_Type extends Admin_Listing_Column
{
	public const KEY = 'complaint_type_code';
	
	public function getTitle(): string
	{
		return Tr::_('Type');
	}
	
	public function getExportHeader(): string
	{
		return $this->getTitle();
	}
	
	public function getExportData( mixed $item ): string
	{
		/**
		 * @var Complaint $item
		 */
		return $item->getComplaintType()->getTitle();
	}
	
	public function render( mixed $item ): string
	{
		/**
		 * @var Complaint $item
		 */
		
		return $item->getComplaintType()?->getTitle()??'';
	}
	
}