<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Hidden;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Category;
use JetApplication\DataExport_XLSX;
use JetApplication\EShop;
use JetApplication\EShops;
use JetApplication\Order_Item;
use JetApplication\Product;


class Report_General_ExportOrders extends Report_General
{
	public const KEY = 'export_orders';
	protected ?string $title = 'Export orders';
	protected bool $one_eshop_mode = true;
	protected bool $is_default = false;
	protected array $sub_reports = [
		'summary' => 'Summary',
	];
	
	protected array $selected_eshop_keys = [];
	
	
	protected EShop $eshop;
	protected array $categories;
	protected bool $include_subcategories;
	protected array $products;
	protected array $foce_non_relevant_products;
	protected array $ignore_products;
	
	public function prepare_summary() : void
	{
		
		/**
		 * @param string $value
		 * @param array<string> $errors
		 * @return array<int>
		 */
		$getCategories = function(string $value, array &$errors=[]) : array {
			$value = trim($value);
			if(!$value) {
				return [];
			}
			
			$_categories = explode(',', $value);
			$categories = [];
			foreach($_categories as $id) {
				$id=(int)trim($id);
				$category = Category::get($id);
				if(!$category) {
					$errors[] = 'Inknown category: '.$id;
				} else {
					$categories[] = $id;
				}
			}
			
			return $categories;
		};
		
		/**
		 * @param string $value
		 * @param array $errors
		 * @return array<int>
		 */
		$getProducts = function(string $value, array &$errors=[]) : array {
			$value = trim($value);
			if(!$value) {
				return [];
			}
			
			$products = [];
			$_products = explode(',', $value);
			foreach($_products as $id) {
				$id=(int)trim($id);
				$product = Product::get($id);
				if(!$product) {
					$errors[] = 'Unknown product: '.$id;
				} else {
					$products[] = $id;
				}
			}
			
			return $products;
		};
		
		
		
		$GET = Http_Request::GET();
		
		$fields = [];
		foreach(Http_Request::GET()->getRawData() as $key => $value) {
			if(!in_array($key, [
				'categories',
				'include_subcategories',
				'products',
				'foce_non_relevant_products',
				'ignore_products'
			])) {
				$field = new Form_Field_Hidden($key);
				$field->setDefaultValue( $value );
				$field->input()->setDataAttribute( 'def', 1 );
				
				$fields[] = $field;
			}
		}
		
		
		
		
		$this->categories = $getCategories( $GET->getRaw('categories', '') );
		$field_categories = new Form_Field_Hidden('categories', 'Category:', );
		$field_categories->setDefaultValue( implode(',', $this->categories) );
		$fields[] = $field_categories;
		
		$this->include_subcategories = $GET->getBool('include_subcategories', true);
		$field_include_subcategories = new Form_Field_Checkbox('include_subcategories', 'include subcategories');
		$field_include_subcategories->setDefaultValue( $this->include_subcategories );
		$fields[] = $field_include_subcategories;
		
		$this->products = $getProducts( $GET->getRaw('products', '') );
		$field_products = new Form_Field_Hidden('products', 'Produkty:' );
		$field_products->setDefaultValue( implode(',', $this->products) );
		$fields[] = $field_products;
		
		$this->foce_non_relevant_products = $getProducts( $GET->getRaw('foce_non_relevant_products', '') );
		$field_foce_non_relevant_products = new Form_Field_Hidden('foce_non_relevant_products', 'Force non relevant products:');
		$field_foce_non_relevant_products->setDefaultValue( implode(',', $this->foce_non_relevant_products) );
		$fields[] = $field_foce_non_relevant_products;
		
		$this->ignore_products = $getProducts( $GET->getRaw('ignore_products', '') );
		$field_ignore_products = new Form_Field_Hidden('ignore_products', 'Ignore products:' );
		$field_ignore_products->setDefaultValue( implode(',', $this->ignore_products) );
		$fields[] = $field_ignore_products;
		
		$form = new Form('export_form', $fields);
		
		$this->view->setVar('form', $form);
		
		if( $form->catch() ) {
			$errors = [];
			$categories = $getCategories($field_categories->getValue(), $errors);
			$products = $getProducts($field_products->getValue(), $errors);
			$foce_non_relevant_products = $getProducts($field_foce_non_relevant_products->getValue(), $errors);
			$ignore_products = $getProducts($field_ignore_products->getValue(), $errors);
			
			if(!$products && !$categories) {
				$errors[] = 'Nejsou určeny ani kategorie ani produkty.';
			}
			
			if($products && $categories) {
				$errors[] = 'Jsou určeny kategorie i produkty. Je možné použít pouze jedno nebo druhé.';
			}
			
			if($errors) {
				$this->view->setVar('errors', $errors);
			} else {
				$params = [
					'categories' => implode(',', $categories),
					'include_subcategories' => $field_include_subcategories->getValue()?1:0,
					'products' => implode(',', $products),
					'foce_non_relevant_products' => implode(',', $foce_non_relevant_products),
					'ignore_products' => implode(',', $ignore_products),
				];
				
				foreach(Http_Request::GET()->getRawData() as $key => $value) {
					if(!isset($params[$key])) {
						$params[$key] = $value;
					}
				}
				
				Http_Headers::reload( $params );
			}
		} else {
			
			$this->eshop = EShops::get( $this->getSelectedEshopKeys()[0] );
			
			if(
				$this->products ||
				$this->categories
			) {
				
				if(
					$this->categories &&
					$this->include_subcategories
				) {
					$categories = [];
					foreach($this->categories as $c_id) {
						$categories[] = $c_id;
						$categories = array_merge( $categories, Category::get($c_id)->getChildrenIds() );
					}
					
				}
				
				
				$events = $this->getRelevantEvents();
				
				if($GET->getString('export')) {
					$this->export( $events );
				}
				
				$sum = [
					'amount' => 0,
					'relevant_amount' => 0,
					'relevant_qty' => 0,
					'non_relevant_amount' => 0,
					'non_relevant_qty' => 0,
				];
				
				foreach($events as $e) {
					$sum['amount'] += $e->getNonRelevantAmount() + $e->getRelevantAmount();
					
					$sum['relevant_amount'] += $e->getRelevantAmount();
					$sum['relevant_qty'] += $e->getRelevantQty();
					
					$sum['non_relevant_amount'] += $e->getNonRelevantAmount();
					$sum['non_relevant_qty'] += $e->getNonRelevantQty();
				}
				
				$this->view->setVar( 'sum', $sum);
				$this->view->setVar( 'events', $events );
				$this->view->setVar( 'eshop', $this->eshop );

			}
		}
		
		
	}
	
	
	/**
	 * @param array<Report_General_ExportOrders_RelevantEvent> $events
	 * @return void
	 */
	protected function export( array $events ) : void
	{
		$sheet_data = [];
		foreach($events as $e) {
			$event = $e->getEvent();
			
			$relevant_items = [];
			
			foreach( $e->getRelevantItems() as $item ) {
				$relevant_items[] = $item->getNumberOfUnits().'x '.$e->getItemName($item);
			}
			$relevant_items = implode("\r\n", $relevant_items);
			
			$non_relevant_items = [];
			foreach( $e->getNonRelevantItems() as $item ) {
				$non_relevant_items[] = $item->getNumberOfUnits().'x '.$e->getItemName($item);
			}
			$non_relevant_items = implode("\r\n", $non_relevant_items);
			
			$sheet_data[] = [
				$event->getOrderNumber(),
				$event->getDateTime(),
				
				$event->getTotalAmountWithVAT(),
				
				$relevant_items,
				
				$e->getRelevantAmount(),
				$e->getRelevantQty(),
				
				$non_relevant_items,
				
				$e->getNonRelevantAmount(),
				$e->getNonRelevantQty()
			
			];
		}
		
		$sheet_name = 'Přehled objednávek';
		$export_header = [
			Tr::_('Order number'),
			Tr::_('Date and time'),
			
			Tr::_('Total'),
			
			Tr::_('Relevant items'),
			Tr::_('Amount of relevant items'),
			Tr::_('Number of relevant items'),
			
			Tr::_('Non-relevant items'),
			Tr::_('Amount of non-relevant items'),
			Tr::_('Number of non-relevant items'),
		];
		$file_name = 'orders_'.$this->getDateFrom().'_'.$this->getDateTo().'_'.date('Ymd').'.xlsx';
		
		
		$xlsx = new DataExport_XLSX(
			header: $export_header,
			data: $sheet_data
		);
		$xlsx->setSheetName($sheet_name);
		
		
		$xlsx->sentToBeDownloaded( $file_name );
		
	}
	
	/**
	 * @return array<Report_General_ExportOrders_RelevantEvent>
	 */
	protected function getRelevantEvents() : array
	{
		$events = Event_Purchase::fetchInstances([
			$this->eshop->getWhere(),
			'AND',
			'date_time >=' => $this->getDateFrom(),
			'AND',
			'date_time <=' => $this->getDateTo(),
		]);
		
		$relevant_events = [];
		foreach($events as $event) {
			$is_relevant = false;
			
			$relevant_items = [];
			$non_relevant_items = [];
			
			
			foreach($event->getItems() as $item) {
				if(!in_array($item->getType(), [
					Order_Item::ITEM_TYPE_PRODUCT,
					Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT
				])) {
					continue;
				}
				
				$product_id = $item->getItemId();
				
				if(
					in_array($product_id, $this->ignore_products)
				) {
					continue;
				}
				
				if(in_array($product_id, $this->foce_non_relevant_products)) {
					$non_relevant_items[] = $item;
					continue;
				}
				
				if(
					in_array($product_id, $this->products) ||
					array_intersect($this->categories, $item->getCategoryIds())
				) {
					$relevant_items[] = $item;
				} else {
					$non_relevant_items[] = $item;
				}
			}
			
			if($relevant_items) {
				$relevant_events[] = new Report_General_ExportOrders_RelevantEvent( $event, $relevant_items, $non_relevant_items );
			}
		}

		return $relevant_events;
	}
	
}