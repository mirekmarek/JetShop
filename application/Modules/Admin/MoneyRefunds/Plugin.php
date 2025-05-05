<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;

use JetApplication\Admin_EntityManager_EditorPlugin;


abstract class Plugin extends Admin_EntityManager_EditorPlugin {
	protected function currentUserCanEdit(): bool
	{
		return Main::getCurrentUserCanEdit();
	}
}