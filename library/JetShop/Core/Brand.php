<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Brand_EShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShop_Managers;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'brands',
	database_table_name: 'brands',
)]
abstract class Core_Brand extends Entity_WithEShopData implements FulltextSearch_IndexDataProvider, Admin_Entity_WithEShopData_Interface
{
	use Admin_Entity_WithEShopData_Trait;
	
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
	
	public function getEditURL() : string
	{
		return Admin_Managers::Brand()->getEditURL( $this->id );
	}
	
	public function getDescriptionMode() : bool
	{
		return true;
	}
	
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'logo',
			image_title:  Tr::_('Logo'),
		);
		$this->defineImage(
			image_class:  'big_logo',
			image_title:  Tr::_('Big logo'),
		);
		$this->defineImage(
			image_class:  'title',
			image_title:  Tr::_('Title image'),
		);
	}
	
}