<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Supplier_GoodsOrder_Event;

#[DataModel_Definition]
abstract class Core_Supplier_GoodsOrder_Event_SentToSupplier extends Supplier_GoodsOrder_Event {

}