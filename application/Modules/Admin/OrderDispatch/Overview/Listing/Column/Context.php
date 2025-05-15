<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Tr;
use JetApplication\Admin_Listing_Column;
use JetApplication\OrderDispatch;

class Listing_Column_Context extends Admin_Listing_Column
{
	public const KEY = 'context';
	
	public function getTitle(): string
	{
		return Tr::_('Context');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	
	public function getExportHeader(): array
	{
		return [
			'context_type' => Tr::_('Context type'),
			'context_number' => Tr::_('Context number'),
		];
	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var OrderDispatch $item
		 */
		$context = $item->getContext();
		
		return [
			'context_type' => $item->getContextType(),
			'context_number' => $item->getContextNumber()
		];
		
	}
	
}