<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;

abstract class Core_Admin_Managers_Category extends Admin_EntityManager_Module
{
	abstract public function renderSelectWidget( string $on_select,
	                                            int $selected_category_id=0,
	                                            ?bool $only_active_filter=null,
	                                            string $name='select_category' ) : string;
}