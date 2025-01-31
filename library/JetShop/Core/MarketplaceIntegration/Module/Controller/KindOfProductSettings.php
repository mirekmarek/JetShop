<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
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
use JetApplication\MarketplaceIntegration_Join_KindOfProduct;
use JetApplication\MarketplaceIntegration_MarketplaceCategory;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\MarketplaceIntegration_Module_Controller_KindOfProductSettings;
use JetApplication\EShop;

/**
 *
 */
abstract class Core_MarketplaceIntegration_Module_Controller_KindOfProductSettings extends MVC_Controller_Default
{
	/**
	 * @var MarketplaceIntegration_MarketplaceCategory[]
	 */
	protected array $marketplace_categories;
	
	protected KindOfProduct $kind_of_product;
	
	protected EShop $eshop;
	
	protected MarketplaceIntegration_Module $marketplace;
	
	protected MarketplaceIntegration_Join_KindOfProduct $category_id_join;
	
	protected ?MarketplaceIntegration_MarketplaceCategory $selected_marketplace_category;
	
	protected Form $category_form;
	
	public function init(
		KindOfProduct                 $kind_of_product,
		EShop                         $eshop,
		MarketplaceIntegration_Module $marketplace
	): void
	{
		$this->kind_of_product = $kind_of_product;
		$this->eshop = $eshop;
		$this->marketplace = $marketplace;
		$this->marketplace_categories = $this->marketplace->getCategories( $this->eshop );
		$this->selected_marketplace_category = null;
		
		$this->category_id_join = MarketplaceIntegration_Join_KindOfProduct::get(
			$this->marketplace->getCode(),
			$this->eshop,
			$this->kind_of_product->getId()
		);
		
		$this->selected_marketplace_category = $this->marketplace_categories[$this->category_id_join->toString()] ?? null;
	}
	
	/**
	 * @return MarketplaceIntegration_MarketplaceCategory[]
	 */
	public function getMarketplaceCategories(): array
	{
		return $this->marketplace_categories;
	}
	
	public function getKindOfProduct(): KindOfProduct
	{
		return $this->kind_of_product;
	}
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function getMarketplace(): MarketplaceIntegration_Module
	{
		return $this->marketplace;
	}
	
	public function getCategoryIdJoin(): MarketplaceIntegration_Join_KindOfProduct
	{
		return $this->category_id_join;
	}
	
	public function getSelectedMarketplaceCategory(): ?MarketplaceIntegration_MarketplaceCategory
	{
		return $this->selected_marketplace_category;
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
		/**
		 * @var MarketplaceIntegration_Module_Controller_KindOfProductSettings $this
		 */
		AJAX::snippetResponse(
			Admin_Managers::KindOfProduct()->renderMarketPlaceIntegrationCategories(
				$this,
				Http_Request::GET()->getString('category')
			)
		);
	}
	
	public function actualize_list_of_categories_Action() : void
	{
		$this->marketplace->actualizeCategories( $this->eshop );
		Http_Headers::reload(unset_GET_params: ['actualize_list_of_categories']);
	}
	
	public function actualize_list_of_parameters_Action() : void
	{
		$this->marketplace->actualizeCategory( $this->eshop, $this->category_id_join );
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
			
			if(!isset( $this->marketplace_categories[$value])) {
				$field->setError('unknown_category');
				return false;
			}
			
			return true;
		});
		$category_field->setFieldValueCatcher(function($value) {
			$this->category_id_join->setMarketplaceCategoryId($value);
		});
		
		$this->category_form = new Form('cate_settings', [$category_field]);
		
		if($this->category_form->catch()) {
			$this->category_id_join->save();
			UI_messages::success(Tr::_('Saved ...'));
			Http_Headers::reload(set_GET_params: ['actualize_list_of_parameters'=>1]);
		}
		
		
		$this->view->setVar('category_form', $this->category_form);
		
		
		/**
		 * @var MarketplaceIntegration_Module_Controller_KindOfProductSettings $this
		 */
		echo Admin_Managers::KindOfProduct()->renderMarketPlaceIntegrationForm( $this );
	}
	
	
}