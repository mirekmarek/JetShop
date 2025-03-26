<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Factory_MVC;
use Jet\MVC_Controller;
use Jet\MVC_Page_Content_Interface;
use JetApplication\Admin_EntityManager_EditTabProvider_EditTab;
use JetApplication\Category;
use JetApplication\Customer;
use JetApplication\EShopEntity_Basic;
use JetApplication\KindOfProduct;
use JetApplication\Product;
use JetApplication\Signpost;
use Closure;

trait Main_Trait_Admin {
	
	protected Report_Controller $report_controller;
	
	public function setReportController( Report_Controller $report_controller ): void
	{
		$this->report_controller = $report_controller;
	}
	
	
	protected function handle( Closure $controller_factory ) : string
	{
		$content = Factory_MVC::getPageContentInstance();
		$content->setModuleName( $this->module_manifest->getName() );
		
		/**
		 * @var MVC_Controller $constroller
		 */
		$constroller = $controller_factory( $content );
		
		$content->setControllerAction( $constroller->resolve() );
		$constroller->dispatch();
		
		return $content->getOutput();
	}
	
	public function handleCustomerActivity( Customer $customer ) : string
	{
		return $this->handle( function( MVC_Page_Content_Interface $content ) use ($customer) {
			$constroller = new Controller_Customer( $content );
			$constroller->setCustomer( $customer );
			
			return $constroller;
		} );
	}
	
	
	public function handleProductAnalytics( Product $product ) : string
	{
		return $this->handle( function( MVC_Page_Content_Interface $content ) use ($product) {
			$constroller = new Controller_Product( $content );
			$constroller->setProduct( $product );
			
			return $constroller;
		} );
	}
	
	public function handleCategoryAnalytics( Category $category ) : string
	{
		return $this->handle( function( MVC_Page_Content_Interface $content ) use ($category) {
			$constroller = new Controller_Category( $content );
			$constroller->setCategory( $category );
			
			return $constroller;
		} );
	}
	
	public function handleSignpostAnalytics( Signpost $signpost ) : string
	{
		return $this->handle( function( MVC_Page_Content_Interface $content ) use ($signpost) {
			$constroller = new Controller_Signpost( $content );
			$constroller->setSignpost( $signpost );
			
			return $constroller;
		} );
	}
	
	public function handleKindOfProductAnalytics( KindOfProduct $kind_of_product ) : string
	{
		return $this->handle( function( MVC_Page_Content_Interface $content ) use ($kind_of_product) {
			$constroller = new Controller_KindOfProduct( $content );
			$constroller->setKindOfProduct( $kind_of_product );
			
			return $constroller;
		} );
	}
	
	public function provideEditTabs( EShopEntity_Basic $item ): array
	{
		$res = [];
		
		switch($item::getEntityType()) {
			
			
			case Customer::getEntityType():
				$tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$tab->setTab(
					tab_key: 'ja-user-activity',
					tab_title: 'User activity',
					tab_icon: 'chart-line'
				);
				
				$tab->setHandler( function() use ($item) : string {
					/**
					 * @var Customer $item
					 */
					return $this->handleCustomerActivity( $item );
				} );
				
				$res[] = $tab;
				break;
			
			
			
			
			case Product::getEntityType():
				$tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$tab->setTab(
					tab_key: 'ja-analytics',
					tab_title: 'Analytics',
					tab_icon: 'chart-line'
				);
				
				$tab->setHandler( function() use ($item) : string {
					/**
					 * @var Product $item
					 */
					return $this->handleProductAnalytics( $item );
				} );
				
				$res[] = $tab;
				break;
			
			
			
			case Category::getEntityType():
				$tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$tab->setTab(
					tab_key: 'ja-analytics',
					tab_title: 'Analytics',
					tab_icon: 'chart-line'
				);
				
				$tab->setHandler( function() use ($item) : string {
					/**
					 * @var Category $item
					 */
					return $this->handleCategoryAnalytics( $item );
				} );
				
				$res[] = $tab;
				break;
			
			
			
			case Signpost::getEntityType():
				$tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$tab->setTab(
					tab_key: 'ja-analytics',
					tab_title: 'Analytics',
					tab_icon: 'chart-line'
				);
				
				$tab->setHandler( function() use ($item) : string {
					/**
					 * @var Signpost $item
					 */
					return $this->handleSignpostAnalytics( $item );
				} );
				
				$res[] = $tab;
				break;
			
			case KindOfProduct::getEntityType():
				$tab = new Admin_EntityManager_EditTabProvider_EditTab( $item, $this );
				
				$tab->setTab(
					tab_key: 'ja-analytics',
					tab_title: 'Analytics',
					tab_icon: 'chart-line'
				);
				
				$tab->setHandler( function() use ($item) : string {
					/**
					 * @var KindOfProduct $item
					 */
					return $this->handleKindOfProductAnalytics( $item );
				} );
				
				$res[] = $tab;
				break;
		}
		
		
		
		return $res;
	}
	
	public function renderSelectReport() : string
	{
		$reports = $this->report_controller->getReports();
		$selected_report = $this->report_controller->getSelectedReport();
		
		$view = Factory_MVC::getViewInstance( static::getViewsDir() );
		$view->setVar('reports', $reports);
		$view->setVar('selected_report', $selected_report);
		
		return $view->render('select-report');
	}
	
	public function renderSelectPeriod() : string
	{
		$time_periods = $this->report_controller->getTimePeriods();
		$selected_time_period = $this->report_controller->getSelectedTimePeriod();
		$date_from = $this->report_controller->getDateFrom();
		$date_to = $this->report_controller->getDateTo();
		
		$view = Factory_MVC::getViewInstance( static::getViewsDir() );
		$view->setVar( 'time_periods', $time_periods );
		$view->setVar( 'selected_time_period', $selected_time_period );
		$view->setVar( 'date_from', $date_from );
		$view->setVar( 'date_to', $date_to );
		
		return $view->render('select-period');
	}
	
	public function renderSelectEShop() : string
	{
		
		$view = Factory_MVC::getViewInstance( static::getViewsDir() );
		$view->setVar( 'report', $this->report_controller->getSelectedReport() );
		
		return $view->render('select-eshop');
	}
	
	
	public function renderSelectSubReport() : string
	{
		$sub_reports = $this->report_controller->getSubReports();
		
		$view = Factory_MVC::getViewInstance( static::getViewsDir() );
		$view->setVar( 'sub-reports', $sub_reports );
		
		return $view->render('select-sub-report');
	}
	
	
	public function renderChart_Line_DaysPerEShop( array $data ) : string
	{
		$view = Factory_MVC::getViewInstance( static::getViewsDir() );
		$view->setVar( 'data', $data );
		
		return $view->render('chart/line/days-per-eshop');
		
	}
}