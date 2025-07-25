<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ProductFilterManager;


use Jet\AJAX;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Tr;
use JetApplication\Admin_Managers_ProductFilter;
use JetApplication\Category;
use JetApplication\KindOfProduct;
use JetApplication\ProductFilter;
use JetApplication\Property;


class Main extends Admin_Managers_ProductFilter
{
	protected ?Category $category = null;
	protected ?Form $form = null;
	protected ?ProductFilter $filter = null;
	protected array $properties = [];
	
	protected function initView() : MVC_View
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		return $view;
	}
	
	public function init( ProductFilter $filter ) : Form
	{
		return Tr::setCurrentDictionaryTemporary(dictionary: $this->module_manifest->getName(), action: function() use ($filter) {
			$GET = Http_Request::GET();
			$POST = Http_Request::POST();
			
			$this->form = new Form('product_filter_form', []);
			
			$this->filter = $filter;

			
			if($GET->exists('filter_kind_of_product_selected')) {
				$this->filter->getBasicFilter()->setKindOfProductId( $GET->getInt('filter_kind_of_product_selected') );
			}
			
			if($POST->getInt('/basic/kind_of_product_id')) {
				$this->filter->getBasicFilter()->setKindOfProductId( $POST->getInt('/basic/kind_of_product_id') );
			}
			
			
			$this->getFilterForm_Basic();
			$this->getFilterForm_Properties();
			$this->getFilterForm_Brands();
			$this->getFilterForm_Categories();
			
			if($GET->exists('filter_brands_render_selected')) {
				$view = $this->initView();
				$view->setVar('brand_ids', $GET->getString('filter_brands_render_selected'));
				
				AJAX::snippetResponse( $view->render('form/brands/selected') );
			}
			
			if($GET->exists('filter_categories_render_selected')) {
				$view = $this->initView();
				$view->setVar('category_ids', $GET->getString('filter_categories_render_selected'));
				
				AJAX::snippetResponse( $view->render('form/categories/selected') );
			}
			
			if($GET->exists('filter_kind_of_product_selected')) {
				$view = $this->initView();
				$view->setVar('properties', $this->properties);
				$view->setVar('form', $this->form);
				$view->setVar('editable', !$this->form->getIsReadonly());
				
				AJAX::snippetResponse( $view->render('form/properties') );
			}
			
			
			return $this->form;
		});
	}
	
	
	protected function getFilterForm_Basic() : void
	{
		$filter = $this->filter->getBasicFilter();
		
		foreach($filter->getSubFilters() as $sub_filter) {
			$field = $sub_filter->getEditField();
			if(!$field) {
				continue;
			}
			
			$field->setName('/basic/'.$field->getName());
			$this->form->addField( $field );
		}
		
	}
	
	protected function getFilterForm_Properties() : void
	{
		$kind_of_product_id = $this->filter->getBasicFilter()->getKindOfProductId();
		if(
			$kind_of_product_id &&
			($kind_of_product = KindOfProduct::load( $kind_of_product_id ))
		) {
			$properties = $kind_of_product->getProperties();
			foreach($properties as $kof_property) {
				$property = Property::load( $kof_property->getPropertyId() );
				if(!$property) {
					continue;
				}
				$this->properties[$property->getId()] = $property;
				
				$_form = $property->getProductFilterEditForm( $this->filter );
				if($_form) {
					foreach($_form->getFields() as $field) {
						$this->form->addField( $field );
					}
				}
			}
		}
	}
	
	protected function getFilterForm_Brands() : void
	{
		$filter = $this->filter->getBrandsFilter();
		
		$brands = new Form_Field_Hidden('/brand/selected_brands', '');
		$brands->setDefaultValue( implode(',', $filter->getSelectedBrandIds()) );
		$brands->setFieldValueCatcher(function( $value ) use ($filter) {
			if(!$value) {
				$filter->setSelectedBrands([]);
			} else {
				$filter->setSelectedBrands(explode(',', $value));
			}
		});
		$this->form->addField( $brands );
		
	}
	
	public function getFilterForm_Categories() : void
	{
		
		$filter = $this->filter->getCategoriesFilter();
		
		$categories = new Form_Field_Hidden('/categories/selected_categories', '');
		$categories->setDefaultValue( implode(',', $filter->getCategoryIds()) );
		$categories->setFieldValueCatcher(function( $value ) use ($filter) {
			if(!$value) {
				$filter->setCategoryIds([]);
			} else {
				$filter->setCategoryIds(explode(',', $value));
			}
		});
		$this->form->addField( $categories );
		
		$branch_mode = new Form_Field_Checkbox('/categories/branch_mode', 'Include subcategories');
		$branch_mode->setDefaultValue( $filter->getBranchMode() );
		$branch_mode->setFieldValueCatcher(function( $value ) use ($filter) {
			$filter->setBranchMode( (bool)$value );
		});

		$this->form->addField( $branch_mode );
	}
	
	
	
	
	public function renderFilterForm() : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				$view = $this->initView();
				$view->setVar('properties', $this->properties);
				$view->setVar('form', $this->form);
				$view->setVar('editable', !$this->form->getIsReadonly());
				
				return $view->render('filter-form');
			}
		);
	}
	
	public function handleFilterForm(): bool
	{
		if($this->form->catch()) {
			$this->filter->save();
			return true;
		}
		
		return false;
	}

	
	public function getFilter() : ProductFilter
	{
		return $this->filter;
	}
	
}