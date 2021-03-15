<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'stencils_options_shop_data',
	database_table_name: 'stencils_options_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Stencil_Option::class
)]
abstract class Core_Stencil_Option_ShopData extends DataModel_Related_1toN implements Images_ShopDataInterface, CommonEntity_ShopDataInterface {

	use CommonEntity_ShopDataTrait;
	use Images_ShopDataTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $stencil_id = 0;

	protected ?Stencil $stencil = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'parent.id',
		form_field_type: false
	)]
	protected int $option_id = 0;

	protected ?Stencil_Option $option = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Filter label:',
		max_len: 255
	)]
	protected string $filter_label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Product detail label:',
		max_len: 255
	)]
	protected string $product_detail_label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'URL parameter:',
		max_len: 255
	)]
	protected string $url_param = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Description:',
		max_len: 65536
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
		form_field_label: 'Keywords words for internal fulltext:'
	)]
	protected string $internal_fulltext_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_pictogram = '';

	public function setParents( Stencil $stencil, Stencil_Option $option ) : void
	{
		$this->stencil = $stencil;
		$this->stencil_id = $stencil->getId();

		$this->option = $option;
		$this->option_id = $option->getId();

	}

	public function getArrayKeyValue() : string|int|null
	{
		return $this->shop_code;
	}
	
	public function getShopCode() : string
	{
		return $this->shop_code;
	}

	public function setShopCode( string $shop_code ) : void
	{
		$this->shop_code = $shop_code;
	}

	public function getStencilId() : int
	{
		return $this->stencil_id;
	}

	public function getStencil() : Stencil
	{
		return $this->stencil;
	}

	public function getOptionId() : int
	{
		return $this->option_id;
	}

	public function getOption() : Stencil_Option
	{
		return $this->option;
	}

	public function getFilterLabel() : string
	{
		return $this->filter_label;
	}

	public function setFilterLabel( string $filter_label ) : void
	{
		$this->filter_label = $filter_label;
	}

	public function getProductDetailLabel() : string
	{
		return $this->product_detail_label;
	}

	public function setProductDetailLabel( string $product_detail_label ) : void
	{
		$this->product_detail_label = $product_detail_label;
	}

	public function getUrlParam() : string
	{
		return $this->url_param;
	}

	public function setUrlParam( string $url_param ) : void
	{
		$this->url_param = $url_param;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getInternalFulltextKeywords() : string
	{
		return $this->internal_fulltext_keywords;
	}

	public function setInternalFulltextKeywords( string $internal_fulltext_keywords ) : void
	{
		$this->internal_fulltext_keywords = $internal_fulltext_keywords;
	}

	public function getImageEntity() : string
	{
		return 'stencil_option';
	}

	public function getImageObjectId() : int|string
	{
		return $this->stencil_id;
	}

	public static function getImageClasses() : array
	{
		return [
			Stencil::IMG_MAIN => Tr::_('Main image', [], Stencil::getManageModuleName() ),
			Stencil::IMG_PICTOGRAM => Tr::_('Pictogram image', [], Stencil::getManageModuleName() ),
		];
	}

	public function setImageMain( string $image_main ) : void
	{
		$this->setImage( Stencil::IMG_MAIN, $image_main);
	}

	public function getImageMain() : string
	{
		return $this->getImage( Stencil::IMG_MAIN );
	}

	public function getImageMainUrl() : string
	{
		return $this->getImageUrl( Stencil::IMG_MAIN );
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Stencil::IMG_MAIN, $max_w, $max_h );
	}

	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->setImage( Stencil::IMG_PICTOGRAM, $image_pictogram );
	}

	public function getImagePictogram() : string
	{
		return $this->getImage( Stencil::IMG_PICTOGRAM );
	}

	public function getImagePictogramUrl() : string
	{
		return $this->getImageUrl( Stencil::IMG_PICTOGRAM );
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Stencil::IMG_PICTOGRAM, $max_w, $max_h );
	}

	public function getPossibleToEditImages() : bool
	{
		if($this->option->getEditForm()->getIsReadonly()) {
			return false;
		}

		return true;
	}
}