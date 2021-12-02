<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;

#[DataModel_Definition(
	name: 'stencils_options',
	database_table_name: 'stencils_options',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	default_order_by: ['priority'],
	parent_model_class: Core_Stencil::class
)]
abstract class Core_Stencil_Option extends DataModel_Related_1toN {

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
		form_field_type: false
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false,
		related_to: 'main.id'
	)]
	protected int $stencil_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Is active'
	)]
	protected bool $is_active = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $priority = 0;

	/**
	 * @var Stencil_Option_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Stencil_Option_ShopData::class
	)]
	protected array $shop_data = [];

	protected bool $is_first = false;

	protected bool $is_last = false;

	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;


	public function __construct() {

		parent::__construct();

		$this->afterLoad();
	}

	public function afterLoad() : void
	{
		Stencil_Option_ShopData::checkShopData( $this, $this->shop_data );
	}

	public function isInherited() : bool
	{
		return true;
	}

	public function getStencilIs() : int
	{
		return $this->stencil_id;
	}


	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getArrayKeyValue() : string
	{
		return $this->id;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function getIsActive() : bool
	{
		return $this->is_active;
	}

	public function setPriority( int $priority ) : void
	{
		$this->priority = $priority;
	}

	public function getPriority() : int
	{
		return $this->priority;
	}

	public function getShopData( ?Shops_Shop $shop=null ) : Stencil_Option_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	public function isFirst() : bool
	{
		return $this->is_first;
	}

	public function setIsFirst( bool $is_first ) : void
	{
		$this->is_first = $is_first;
	}

	public function isLast() : bool
	{
		return $this->is_last;
	}

	public function setIsLast( bool $is_last ) : void
	{
		$this->is_last = $is_last;
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$form = $this->getCommonForm('option_add_form');
			$this->_add_form = $form;
		}

		return $this->_add_form;
	}


	public function catchAddForm() : bool
	{
		$form = $this->getAddForm();

		if(!$form->catchInput() || !$form->validate()) {
			return false;
		}

		$form->catchData();

		return true;
	}

	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$form = $this->getCommonForm('option_edit_form_'.$this->id);

			$this->_edit_form = $form;
		}

		return $this->_edit_form;
	}

	public function catchEditForm() : bool
	{
		$form = $this->getEditForm();

		if(!$form->catchInput() || !$form->validate()) {
			return false;
		}

		$form->catchData();

		return true;
	}

	public function getFilterLabel( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getFilterLabel();
	}

	public function getProductDetailLabel( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getProductDetailLabel();
	}

	public function getUrlParam( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getUrlParam();
	}

	public function getDescription( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getDescription();
	}


}