<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Brand;
use JetApplication\Brand_EShopData;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Entity_Admin_WithEShopData_Trait;
use JetApplication\Entity_WithEShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShop_Managers;
use JetApplication\EShop;
use JetApplication\Entity_Definition;

#[DataModel_Definition(
	name: 'brands',
	database_table_name: 'brands',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_Brand::class,
	description_mode: true,
	images: [
		'logo' => 'Logo',
		'big_logo' => 'Big logo',
		'title' => 'Title image',
	]
)]
abstract class Core_Brand extends Entity_WithEShopData implements
	FulltextSearch_IndexDataProvider,
	Entity_Admin_WithEShopData_Interface
{
	use Entity_Admin_WithEShopData_Trait;
	
	/**
	 * @var Brand_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Brand_EShopData::class
	)]
	protected array $eshop_data = [];
	
	public function getEshopData( ?EShop $eshop=null ) : Brand_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	
	public function getFulltextObjectType(): string
	{
		return '';
	}
	
	public function getFulltextObjectIsActive(): bool
	{
		return $this->isActive();
	}
	
	public function getInternalFulltextObjectTitle(): string
	{
		return $this->getAdminTitle();
	}
	
	public function getInternalFulltextTexts(): array
	{
		return [$this->getInternalName(), $this->getInternalCode()];
	}
	
	public function getShopFulltextTexts( EShop $eshop ): array
	{
		$sd = $this->getEshopData( $eshop );
		
		return [$sd->getName()];
	}
	
	public function updateFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->updateIndex( $this );
		EShop_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function removeFulltextSearchIndex() : void
	{
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
		EShop_Managers::FulltextSearch()->deleteIndex( $this );
	}
	
}