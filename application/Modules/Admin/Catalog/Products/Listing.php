<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Search;
use Jet\Form_Field_Select;
use Jet\Http_Request;

use Jet\Tr;
use JetShop\Category;
use JetShop\Fulltext_Index_Internal_Product;
use JetShop\Product;
use JetShop\Product_Category;
use JetShop\Product_ShopData;
use JetShop\Shops;

class Listing extends Data_Listing {

	protected array $grid_columns = [
		'_edit_'     => [
			'title'         => '',
			'disallow_sort' => true
		],
		'id'         => ['title' => 'ID'],
		'products_shop_data.name'       => ['title' => 'Name'],
	];

	protected array $filters = [
		'search',
		'categories',
		'is_active'
	];

	protected string $search = '';

	protected array $categories = [];
	
	protected int $is_active_general = 0;
	
	protected array $is_active_per_shop = [];

	
	protected function getList() : DataModel_Fetch_Instances
	{
		return Product::getList();
	}

	protected function filter_search_catchGetParams() : void
	{
		$this->search = Http_Request::GET()->getString('search');
		$this->setGetParam('search', $this->search);
	}

	public function filter_search_catchForm( Form $form ) : void
	{
		$value = $form->field('search')->getValue();

		$this->search = $value;
		$this->setGetParam('search', $value);
	}

	protected function filter_search_getForm( Form $form ) : void
	{
		$search = new Form_Field_Search('search', '', $this->search);
		$form->addField($search);
	}

	protected function filter_search_getWhere() : void
	{
		if(!$this->search) {
			return;
		}

		$ids = Fulltext_Index_Internal_Product::search(
			search_string: $this->search,
			only_ids: true
		);

		if(!$ids) {
			$ids = [0];
		}


		$this->filter_addWhere([
			'id'   => $ids,
		]);

	}

	/**
	 * @return Category[]
	 */
	public function filter_categories_getSelected() : array
	{
		if(!$this->categories) {
			return [];
		}

		$res = [];

		foreach($this->categories as $id) {
			$category = Category::get($id);
			if($category) {
				$res[$id] = $category;
			}
		}

		return $res;

	}


	protected function filter_categories_set( string $categories )
	{
		if($categories) {
			$this->categories = explode(',', $categories);
			foreach($this->categories as $i=>$id) {
				$this->categories[$i] = (int)$id;
			}

			$this->setGetParam('categories', implode(',', $this->categories));
		} else {
			$this->categories = [];
			$this->setGetParam('categories', '');
		}

	}


	protected function filter_categories_catchGetParams() : void
	{
		$this->filter_categories_set( Http_Request::GET()->getString('categories') );
	}

	public function filter_categories_catchForm( Form $form ) : void
	{
		$this->filter_categories_set( $form->field('categories')->getValue() );
	}

	protected function filter_categories_getForm( Form $form ) : void
	{
		$categories = new Form_Field_Hidden('categories', '', $this->categories ? implode(',', $this->categories):'');
		$form->addField($categories);
	}

	protected function filter_categories_getWhere() : void
	{
		if(!$this->categories) {
			return;
		}

		$items = Product_Category::fetch([
			'products_categories' => [
				'category_id' => $this->categories
			]
		]);

		$ids = [];

		foreach($items as $item) {
			/**
			 * @var Product_Category $item
			 */

			$ids[] = (int)$item->getProductId();
		}


		if(!$ids) {
			$ids = [0];
		}


		$this->filter_addWhere([
			'id'   => $ids,
		]);

	}









	protected function filter_is_active_prepare() : void
	{
		foreach(Shops::getList() as $code => $shop) {
			if(!array_key_exists($code, $this->is_active_per_shop)) {
				$this->is_active_per_shop[$code] = '0';
			}
		}

	}



	protected function filter_is_active_catchGetParams() : void
	{
		$this->is_active_general = Http_Request::GET()->getString('is_active_general', '0', ['0', '1', '-1']);
		if($this->is_active_general!='0') {
			$this->setGetParam('is_active_general', $this->is_active_general);
		}

		foreach(Shops::getList() as $code => $shop) {
			$this->is_active_per_shop[$code] = Http_Request::GET()->getString('is_active_'.$code, '0', ['0', '1', '-1']);

			if($this->is_active_per_shop[$code]!='0') {
				$this->setGetParam('is_active_'.$code, $this->is_active_per_shop[$code]);
			}
		}
	}

	public function filter_is_active_catchForm( Form $form ) : void
	{
		$this->is_active_general = $form->field('is_active_general')->getValue();
		if($this->is_active_general!='0') {
			$this->setGetParam('is_active_general', $this->is_active_general);
		} else {
			$this->setGetParam('is_active_general', '');
		}

		foreach(Shops::getList() as $code => $shop) {
			$this->is_active_per_shop[$code] = $form->field('is_active_'.$code)->getValue();
			if($this->is_active_per_shop[$code]!='0') {
				$this->setGetParam('is_active_'.$code, $this->is_active_per_shop[$code]);
			} else {
				$this->setGetParam('is_active_'.$code, '');
			}
		}
	}

	protected function filter_is_active_getForm( Form $form ) : void
	{
		$this->filter_is_active_prepare();

		$options = [
			'0' => Tr::_('- all -'),
			'-1' => Tr::_('Not active'),
			'1' => Tr::_('Active'),
		];

		$error_messages = [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select option',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select option'
		];

		$is_active_general = new Form_Field_Select('is_active_general', 'Is active - general:', $this->is_active_general);
		$is_active_general->setSelectOptions( $options );;
		$is_active_general->setErrorMessages($error_messages);
		$form->addField($is_active_general);

		foreach(Shops::getList() as $code => $shop) {

			$is_active = new Form_Field_Select('is_active_'.$code, 'Is active - '.$shop->getName().':', $this->is_active_per_shop[$code]);
			$is_active->setSelectOptions( $options );;
			$is_active->setErrorMessages($error_messages);
			$form->addField($is_active);
		}

	}

	protected function filter_is_active_getWhere() : void
	{
		$this->filter_is_active_prepare();

		$where = [];

		if($this->is_active_general!='0') {
			$where['is_active'] = ($this->is_active_general=='1');
		}

		foreach(Shops::getList() as $code=>$shop) {
			if($this->is_active_per_shop[$code]=='0') {
				continue;
			}

			$_ids = Product_ShopData::fetchData(['product_id'], [
				'shop_code' => $code,
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
			$this->filter_addWhere($where);
		}
	}
	
	
	
}