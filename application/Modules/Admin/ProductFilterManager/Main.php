<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ProductFilterManager;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Select;
use Jet\MVC_View;
use Jet\Tr;
use JetApplication\Admin_Managers_ProductFilter;
use JetApplication\Brand;
use JetApplication\Category;
use JetApplication\KindOfProduct;
use JetApplication\ProductFilter;
use JetApplication\Property;
use JetApplication\Shops;


class Main extends Application_Module implements Admin_Managers_ProductFilter
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
	
	protected function getCategoryAutoAppendFilterForm() : Form
	{
		if(!$this->form) {
			$this->filter = $this->category->getAutoAppendProductsFilter();
			
			$this->form = new Form('product_auto_append_form', []);
			
			$this->getFilterForm_Basic();
			$this->getFilterForm_Properties();
			$this->getFilterForm_Brands();
			
		}
		
		return $this->form;
	}
	
	protected function getCategoryManualAppendFilterForm() : Form
	{
		if(!$this->form) {
			$this->filter = new ProductFilter( Shops::getDefault() );
			if($this->category->getKindOfProductId()) {
				$this->filter->getBasicFilter()->setKindOfProductId( $this->category->getKindOfProductId() );
			}
			
			$this->form = new Form('product_manual_append_form', []);
			
			$this->getFilterForm_Basic();
			$this->getFilterForm_Properties();
			$this->getFilterForm_Brands();
			
		}
		
		return $this->form;
	}
	
	
	protected function getFilterForm_Basic() : void
	{
		$filter = $this->filter->getBasicFilter();
		
		$inputToFilter = function( $value ) {
			return match ($value) {
				'' => null,
				'0' => false,
				'1' => true
			};
		};
		
		$filterToInput = function( $val ) {
			if($val===null) {
				$val = '';
			} else {
				$val = $val?'1':'0';
			}
			
			return $val;
		};
		
		$in_stock = new Form_Field_Select('/basic/is_in_stock', 'Is in stock:');
		$in_stock->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => ' '
		]);
		$in_stock->setSelectOptions([
			'' => Tr::_('- all -'),
			'1' => Tr::_('In stock'),
			'0' => Tr::_('Not in stock'),
		]);
		
		$in_stock->setDefaultValue( $filterToInput($filter->getInStock()) );
		$in_stock->setFieldValueCatcher( function( $value ) use ($filter, $inputToFilter) {
			$filter->setInStock($inputToFilter($value));
		} );
		
		$this->form->addField( $in_stock );
		
		
		
		$has_discount = new Form_Field_Select('/basic/has_discount', 'Has discount:');
		$has_discount->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => ' '
		]);
		$has_discount->setSelectOptions([
			'' => Tr::_('- all -'),
			'1' => Tr::_('Has discount'),
			'0' => Tr::_('Without discount'),
		]);
		
		$has_discount->setDefaultValue( $filterToInput($filter->getHasDiscount()) );
		$has_discount->setFieldValueCatcher( function( $value ) use ($filter, $inputToFilter) {
			$filter->setHasDiscount($inputToFilter($value));
		} );
		
		$this->form->addField( $has_discount );
		
		
		
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
		$brands = [0=>Tr::_('- all -')]+Brand::getScope();
		$filter = $this->filter->getBrandsFilter();
		
		foreach($brands as $id=>$label) {
			if(!$id) {
				continue;
			}
			
			$checked = new Form_Field_Checkbox('/brand/'.$id.'/checked', $label);
			$checked->setDefaultValue( $filter->getBrandSelected( $id ) );
			$checked->setFieldValueCatcher( function( $value ) use ($filter, $id) {
				if($value) {
					$filter->selectBrand($id);
				} else {
					$filter->unselectBrand($id);
				}
			} );
			
			$this->form->addField( $checked );
		}
	}
	
	public function renderCategoryAutoAppendFilterForm( Category $category, bool $editable ): string
	{
		$this->category = $category;
		
		$form = $this->getCategoryAutoAppendFilterForm();
		
		if(!$editable) {
			$form->setIsReadonly();
		}
		
		
		
		$view = $this->initView();
		
		$view->setVar('category', $category);
		$view->setVar('editable', $editable);
		$view->setVar('properties', $this->properties);
		$view->setVar('form', $form);
		
		
		return $view->render('category-auto-append-filter-form');
	}
	
	public function handleCategoryAutoAppendFilterForm( Category $category ): bool
	{
		$this->category = $category;
		
		if($this->getCategoryAutoAppendFilterForm()->catch()) {
			$this->filter->save();
			return true;
		}
		
		return false;
	}
	
	
	public function renderCategoryManualAppendFilterForm( Category $category ): string
	{
		$this->category = $category;
		
		$form = $this->getCategoryManualAppendFilterForm();
		
		$view = $this->initView();
		
		$view->setVar('category', $category);
		$view->setVar('properties', $this->properties);
		$view->setVar('form', $form);
		
		return $view->render('category-manual-append-filter-form');
	}
	
	public function handleCategoryManualAppendFilterForm( Category $category ): bool
	{
		$this->category = $category;
		
		if(!$this->getCategoryManualAppendFilterForm()->catch()) {
			return false;
		}
		
		$products = $this->filter->filter();
		foreach($products as $product_id) {
			$category->addProduct( $product_id );
		}
		
		return true;
	}
	
}