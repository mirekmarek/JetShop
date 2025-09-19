<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Customers\DeletePersonalData;


use Jet\Application_Module;
use Jet\Data_DateTime;
use Jet\Db;
use Jet\Tr;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\ProductQuestion;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;
use JetApplication\Customer;

class Main extends Application_Module implements SysServices_Provider_Interface
{
	
	public function getSysServicesDefinitions(): array
	{
		$services = [];
		
		$service = new SysServices_Definition(
			module: $this,
			name: Tr::_('Delete non-active customer'),
			description: Tr::_('Deletes all non-relevant personal data'),
			service_code: 'perform',
			service: function() {
				$this->performDeletion();
			}
		);
		
		$service->setIsPeriodicallyTriggeredService( true );
		$service->setServiceRequiresEshopDesignation( true );
		
		return [
			$service
		];
		
		
		return $services;
	}
	
	protected function deleteCustomers() : void
	{
		$eshop = EShops::getCurrent();
		
		$date = Data_DateTime::catchDateTime( date('Y-m-d', strtotime('-5 years')) );
		
		$ids = Customer::dataFetchCol(
			select: ['id'],
			where:[
				EShops::getCurrent()->getWhere(),
				'AND',
				'last_login_date_time <= ' => $date,
			],
			limit: 50
		);
		
		$c = 0;
		$count = count($ids);
		
		foreach( $ids as $customer_id ) {
			$c++;
			$cst = Customer::load( $customer_id );
			if(!$cst) {
				echo "[$c/$count] $customer_id ????\n";
				
				continue;
			}
			
			echo "Customers [$c/$count] $customer_id ({$cst->getName()}, {$cst->getEmail()}, {$cst->getCreated()}, {$cst->getLastLoginDateTime()})\n";
			$cst->deletePersonalData();
		}
	}
	
	protected function deleteQuestions(): void
	{
		$date = Data_DateTime::catchDateTime( date('Y-m-d', strtotime('-3 years')) );
		
		$ids = ProductQuestion::dataFetchCol(
			select: ['id'],
			where:[
				EShops::getCurrent()->getWhere(),
				'AND',
				'created <=' => $date,
			]
		);
		
		$db = Db::get();
		$date = date('Y-m-d', strtotime('-3 years'));
		
		
		$c = 0;
		$count = count( $ids );
		
		foreach($ids as $id ) {
			$c++;
			echo "Product questions [$c/$count] $id\n";
			
			$q = ProductQuestion::get($id);
			$q?->delete();
			$q?->actualizeProduct();
		}

	}
	
	protected function deleteOrders(): void
	{
		$date = Data_DateTime::catchDateTime( date('Y-m-d', strtotime('-10 years')) );
		
		$ids = Order::dataFetchCol(
			select: ['id'],
			where:[
				EShops::getCurrent()->getWhere(),
				'AND',
				'date_purchased <= ' => $date,
				'AND',
				'customer_id' => 0
			],
			limit: 50
		);
		
		
		$c = 0;
		$count = count( $ids );
		
		foreach($ids as $id ) {
			$c++;
			$order = Order::get($id);
			
			if(!$order) {
				echo "Orders [$c/$count] $id ????\n";
				continue;
				
			}
			
			echo "Orders [$c/$count] $id - {$order->getNumber()}\n";
			
			$order->deletePersonalData();
		}
		
	}
	
	public function performDeletion() : void
	{
		$this->deleteCustomers();
		$this->deleteQuestions();
		$this->deleteOrders();
	}
}