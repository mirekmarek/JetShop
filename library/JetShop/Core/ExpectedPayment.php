<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use JetApplication\Context_HasContext_Interface;
use JetApplication\Context_HasContext_Trait;
use JetApplication\EShop;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\EShopEntity_HasOrderContext_Interface;
use JetApplication\EShopEntity_HasOrderContext_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_WithEShopRelation;

#[DataModel_Definition(
	name: 'expected_payment',
	database_table_name: 'expected_payment',
)]
abstract class Core_ExpectedPayment extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasNumberSeries_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface,
	EShopEntity_HasOrderContext_Interface,
	Context_HasContext_Interface,
	EShopEntity_Admin_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_HasEvents_Trait;
	use EShopEntity_HasOrderContext_Trait;
	use Context_HasContext_Trait;
	use EShopEntity_Admin_Trait;
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return $this->getEshop();
	}
	
	public static function getStatusList(): array
	{
		// TODO: Implement getStatusList() method.
		return [];
	}
	
	public static function getNumberSeriesEntityTitle(): string
	{
		return 'Expected payment';
	}
	
	public function createEvent( EShopEntity_Event $event ): EShopEntity_Event
	{
		// TODO: Implement createEvent() method.
		return null;
	}
	
	public function getHistory(): array
	{
		// TODO: Implement getHistory() method.
		return [];
	}
	
	public function getAdminTitle(): string
	{
		return $this->getNumber();
	}
	
	public static function getNumberSeriesEntityIsPerShop(): bool
	{
		return true;
	}
	
}