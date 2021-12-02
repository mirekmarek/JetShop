<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form;
use Jet\DataModel;
use Jet\DataModel_Related_1toN;
use Jet\Form_Field_Input;
use Jet\Tr;

#[DataModel_Definition(
	name: 'payment_methods_options',
	database_table_name: 'payment_methods_options',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Core_Payment_Method::class
)]
abstract class Core_Payment_Method_Option extends DataModel_Related_1toN {


	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_id: true,
		max_len: 255,
		form_field_type: Form::TYPE_INPUT,
		form_field_label: 'Code:',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter code'
		]
	)]
	protected string $code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true,
		form_field_type: false,
		related_to: 'main.code'
	)]
	protected string $payment_method_code = '';

	/**
	 * @var Payment_Method_Option_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_Option_ShopData::class
	)]
	protected array $shop_data = [];


	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;


	public function __construct() {

		parent::__construct();

		$this->afterLoad();
	}

	public function afterLoad() : void
	{
		Payment_Method_Option_ShopData::checkShopData( $this, $this->shop_data );
	}


	public function isInherited() : bool
	{
		return true;
	}

	public function getPaymentMethodCode() : int
	{
		return $this->payment_method_code;
	}


	public function getCode() : string
	{
		return $this->code;
	}

	public function setCode( string $code ) : void
	{
		$this->code = $code;
	}

	public function getArrayKeyValue() : string
	{
		return $this->code;
	}

	public function getShopData( ?Shops_Shop $shop=null ) : Payment_Method_Option_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$form = $this->getCommonForm('option_add_form');

			$form->field('code')->setValidator(function( Form_Field_Input $field ) {
				if(!$field->checkValueIsNotEmpty()) {
					return false;
				}

				$code = $field->getValue();


				foreach( Payment_Method::get($this->payment_method_code)->getOptions() as $option ) {
					if($option->getCode()==$code) {
						$field->setCustomError(Tr::_('This code is already used'));
						return false;
					}
				}

				return true;
			});
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
			$form = $this->getCommonForm('option_edit_form_'.$this->code);

			$form->field('code')->setIsReadonly(true);

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

	public function getDescription( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getDescription();
	}

	public function getTitle( ?Shops_Shop $shop=null ) : string
	{
		return $this->getShopData( $shop )->getTitle();
	}


}