<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Categories;


use Jet\AJAX;
use Jet\Factory_MVC;
use Jet\Form_Field_Hidden;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Application_Service_Admin_Category;
use JetApplication\Category;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_Basic;


class Main extends Application_Service_Admin_Category
{
	public const ADMIN_MAIN_PAGE = 'categories';
	
	public function renderSelectWidget( string $on_select,
                                       int $selected_category_id=0,
                                       ?bool $only_active_filter=null,
                                       string $name='select_category' ) : string
	{
		
		$selected = $selected_category_id ? Category::get($selected_category_id) : null;
		
		return Application_Service_Admin::UI()->renderSelectEntityWidget(
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
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Category();
	}
	
	public function renderSelectCategoriesWidget( Form_Field_Hidden $input ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($input) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar('input', $input);
				
				$GET = Http_Request::GET();
				if(
					$GET->exists('show_categories') &&
					$GET->getString('input_id')==$input->getId()
				) {
					$input->setDefaultValue( $GET->getString('show_categories') );
					
					AJAX::snippetResponse(
						$view->render('select-categories/categories')
					);
				}
				
				return $view->render('select-categories/select');
				
			}
		);
	}

}