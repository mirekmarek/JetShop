<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_Managers;
use JetApplication\KindOfProduct;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Exports_ExportCategory;
use JetApplication\Exports_Module;
use JetApplication\EShop;

/**
 *
 */
abstract class Core_Exports_Module_Controller_KindOfProductSettings extends MVC_Controller_Default
{
	/**
	 * @var Exports_ExportCategory[]
	 */
	protected array $export_categories;
	
	protected KindOfProduct $kind_of_product;
	
	protected EShop $eshop;
	
	protected Exports_Module $export;
	
	protected Exports_Join_KindOfProduct $category_id_join;
	
	protected ?Exports_ExportCategory $selected_export_category;
	
	protected Form $category_form;
	
	public function init(
		KindOfProduct  $kind_of_product,
		EShop          $eshop,
		Exports_Module $marketplace
	): void
	{
		$this->kind_of_product = $kind_of_product;
		$this->eshop = $eshop;
		$this->export = $marketplace;
		$this->export_categories = $this->export->getCategories( $this->eshop );
		$this->selected_export_category = null;
		
		$this->category_id_join = Exports_Join_KindOfProduct::get(
			$this->export->getCode(),
			$this->eshop,
			$this->kind_of_product->getId()
		);
		
		$this->selected_export_category = $this->export_categories[$this->category_id_join->toString()] ?? null;
	}
	
	/**
	 * @return Exports_ExportCategory[]
	 */
	public function getExportCategories(): array
	{
		return $this->export_categories;
	}
	
	public function getKindOfProduct(): KindOfProduct
	{
		return $this->kind_of_product;
	}
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function getExport(): Exports_Module
	{
		return $this->export;
	}
	
	public function getCategoryIdJoin(): Exports_Join_KindOfProduct
	{
		return $this->category_id_join;
	}
	
	public function getSelectedExportCategory(): ?Exports_ExportCategory
	{
		return $this->selected_export_category;
	}
	
	public function getCategoryForm(): Form
	{
		return $this->category_form;
	}
	
	
	
	
	public function resolve(): bool|string
	{
		$GET = Http_Request::GET();
		
		if($GET->exists('category')) {
			return 'dialog_eshop_category';
		}
		
		if($GET->exists('actualize_list_of_categories')) {
			return 'actualize_list_of_categories';
		}
		
		if($GET->exists('actualize_list_of_parameters')) {
			return 'actualize_list_of_parameters';
		}
		
		
		return 'default';
	}
	
	public function dialog_eshop_category_Action() : void
	{
		/** @noinspection PhpParamsInspection */
		AJAX::snippetResponse(
			Admin_Managers::KindOfProduct()->renderExportsCategories(
				$this,
				Http_Request::GET()->getString('category')
			)
		);
		
	}
	
	public function actualize_list_of_categories_Action() : void
	{
		$this->export->actualizeCategories( $this->eshop );
		Http_Headers::reload(unset_GET_params: ['actualize_list_of_categories']);
	}
	
	public function actualize_list_of_parameters_Action() : void
	{
		$this->export->actualizeCategory( $this->eshop, $this->category_id_join );
		Http_Headers::reload(unset_GET_params: ['actualize_list_of_parameters']);
	}
	
	public function default_Action() : void
	{
		$category_field = new Form_Field_Input('category', 'Category ID:');
		$category_field->setDefaultValue( $this->category_id_join );
		$category_field->setErrorMessages([
			'unknown_category' => 'Unknown category'
		]);
		$category_field->setValidator(function( Form_Field_Input $field ) {
			$value = $field->getValue();
			
			if(!$value) {
				return true;
			}
			
			if(!isset( $this->export_categories[$value])) {
				$field->setError('unknown_category');
				return false;
			}
			
			return true;
		});
		$category_field->setFieldValueCatcher(function($value) {
			$this->category_id_join->setExportCategoryId($value);
		});
		
		$this->category_form = new Form('cate_settings', [$category_field]);
		
		if($this->category_form->catch()) {
			$this->category_id_join->save();
			UI_messages::success(Tr::_('Saved ...'));
			Http_Headers::reload(set_GET_params: ['actualize_list_of_parameters'=>1]);
		}
		
		
		$this->view->setVar('category_form', $this->category_form);
		
		
		/** @noinspection PhpParamsInspection */
		echo Admin_Managers::KindOfProduct()->renderExportsForm( $this );
	}
	
	
}