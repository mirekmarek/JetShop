<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\EShop\ProductReviews;

use Jet\Form;
use Jet\Form_Field_Range;
use Jet\Form_Field_Textarea;
use JetApplication\Customer;
use JetApplication\Order_Item;
use JetApplication\Product_EShopData;
use JetApplication\ProductReview;
use JetApplication\Order;

class CustomerReviewManager {
	protected Customer $customer;
	protected ?array $possible_product_ids = null;
	protected ?int $write_review_product_id = null;
	protected ?ProductReview $new_review = null;
	protected ?Form $new_review_form = null;
	
	
	public function __construct()
	{
		$this->customer = Customer::getCurrentCustomer();
		$this->getPossibleProductIds();
	}
	
	
	public function getPossibleProductIds() : array
	{
		if( $this->possible_product_ids===null ) {
			$this->possible_product_ids = [];
			
			$order_ids = Order::dataFetchCol(
				select: ['id'],
				where: [
					'customer_id' => $this->customer->getId()
				]
			);
			
			if($order_ids) {
				$already_written = ProductReview::dataFetchCol(
					select: ['product_id'],
					where: ['customer_id'=>$this->customer->getId()],
					raw_mode: true
				);
				
				if(!$already_written) {
					$already_written = ['0'];
				}
				
				$_possible_product_ids = Order_Item::dataFetchCol(
					select: ['item_id'],
					where: [
						'order_id' => $order_ids,
						'AND',
						'type' => [Order_Item::ITEM_TYPE_PRODUCT, Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT],
					]
				);
				
				foreach($_possible_product_ids as $id) {
					if(in_array($id, $already_written)) {
						continue;
					}
					
					$product = Product_EShopData::get( $id );
					if(!$product) {
						continue;
					}
					
					if($product->isVariant()) {
						$this->possible_product_ids[] = $product->getVariantMasterProductId();
					} else {
						$this->possible_product_ids[] = $product->getId();
					}
				}
				
				$this->possible_product_ids = array_unique($this->possible_product_ids);
			}
			
		}
		
		return $this->possible_product_ids;
	}
	
	/**
	 * @return Product_EShopData[]
	 */
	public function getPossibleProducts() : array
	{
		$ids = $this->getPossibleProductIds();
		if(!$ids) {
			return [];
		}
		return Product_EShopData::getActiveList( $ids );
	}
	
	public function getWriteReviewProductId(): ?int
	{
		return $this->write_review_product_id;
	}
	
	public function setWriteReviewProductId( ?int $write_review_product_id ): void
	{
		$this->write_review_product_id = $write_review_product_id;
	}
	
	public function getWriteReviewProduct(): ?Product_EShopData
	{
		if(!$this->write_review_product_id) {
			return null;
		}
		
		return Product_EShopData::get( $this->write_review_product_id );
	}
	
	
	public function getWriteReviewForm() : Form
	{
		if(!$this->new_review_form) {
			$customer = $this->customer;
			
			$this->new_review = new ProductReview();
			$this->new_review->setEshop( $customer->getEshop() );
			$this->new_review->setProductId( $this->write_review_product_id );
			$this->new_review->setAuthorEmail( $customer->getEmail() );
			$this->new_review->setAuthorName( $customer->getName() );
			$this->new_review->setCustomerId( $customer->getId() );
			
			$rank = new Form_Field_Range('rank', 'Rank:');
			$rank->setDefaultValue(100);
			$rank->setMinValue(0);
			$rank->setMaxValue(100);
			$rank->setErrorMessages([
				Form_Field_Range::ERROR_CODE_OUT_OF_RANGE => 'Out of range'
			]);
			$rank->setFieldValueCatcher( function( $value ) {
				$this->new_review->setRank( (int)$value );
			} );
			
			$positive_characteristics = new Form_Field_Textarea('positive', 'Positive:');
			$positive_characteristics->setFieldValueCatcher( function( $value ) {
				$this->new_review->setPositiveCharacteristics( trim($value) );
			} );
			
			$negative_characteristics = new Form_Field_Textarea('negative', 'Negative:');
			$negative_characteristics->setFieldValueCatcher( function( $value ) {
				$this->new_review->setNegativeCharacteristics( trim($value) );
			} );
			
			$summary = new Form_Field_Textarea('summary', 'Summary:');
			$summary->setFieldValueCatcher( function( $value ) {
				$this->new_review->setSummary( trim($value) );
			} );
			
			$this->new_review_form = new Form('new_review', [
				$rank,
				$positive_characteristics,
				$negative_characteristics,
				$summary
			]);
			
			
		}
		
		return $this->new_review_form;
	}
	
	public function catchWriteReviewForm() : bool
	{
		$form = $this->getWriteReviewForm();
		if(!$form->catch()) {
			return false;
		}
		
		$this->new_review->save();
		
		return true;
	}
	
	/**
	 * @return ProductReview[]
	 */
	public function getCustomersReviews() : iterable
	{
		$list = ProductReview::fetchInstances([
			'customer_id' => $this->customer->getId()
		]);
		
		$list->getQuery()->setOrderBy('-id');
		
		return $list;
	}
	
}