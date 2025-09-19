<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;

use Jet\Form_Field_Input;
use Jet\MVC;
use Jet\MVC_Cache;
use JetApplication\Application_Service_Admin_Content_InfoPages;
use JetApplication\Content_InfoPage_EShopData;
use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'content_info_page',
	database_table_name: 'content_info_page',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Info page',
	admin_manager_interface: Application_Service_Admin_Content_InfoPages::class,
	separate_tab_form_shop_data: true
)]
abstract class Core_Content_InfoPage extends EShopEntity_WithEShopData implements EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_Admin_WithEShopData_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Page id:',
		is_required: true,
		validation_regexp: '/^[a-zA-Z0-9\-]{2,}$/i',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY          => 'Please enter valid page id',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter valid page id',
			'page_id_is_not_unique'               => 'Page with the identifier already exists',
		]
	)]
	protected string $page_id = '';
	
	/**
	 * @var Content_InfoPage_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_InfoPage_EShopData::class
	)]
	protected array $eshop_data = [];
	
	
	
	public function afterAdd(): void
	{
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->publish();
		}
		
		parent::afterAdd();
		
		MVC_Cache::reset();
	}
	
	public function afterUpdate(): void
	{
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->publish();
		}
		
		parent::afterUpdate();
		
		MVC_Cache::reset();
	}
	
	public function afterDelete(): void
	{
		parent::afterDelete();
		MVC_Cache::reset();
	}
	
	
	
	
	public function setPageId( string $value ): void
	{
		$this->page_id = $value;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->setPageId( $value );
		}
	}
	
	public function getPageId(): string
	{
		return $this->page_id;
	}
	
	public function getEshopData( ?EShop $eshop = null ): Content_InfoPage_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	public function publish() : void
	{
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData( $eshop )->publish();
		}
	}
	
	public function activate(): void
	{
		parent::activate();
		$this->publish();
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		$this->publish();
	}
	
	public function activateCompletely(): void
	{
		parent::activateCompletely();
		$this->publish();
	}
	
	public function activateEShopData( EShop $eshop ): void
	{
		parent::activateEShopData( $eshop );
		$this->publish();
	}
	
	public function deactivateEShopData( EShop $eshop ): void
	{
		parent::deactivateEShopData( $eshop );
		$this->publish();
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$form->field('page_id')->setIsReadonly( true );
		
		foreach( EShops::getList() as $eshop) {
			
			$form->field('/eshop_data/'.$eshop->getKey().'/relative_path_fragment')
				->setValidator( function( Form_Field_Input $field ) use ($eshop) {
					return $this->_pathFragmentValidator( $field, $eshop );
				} );
			
		}
		
	}
	
	protected function setupAddForm( Form $form ): void
	{
		
		$form->field('page_id')->setValidator(function( $page_id_field ) {
			$page_id = $page_id_field->getValue();
			
			foreach( EShops::getList() as $eshop) {
				if( MVC::getPage( $page_id, $eshop->getLocale(), $eshop->getBaseId() ) ) {
					$page_id_field->setError( 'page_id_is_not_unique' );
					
					return false;
				}
			}
			
			return true;
		});
		
		
		foreach( EShops::getList() as $eshop) {
			
			$form->field('/eshop_data/'.$eshop->getKey().'/relative_path_fragment')
				->setValidator( function( Form_Field_Input $field ) use ($eshop) {
					return $this->_pathFragmentValidator( $field, $eshop );
				} );
			
		}
	}
	
	public function _pathFragmentValidator( Form_Field_Input $field, EShop $eshop  ) : bool
	{
		$value = $field->getValue();
		
		$value = Data_Text::removeAccents( $value );
		$value = strtolower( $value );
		
		$value = str_replace( ' ', '-', $value );
		$value = preg_replace( '/[^a-z0-9-]/i', '', $value );
		$value = preg_replace( '~(-{2,})~', '-', $value );
		
		$field->setValue( $value );
		
		
		if( !$value ) {
			$field->setError( Form_Field::ERROR_CODE_EMPTY );
			return false;
		}
		
		$parent = MVC::getBase($eshop->getBaseId())->getHomepage($eshop->getLocale());
		
		
		foreach( $parent->getChildren() as $ch ) {
			if( $ch->getId() == $this->getPageId() ) {
				continue;
			}
			
			if( $ch->getRelativePathFragment() == $value ) {
				$field->setError('uri_is_not_unique', [
					'page' => $ch->getName()
				]);
				
				return false;
			}
		}
		
		return true;
	}
	
}