<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\EMail_Layout;


#[DataModel_Definition(
	name: 'email_layout_shop_data',
	database_table_name: 'email_layout_shop_data',
	parent_model_class: EMail_Layout::class
)]
abstract class Core_EMail_Layout_ShopData extends Entity_WithShopData_ShopData
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Layout HTML:'
	)]
	protected string $layout_html = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Layout TXT:'
	)]
	protected string $layout_txt = '';
	
	
	public function setLayoutHTML( string $value ) : void
	{
		$this->layout_html = $value;
	}
	
	public function getLayoutHTML() : string
	{
		return $this->layout_html;
	}
	
	public function getLayoutTxt(): string
	{
		return $this->layout_txt;
	}
	
	public function setLayoutTxt( string $layout_txt ): void
	{
		$this->layout_txt = $layout_txt;
	}
	
	
	
}