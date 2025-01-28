<?php
namespace JetApplicationModule\Admin\Catalog\Categories;

use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Category;
use JetApplication\Admin_Managers;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_Category;
use JetApplication\Entity_Basic;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Category
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'categories';

	public const ACTION_GET = 'get_category';
	public const ACTION_ADD = 'add_category';
	public const ACTION_UPDATE = 'update_category';
	public const ACTION_DELETE = 'delete_category';

	
	public function renderSelectWidget( string $on_select,
                                       int $selected_category_id=0,
                                       ?bool $only_active_filter=null,
                                       string $name='select_category' ) : string
	{
		
		$selected = $selected_category_id ? Category::get($selected_category_id) : null;
		
		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select category ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			entity_type: Category::getEntityType(),
			object_type_filter: null,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getPathName(),
			selected_entity_edit_URL: $selected?->getEditUrl()
		);
	}
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Category();
	}

}