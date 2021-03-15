<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Exports\GoogleShopping;

use Jet\Application;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Mvc_Controller_Default;
use Jet\Tr;
use Jet\UI_messages;
use JetShop\Category;
use JetShop\Exports_Join_Category;
use JetShop\Exports_Join_Property;
use JetShop\Exports_Join_Property_Option;
use JetShop\Parametrization_Property;
use JetShop\Shops_Shop;

/**
 *
 */
class Controller_handleCategorySettings extends Mvc_Controller_Default
{
	protected Category $category;

	protected Shops_Shop $shop;

	protected Main $export;

	protected array $categories;

	/**
	 *
	 */
	public function handleCategorySettings_Action() : void
	{
		$this->category = $this->getParameter('category');
		$this->shop = $this->getParameter('shop');
		$this->export = $this->getParameter('export');

		$this->categories = $this->export->getExportCategories( $this->shop );

		$this->view->setVar('category', $this->category);
		$this->view->setVar('export_categories', $this->categories);

		$GET = Http_Request::GET();

		if($GET->exists('google_category')) {
			$this->view->setVar('dialog_selected_category', $GET->getString('google_category'));
			ob_end_clean();
			echo $this->view->render('category_settings/dialog/categories');
			Application::end();
		}


		$export_category_id = Exports_Join_Category::get(
			$this->export->getCode(),
			$this->shop->getCode(),
			$this->category->getId()
		);

		$export_category_field = new Form_Field_Hidden('export_category', 'Google Shopping category ID:', $export_category_id);
		$export_category_field->setValidator(function( Form_Field_Hidden $field ) {
			$value = $field->getValue();

			if(!$value) {
				return true;
			}

			if(!isset($this->categories[$value])) {
				$field->setCustomError(Tr::_('Unknown Google Shopping category'));
				return false;
			}

			return true;
		});
		$export_category_field->setCatcher(function($value) use ($export_category_id) {
			$export_category_id->setExportCategoryId($value);
		});

		$category_form = new Form('google_cate_settings', [$export_category_field]);

		if($category_form->catch()) {
			$export_category_id->save();
			UI_messages::success(Tr::_('Saved ...'));
			Http_Headers::reload();
		}

		if(isset($this->categories[$export_category_id->toString()])) {
			$export_category = $this->categories[$export_category_id->toString()];
		} else {
			$export_category = null;
		}

		$this->view->setVar('category_form', $category_form);
		$this->view->setVar('export_category_id', $export_category_id);
		$this->view->setVar('export_category', $export_category);



		$param_fields = [];
		$glue = [];

		foreach( $this->category->getParametrizationGroups() as $p_group ) {
			foreach($p_group->getProperties() as $p_property) {
				$p_g = Exports_Join_Property::get(
					$this->export->getCode(),
					$this->shop->getCode(),
					$p_property->getId()
				);

				$glue[] = $p_g;

				$field = new Form_Field_Input(
					'g'.$p_group->getId().'-p'.$p_property->getId(),
					$p_property->getShopData()->getLabel(),
					$p_g->toString()
				);
				$field->setCatcher(function($value) use ($p_g, $field) {
					$p_g->setExportPropertyId($value);
				});

				$param_fields[] = $field;

				if($p_property->getType()==Parametrization_Property::PROPERTY_TYPE_OPTIONS) {
					foreach($p_property->getOptions() as $p_option) {

						$o_g = Exports_Join_Property_Option::get(
							$this->export->getCode(),
							$this->shop->getCode(),
							$p_property->getId(),
							$p_option->getId()
						);
						$glue[] = $o_g;


						$field = new Form_Field_Input(
							'g'.$p_group->getId().'-p'.$p_property->getId().'-o'.$p_option->getId(),
							$p_option->getShopData()->getFilterLabel(),
							$o_g
						);
						$field->setCatcher(function($value) use ($o_g, $field) {
							$o_g->setExportOptionId($value);
						});
						$param_fields[] = $field;

					}
				}
			}
		}

		$param_form = new Form('google_param_settings', $param_fields);

		if($param_form->catch()) {
			foreach($glue as $g) {
				$g->save();
			}

			UI_messages::success(Tr::_('Saved ...'));
			Http_Headers::reload();
		}

		$this->view->setVar('param_form', $param_form);

		$this->output('category_settings/default');
	}
}