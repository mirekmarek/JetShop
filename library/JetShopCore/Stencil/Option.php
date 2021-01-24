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

	protected ?Stencil $stencil = null;

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

	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Stencil_Option_ShopData::class
	)]
	protected $shop_data;

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
		foreach( Shops::getList() as $shop ) {
			$shop_id = $shop->getId();

			if(!isset($this->shop_data[$shop_id])) {

				$sh = new Stencil_Option_ShopData();
				$sh->setShopId($shop_id);

				$this->shop_data[$shop_id] = $sh;
			}

		}
	}

	public function setParents( Stencil $stencil ) : void
	{
		$this->stencil = $stencil;
		$this->stencil_id = $stencil->getId();

		foreach($this->shop_data as $shop_data) {
			$shop_data->setParents( $stencil, $this );
		}

	}

	public function isInherited() : bool
	{
		return true;
	}

	public function getStencilIs() : int
	{
		return $this->stencil_id;
	}

	public function getStencil() : Stencil
	{
		return $this->stencil;
	}


	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getArrayKeyValue() : int
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

	public function getShopData( string|null $shop_id=null ) : Stencil_Option_ShopData
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return $this->shop_data[$shop_id];
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

	public function getFilterLabel( string|null $shop_id=null ) : string
	{
		return $this->getShopData( $shop_id )->getFilterLabel();
	}

	public function getProductDetailLabel( string|null $shop_id=null ) : string
	{
		return $this->getShopData( $shop_id )->getProductDetailLabel();
	}

	public function getUrlParam( string|null $shop_id=null ) : string
	{
		return $this->getShopData( $shop_id )->getUrlParam();
	}

	public function getDescription( string|null $shop_id=null ) : string
	{
		return $this->getShopData( $shop_id )->getDescription();
	}


}