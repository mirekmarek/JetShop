<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing;
use Jet\Data_Listing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetShop\Product_ShopData;
use JetShop\Shops;


class Listing_Filter_IsActive extends Data_Listing_Filter
{
	protected int $is_active_general = 0;
	
	protected array $is_active_per_shop = [];
	
	public function __construct( Data_Listing $listing )
	{
		parent::__construct( $listing );
		foreach(Shops::getList() as $code => $shop) {
			if(!array_key_exists($code, $this->is_active_per_shop)) {
				$this->is_active_per_shop[$code] = '0';
			}
		}
	}
	
	public function catchGetParams(): void
	{
		$this->is_active_general = Http_Request::GET()->getString('is_active_general', '0', ['0', '1', '-1']);
		if($this->is_active_general!='0') {
			$this->listing->setGetParam('is_active_general', $this->is_active_general);
		}
		
		foreach(Shops::getList() as $code => $shop) {
			$this->is_active_per_shop[$code] = Http_Request::GET()->getString('is_active_'.$code, '0', ['0', '1', '-1']);
			
			if($this->is_active_per_shop[$code]!='0') {
				$this->listing->setGetParam('is_active_'.$code, $this->is_active_per_shop[$code]);
			}
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [
			'0' => Tr::_('- all -'),
			'-1' => Tr::_('Not active'),
			'1' => Tr::_('Active'),
		];
		
		$error_messages = [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select option',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select option'
		];
		
		$is_active_general = new Form_Field_Select('is_active_general', 'Is active - general:' );
		$is_active_general->setDefaultValue( $this->is_active_general );
		$is_active_general->setSelectOptions( $options );
		$is_active_general->setErrorMessages($error_messages);
		$form->addField($is_active_general);
		
		foreach(Shops::getList() as $code => $shop) {
			
			$is_active = new Form_Field_Select('is_active_'.$code, 'Is active - '.$shop->getShopName().':');
			$is_active->setDefaultValue( $this->is_active_per_shop[$code] );
			$is_active->setSelectOptions( $options );
			$is_active->setErrorMessages($error_messages);
			$form->addField($is_active);
		}
	}
	
	public function catchForm( Form $form ): void
	{
		$this->is_active_general = $form->field('is_active_general')->getValue();
		if($this->is_active_general!='0') {
			$this->listing->setGetParam('is_active_general', $this->is_active_general);
		} else {
			$this->listing->setGetParam('is_active_general', '');
		}
		
		foreach(Shops::getList() as $code => $shop) {
			$this->is_active_per_shop[$code] = $form->field('is_active_'.$code)->getValue();
			if($this->is_active_per_shop[$code]!='0') {
				$this->listing->setGetParam('is_active_'.$code, $this->is_active_per_shop[$code]);
			} else {
				$this->listing->unsetGetParam('is_active_'.$code);
			}
		}
	}
	
	public function generateWhere(): void
	{
		$where = [];
		
		if($this->is_active_general!='0') {
			$where['is_active'] = ($this->is_active_general=='1');
		}
		
		foreach(Shops::getList() as $code=>$shop) {
			if($this->is_active_per_shop[$code]=='0') {
				continue;
			}
			
			$_ids = Product_ShopData::dataFetchAll(
				select:['product_id'],
				where: [
					$shop->getWhere(),
					'AND',
					'is_active' => ($this->is_active_per_shop[$code]=='1')
				]);
			
			$ids = [];
			foreach($_ids as $id) {
				$ids[] = $id['product_id'];
			}
			
			if(!$ids) {
				$ids = [0];
			}
			
			if($where) {
				$where[] = 'AND';
			}
			
			$where[] = [
				'id' => $ids
			];
		}
		
		if($where) {
			$this->listing->addWhere($where);
		}
	}
	
}