<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductReviews;

use JetShop\Core_Admin_EntityManager_EditorPlugin_ForceStatus;

class Plugin_ForceStatus_Main extends Plugin
{
	public const KEY = 'force_status';
	
	use Core_Admin_EntityManager_EditorPlugin_ForceStatus;
}