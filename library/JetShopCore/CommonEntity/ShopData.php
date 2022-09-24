<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Definition;
use Jet\MVC;
use Jet\MVC_View;


/**
 *
 */
#[DataModel_Definition(
	database_table_name: 'category_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_CommonEntity_ShopData extends DataModel_Related_1toN
{
	use CommonEntity_ShopRelationTrait_ShopIsId;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active'
	)]
	protected bool $is_active = true;


	public static function checkShopData( object $parent, array &$property ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$key = $shop->getKey();

			if(!isset($property[$key])) {

				$sh = new static();

				$property[$key] = $sh;
			}

			$property[$key]->setShop( $shop );
		}

	}

	public function getArrayKeyValue() : string
	{
		return $this->shop_code.'_'.$this->locale;
	}


	public function isActive() : bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function renderShopDataBlock_start() : string
	{
		$view = new MVC_View( MVC::getBase()->getViewsPath() );

		$view->setVar('shop', $this->getShop() );

		return $view->render('shop-data-block/start');

	}

	public function renderShopDataBlock_end() : string
	{
		$view = new MVC_View( MVC::getBase()->getViewsPath() );

		$view->setVar('shop', $this->getShop() );

		return $view->render('shop-data-block/end');

	}

	public function createForm( string $form_name, array $only_fields=[], array $exclude_fields=[]   ) : Form
	{
		if(!Shops::exists( $this->getShopKey() )) {
			return new Form($form_name, []);
		}

		return parent::createForm( $form_name, $only_fields, $exclude_fields );
	}
}