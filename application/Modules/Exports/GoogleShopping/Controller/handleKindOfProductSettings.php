<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Exports\GoogleShopping;

use Jet\Application;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_Join_Property;
use JetApplication\Exports_Join_Property_Option;
use JetApplication\KindOfProduct;
use JetApplication\Property;
use JetApplication\Shops_Shop;

/**
 *
 */
class Controller_handleKindOfProductSettings extends MVC_Controller_Default
{
	protected KindOfProduct $kind_of_product;

	protected Shops_Shop $shop;

	protected Main $export;

	protected array $categories;

	/**
	 *
	 */
	public function handleKindOfProductSettings_Action() : void
	{
		$this->kind_of_product = $this->content->getParameter('kind_of_product');
		$this->shop = $this->content->getParameter('shop');
		$this->export = $this->content->getParameter('export');

		$this->categories = $this->export->getExportCategories( $this->shop );

		$this->view->setVar('kind_of_product', $this->kind_of_product);
		$this->view->setVar('export_categories', $this->categories);

		$GET = Http_Request::GET();

		if($GET->exists('google_category')) {
			$this->view->setVar('dialog_selected_category', $GET->getString('google_category'));
			ob_end_clean();
			echo $this->view->render('kind_of_product_settings/dialog/categories');
			Application::end();
		}


		$export_category_id = Exports_Join_KindOfProduct::get(
			$this->export->getCode(),
			$this->shop,
			$this->kind_of_product->getId()
		);

		$export_category_field = new Form_Field_Hidden('export_category', 'Google Shopping category ID:' );
		$export_category_field->setDefaultValue( $export_category_id );
		$export_category_field->setErrorMessages([
			'unknown_category' => 'Unknown Google Shopping category'
		]);
		$export_category_field->setValidator(function( Form_Field_Hidden $field ) {
			$value = $field->getValue();

			if(!$value) {
				return true;
			}

			if(!isset($this->categories[$value])) {
				$field->setError('unknown_category');
				return false;
			}

			return true;
		});
		$export_category_field->setFieldValueCatcher(function($value) use ($export_category_id) {
			$export_category_id->setExportCategoryId($value);
		});

		$category_form = new Form('google_cate_settings', [$export_category_field]);

		if($category_form->catch()) {
			$export_category_id->save();
			UI_messages::success(Tr::_('Saved ...'));
			Http_Headers::reload();
		}

		$export_category = $this->categories[$export_category_id->toString()] ?? null;

		$this->view->setVar('category_form', $category_form);
		$this->view->setVar('export_category_id', $export_category_id);
		$this->view->setVar('export_category', $export_category);



		$param_fields = [];
		$glue = [];

		foreach( $this->kind_of_product->getAllPropertyIds() as $p_property ) {

			$p_g = Exports_Join_Property::get(
				$this->export->getCode(),
				$this->shop,
				$p_property->getId()
			);

			$glue[] = $p_g;

			$field = new Form_Field_Input(
				$p_property->getId(),
				$p_property->getShopData()->getLabel()
			);
			$field->setDefaultValue($p_g->toString());
			$field->setFieldValueCatcher(function($value) use ($p_g, $field) {
				$p_g->setExportPropertyId($value);
			});

			$param_fields[] = $field;

			if($p_property->getType()==Property::PROPERTY_TYPE_OPTIONS) {
				foreach($p_property->getOptions() as $p_option) {

					$o_g = Exports_Join_Property_Option::get(
						$this->export->getCode(),
						$this->shop,
						$p_property->getId(),
						$p_option->getId()
					);
					$glue[] = $o_g;


					$field = new Form_Field_Input(
						$p_property->getId().'-o'.$p_option->getId(),
						$p_option->getShopData()->getFilterLabel()
					);
					$field->setDefaultValue($o_g);
					$field->setFieldValueCatcher(function($value) use ($o_g, $field) {
						$o_g->setExportOptionId($value);
					});
					$param_fields[] = $field;

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

		$this->output('kind_of_product_settings/default');
	}
}