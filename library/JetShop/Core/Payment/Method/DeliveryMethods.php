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

use JetApplication\Payment_Method;
use JetApplication\Delivery_Method;


/**
 *
 */
#[DataModel_Definition(
	name: 'payment_method_delivery_method',
	database_table_name: 'delivery_methods_payment_methods',
	parent_model_class: Payment_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Payment_Method_DeliveryMethods extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $payment_method_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected string $delivery_method_code = '';

	protected Delivery_Method|null|bool $delivery_method = null;

	public function getArrayKeyValue(): string
	{
		return $this->delivery_method_code;
	}

	public function getPaymentMethodCode(): string
	{
		return $this->payment_method_code;
	}

	public function setPaymentMethodCode( string $payment_method_code ): void
	{
		$this->payment_method_code = $payment_method_code;
	}

	public function getDeliveryMethodCode(): string
	{
		return $this->delivery_method_code;
	}

	public function setDeliveryMethodCode( string $delivery_method_code ): void
	{
		$this->delivery_method_code = $delivery_method_code;
		$this->delivery_method = null;
	}

	public function getDeliveryMethod() : ?Delivery_Method
	{
		if($this->delivery_method===null) {
			$this->delivery_method = Delivery_Method::get($this->delivery_method_code);
			if(!$this->delivery_method) {
				$this->delivery_method = false;
			}
		}

		return $this->delivery_method ? : null;
	}


}