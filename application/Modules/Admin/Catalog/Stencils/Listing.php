<?php
namespace JetShopModule\Admin\Catalog\Stencils;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Form_Field_Search;
use Jet\Http_Request;

use JetShop\Stencil;

class Listing extends Data_Listing {

	protected array $grid_columns = [
		'_edit_'     => [
			'title'         => '',
			'disallow_sort' => true
		],
		'id'         => ['title' => 'ID'],
		'name'       => ['title' => 'Name'],
	];

	protected array $filters = [
		'search',
	];

	protected string $search = '';

	protected function getList() : DataModel_Fetch_Instances
	{
		return Stencil::getList();
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
			'name *'   => $search,
		]);

	}


}