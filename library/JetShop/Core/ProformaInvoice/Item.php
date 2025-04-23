<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\ProformaInvoice;
use JetApplication\ProformaInvoice_Item_SetItem;
use JetApplication\EShopEntity_AccountingDocument_Item;

#[DataModel_Definition(
	name: 'proforma_invoice_item',
	database_table_name: 'proforma_invoices_items',
	parent_model_class: ProformaInvoice::class
)]
abstract class Core_ProformaInvoice_Item extends EShopEntity_AccountingDocument_Item {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $invoice_id = 0;
	
	
	/**
	 * @var ProformaInvoice_Item_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: ProformaInvoice_Item_SetItem::class
	)]
	protected array $set_items = [];
	
	
	/**
	 * @return ProformaInvoice_Item_SetItem[]
	 */
	public function getSetItems(): array
	{
		return $this->set_items;
	}
	
}