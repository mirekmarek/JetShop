<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductReviews;

use JetApplication\Customer;
use JetApplication\ProductReview;
use JetApplication\Order;

class ReviewManager_LoggedInCustomer extends ReviewManager_Common
{
	protected Customer $customer;
	
	protected function init() : void
	{
		$this->customer = Customer::getCurrentCustomer();
		
		$this->already_written_reviews = [];
		
		$already_written = ProductReview::fetch([''=>['customer_id'=>$this->customer->getId()]], order_by: '-id');
		foreach($already_written as $review) {
			$this->already_written_reviews[$review->getProductId()] = $review;
		}
		
		$order_ids = Order::dataFetchCol(
			select: ['id'],
			where: [
				'customer_id' => $this->customer->getId()
			]
		);
		
		$this->initPossibleProducts( $order_ids );
		$this->initWriteReview();
	}
	
}