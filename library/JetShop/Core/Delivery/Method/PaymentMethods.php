<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Delivery_Method;
use JetApplication\Payment_Method;


/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method_payment_method',
	database_table_name: 'delivery_methods_payment_methods',
	parent_model_class: Delivery_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Method_PaymentMethods extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $delivery_method_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected string $payment_method_code = '';

	protected Payment_Method|null|bool $payment_method = null;

	public function getArrayKeyValue(): string
	{
		return $this->payment_method_code;
	}

	public function getDeliveryMethodCode(): string
	{
		return $this->delivery_method_code;
	}

	public function setDeliveryMethodCode( string $delivery_method_code ): void
	{
		$this->delivery_method_code = $delivery_method_code;
	}

	public function getPaymentMethodCode(): string
	{
		return $this->payment_method_code;
	}

	public function setPaymentMethodCode( string $payment_method_code ): void
	{
		$this->payment_method_code = $payment_method_code;
		$this->payment_method = null;
	}

	public function getPaymentMethod() : ?Payment_Method
	{
		if($this->payment_method===null) {
			$this->payment_method = Payment_Method::get($this->payment_method_code);
			if(!$this->payment_method) {
				$this->payment_method = false;
			}
		}

		return $this->payment_method ? : null;
	}

}