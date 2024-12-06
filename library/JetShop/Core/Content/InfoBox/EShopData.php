<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithEShopData_EShopData;
use JetApplication\Content_InfoBox;


#[DataModel_Definition(
	name: 'content_info_box_eshop_data',
	database_table_name: 'content_info_box_eshop_data',
	parent_model_class: Content_InfoBox::class
)]
abstract class Core_Content_InfoBox_EShopData extends Entity_WithEShopData_EShopData
{
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Text:'
	)]
	protected string $text = '';
	
	
	public function setText( string $value ) : void
	{
		$this->text = $value;
	}
	
	public function getText() : string
	{
		return $this->text;
	}
}