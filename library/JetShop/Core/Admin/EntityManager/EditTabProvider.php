<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Admin_EntityManager_EditTabProvider_EditTab;
use JetApplication\EShopEntity_Basic;

interface Core_Admin_EntityManager_EditTabProvider {
	
	/**
	 * @param EShopEntity_Basic $item
	 * @return Admin_EntityManager_EditTabProvider_EditTab[]
	 */
	public function provideEditTabs( EShopEntity_Basic $item ): array;
}