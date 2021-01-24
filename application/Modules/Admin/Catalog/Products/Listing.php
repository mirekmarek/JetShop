<?php
namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Form_Field_Search;
use Jet\Http_Request;

use JetShop\Product;

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
	];

	protected string $search = '';

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

		$search = '%'.$this->search.'%';
		$this->filter_addWhere([
			'products_shop_data.name *'   => $search,
		]);

	}

}