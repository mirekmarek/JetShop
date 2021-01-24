<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_Passive;
use Jet\Tr;

#[DataModel_Definition(
	name: 'parametrization_groups_shop_data',
	database_table_name: 'parametrization_groups_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Parametrization_Group::class
)]
abstract class Core_Parametrization_Group_ShopData extends DataModel_Related_1toN implements Images_ShopDataInterface, CommonEntity_ShopDataInterface {

	use CommonEntity_ShopDataTrait;
	use Images_ShopDataTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $category_id = 0;

	protected Category|null $category = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'parent.id',
		form_field_type: false
	)]
	protected int $group_id = 0;

	protected Parametrization_Group|null $group = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Name:',
		max_len: 255
	)]
	protected string $label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Description:',
		form_field_type: Form::TYPE_WYSIWYG,
		max_len: 65536
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type: false,
		max_len: 255
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type: false,
		max_len: 255
	)]
	protected string $image_pictogram = '';

	public function setParents( Category $category, Parametrization_Group $group ) : void
	{
		$this->category_id = $category->getId();
		$this->category = $category;

		$this->group_id = $group->getId();
		$this->group = $group;
	}

	public function getCategoryId() : int
	{
		return $this->category_id;
	}

	public function setCategoryId( int $category_id ) : void
	{
		$this->category_id = $category_id;
	}

	public function setGroupId( int $groups_id)  : void
	{
		$this->group_id = $groups_id;
	}

	public function getGroupId() : int
	{
		return $this->group_id;
	}

	public function setLabel( string $label ) : void
	{
		$this->label = $label;
	}

	public function getLabel() : string
	{
		return $this->label;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function getImageEntity() : string
	{
		return 'category_pg';
	}

	public function getImageObjectId() : int
	{
		return $this->group_id;
	}

	public static function getImageClasses() : array
	{
		return [
			Category_ShopData::IMG_MAIN => Tr::_('Main image', [], Category::getManageModuleName() ),
			Category_ShopData::IMG_PICTOGRAM => Tr::_('Pictogram image', [], Category::getManageModuleName() ),
		];
	}

	public function setImageMain( string $image_main ) : void
	{
		$this->setImage( Category_ShopData::IMG_MAIN, $image_main);
	}

	public function getImageMain() : string
	{
		return $this->getImage( Category_ShopData::IMG_MAIN );
	}

	public function getImageMainUrl() : string
	{
		return $this->getImageUrl( Category_ShopData::IMG_MAIN );
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Category_ShopData::IMG_MAIN, $max_w, $max_h );
	}

	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->setImage( Category_ShopData::IMG_PICTOGRAM, $image_pictogram );
	}

	public function getImagePictogram() : string
	{
		return $this->getImage( Category_ShopData::IMG_PICTOGRAM );
	}

	public function getImagePictogramUrl() : string
	{
		return $this->getImageUrl( Category_ShopData::IMG_PICTOGRAM );
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Category_ShopData::IMG_PICTOGRAM, $max_w, $max_h );
	}

	public function getPossibleToEditImages() : bool
	{
		if($this->group->isInherited()) {
			return false;
		}

		if($this->category->getEditForm()->getIsReadonly()) {
			return false;
		}

		return true;
	}
}