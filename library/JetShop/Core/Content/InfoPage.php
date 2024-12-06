<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\Content_InfoPage_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShops;
use JetApplication\EShop;



#[DataModel_Definition(
	name: 'content_info_page',
	database_table_name: 'content_info_page',
)]
abstract class Core_Content_InfoPage extends Entity_WithEShopData
{
	
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
	}
	
	public function afterUpdate(): void
	{
		foreach( EShops::getList() as $eshop ) {
			$this->getEshopData( $eshop )->publish();
		}
		
		parent::afterUpdate();
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
	
}