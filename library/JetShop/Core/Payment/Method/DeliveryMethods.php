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
	#[DataModel_Definition(related_to: 'main.id')]
	protected int $payment_method_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected int $delivery_method_id = 0;

	protected Delivery_Method|null|bool $delivery_method = null;

	public function getArrayKeyValue(): string
	{
		return $this->delivery_method_id;
	}

	public function getPaymentMethodId(): int
	{
		return $this->payment_method_id;
	}

	public function setPaymentMethodId( int $payment_method_id ): void
	{
		$this->payment_method_id = $payment_method_id;
	}

	public function getDeliveryMethodId(): int
	{
		return $this->delivery_method_id;
	}

	public function setDeliveryMethodId( int $delivery_method_id ): void
	{
		$this->delivery_method_id = $delivery_method_id;
		$this->delivery_method = null;
	}
}