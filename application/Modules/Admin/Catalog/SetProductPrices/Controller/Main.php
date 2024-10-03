<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\SetProductPrices;

use Exception;
use Jet\Form;
use Jet\Form_Field_File;
use Jet\Form_Field_File_UploadedFile;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\MVC_Controller_Default;
use Jet\Navigation_Breadcrumb;
use Jet\SysConf_Path;
use Jet\Tr;
use Jet\UI_messages;
use Jet\UI_tabs;
use JetApplication\Admin_Managers;
use JetApplication\Brand;
use JetApplication\Pricelists;
use JetApplication\Pricelists_Pricelist;
use JetApplicationModule\Admin\Suppliers\Supplier;
use XLSXReader\XLSXReader;

class Controller_Main extends MVC_Controller_Default
{
	protected UI_tabs $tabs;
	
	protected ?Pricelists_Pricelist $selected_pricelist = null;
	
	protected ?string $product_identifier = null;
	
	protected ?string $select_by = null;
	
	protected ?int $supplier_id = null;
	
	protected ?int $brand_id = null;
	
	protected array $product_identifiers = [];
	
	public function resolve(): bool|string
	{
		$GET = Http_Request::GET();
		
		$this->tabs = Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module->getModuleManifest()->getName(),
			action: function() {
				return new UI_tabs(
					tabs: [
						'manually' => Tr::_('Set prices manually'),
						'export' => Tr::_('Export price list'),
						'import' => Tr::_('Import price list'),
					],
					tab_url_creator: function( string $tab ) : string
					{
						return Http_Request::currentURI(set_GET_params: ['p'=>$tab]);
					},
					selected_tab_id: Http_Request::GET()->getString('p')
				);
				
			}
		);
		
		$this->view->setVar('tabs', $this->tabs );
		
		
		$pricelists = array_keys( Pricelists::getScope() );
		
		$pricelist_code = $GET->getString('pricelist',
			default_value: $pricelists[0],
			valid_values: $pricelists
		);
		
		if($pricelist_code) {
			$this->selected_pricelist = Pricelists::get( $pricelist_code );
			
			$this->view->setVar('selected_pricelist', $this->selected_pricelist );
		}
			
		
		
		
		if($this->tabs->getSelectedTabId()=='manually') {
			$this->product_identifiers = ['id'=>'id'];
			$this->product_identifier = 'id';
		} else {
			$this->product_identifiers = ProductPriceList::getIdentifiers();
			$this->product_identifier = $GET->getString('product_identifier', '', array_keys($this->product_identifiers));
			$this->view->setVar( 'product_identifiers', $this->product_identifiers);
		}
		$this->view->setVar( 'product_identifier', $this->product_identifier);
		
		
		$select_by_scope = [
			'supplier' => Tr::_('Supplier'),
			'brand'    => Tr::_('Brand'),
		];
		$this->select_by = $GET->getString('select_by', '', array_keys($select_by_scope));
		$this->view->setVar( 'select_by_scope', $select_by_scope);
		$this->view->setVar( 'select_by', $this->select_by);
		
		switch($this->select_by) {
			case 'supplier':
				$this->supplier_id = $GET->getInt('supplier');
				if(!Supplier::exists($this->supplier_id)) {
					$this->supplier_id = 0;
				}
				$this->view->setVar('supplier_id', $this->supplier_id);
				break;
			case 'brand':
				$this->brand_id = $GET->getInt('brand');
				if(!Brand::exists($this->brand_id)) {
					$this->brand_id = 0;
				}
				$this->view->setVar('brand_id', $this->brand_id);
				break;
		}
		
		
		if(
			$this->selected_pricelist &&
			$this->product_identifier &&
			$this->select_by &&
			($this->supplier_id || $this->brand_id)
		) {
			return $this->tabs->getSelectedTabId();
		}
		
		return 'default';
	}
	
	protected function setBreadcrumbNavigation( string $current_label = '', string $URL = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();
		
		if($current_label) {
			Navigation_Breadcrumb::addURL( $current_label, $URL );
		}
	}
	
	
	public function default_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
	}
	
	protected function readPriceList() : ProductPriceList
	{
		return new ProductPriceList(
			$this->product_identifier,
			$this->selected_pricelist,
			$this->brand_id,
			$this->supplier_id
		);
	}
	
	public function export_Action() : void
	{
		if(Http_Request::GET()->exists('export')) {
			$this->readPriceList()->export();
		}
		
		$this->setBreadcrumbNavigation();
		
		$this->output('export');
		
	}
	
	public function import_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$price_list = new Form_Field_File('price_list', 'Price list:');
		$price_list->setErrorMessages([
			Form_Field_File::ERROR_CODE_EMPTY => 'Please select price list',
			Form_Field_File::ERROR_CODE_FILE_IS_TOO_LARGE => 'File is too large'
		]);
		$price_list->setIsRequired(true);
		
		$form = new Form(
			name:'import_form',
			fields: [$price_list]
		);
		$form->setAction( Http_Request::currentURI(unset_GET_params: ['price_list', 'do_it']) );
		
		$dir = SysConf_Path::getTmp().'/product_price_list/';
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create($dir);
		}
		
		if($form->catch()) {
			foreach($price_list->getValue() as $file) {
				/**
				 * @var Form_Field_File_UploadedFile $file
				 */
				try {
					$xlsx = new XLSXReader( $file->getTmpFilePath() );
					
					IO_File::moveUploadedFile( $file->getTmpFilePath(), $dir.$file->getFileName() );
					
					Http_Headers::reload(['price_list'=>$file->getFileName()]);

				} catch( Exception $e ) {
					$form->setCommonMessage(
						UI_messages::createDanger( Tr::_('Unable to read price list') )
					);
				}
				
				
				break;
			}
		}
		
		$this->view->setVar('form', $form);
		
		
		$price_list_file = Http_Request::GET()->getString('price_list');
		if(
			$price_list_file &&
			IO_File::exists( $dir.$price_list_file )
		) {
			$price_list = $this->readPriceList();
			$price_list->import( $dir.$price_list_file );
			
			$this->view->setVar( 'price_list', $price_list );
			
			if(Http_Request::GET()->exists('do_it')) {
				$price_list->setNewPrices();
				
				$this->view->setVar('done', true);
			}
		}
		
		
		$this->output('import');
	}
	
	public function manually_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$price_list = $this->readPriceList();
		$this->view->setVar('price_list', $price_list);
		
		$POST = Http_Request::POST();
		if($POST->exists('new_prices')) {
			foreach( $POST->getRaw('new_prices') as $identifier=>$new_price ) {
				foreach($price_list->getItems() as $item) {
					if($item->getProductIdentification()==$identifier) {
						$item->setNewPrice( $new_price );
					}
				}
			}

			$price_list->setNewPrices();
			Http_Headers::reload();
		}
		
		$this->output('manually');
		
	}
}