<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductReviews;


use Jet\Form;
use Jet\Form_Field_Range;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\Order_Item;
use JetApplication\Product_EShopData;
use JetApplication\ProductReview;
use JetApplication\Order;

abstract class ReviewManager_Common {
	
	protected ?Order $order = null;
	protected ?Product_EShopData $product = null;
	protected ?ProductReview $review = null;
	protected ?Form $review_form = null;
	
	
	/**
	 * @var array<int,Product_EShopData>|null
	 */
	protected ?array $possible_products = null;
	
	/**
	 * @var array<int,ProductReview>|null
	 */
	protected ?array $already_written_reviews = null;
	
	/**
	 * @var array<int,int>|null
	 */
	protected ?array $products_to_orders_map = null;
	
	public function __construct()
	{
		$this->init();
	}
	
	abstract protected function init() : void;
	
	protected function initPossibleProducts( array $order_ids ): void
	{
		$this->possible_products = [];
		$this->products_to_orders_map = [];
		
		if(!$order_ids) {
			return;
		}
		
		$orders = Order_Item::dataFetchAll(
			select: ['order_id','item_id'],
			where: [
				'order_id' => $order_ids,
				'AND',
				'type' => [Order_Item::ITEM_TYPE_PRODUCT, Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT],
			]
		);
		
		foreach($orders as $o) {
			$product_id = $o['item_id'];
			$order_id = $o['order_id'];
			
			$product = Product_EShopData::get( $product_id );
			if(!$product) {
				continue;
			}
			
			if($product->isVariant()) {
				$master = $product->getVariantMasterProduct();
				if($master) {
					$product = $master;
				}
			}
			
			$this->products_to_orders_map[$product->getId()] = $order_id;
			
			$this->possible_products[$product->getId()] = $product;
		}
		
	}
	
	public function initWriteReview() : void
	{
		if( ($product_id=Http_Request::GET()->getInt('write_review')) ) {
			if( !isset($this->possible_products[$product_id] ) ) {
				Http_Headers::reload(unset_GET_params: ['write_review']);
			}
			
			$this->product = $this->possible_products[$product_id];
			if(!$this->order) {
				$this->order = Order::get( $this->products_to_orders_map[$product_id] );
			}
			
			if(isset( $this->already_written_reviews[$product_id])) {
				$this->review = $this->already_written_reviews[$product_id];
			} else {
				$this->review = new ProductReview();
				$this->review->setEshop( $this->order->getEshop() );
				$this->review->setOrderId( $this->order->getId() );
				$this->review->setProductId( $this->product->getId() );
				$this->review->setCustomerId( $this->order->getCustomerId() );
				
				$this->review->setAuthorEmail( $this->order->getEmail() );
				$this->review->setAuthorName( $this->order->getBillingFirstName().' '.$this->order->getBillingSurname() );
				
				$this->review->setRank( 100 );
				
			}
			
		}
		
	}
	
	
	public function getOrder(): ?Order
	{
		return $this->order;
	}
	
	public function getProduct(): ?Product_EShopData
	{
		return $this->product;
	}
	
	public function getReview(): ?ProductReview
	{
		return $this->review;
	}
	
	
	
	
	
	public function getReviewForm() : ?Form
	{
		if(!$this->product) {
			return null;
		}
		
		if(!$this->review_form) {
			
			$rank = new Form_Field_Range('rank', 'Rank:');
			$rank->setDefaultValue(100);
			$rank->setMinValue(0);
			$rank->setMaxValue(100);
			$rank->setErrorMessages([
				Form_Field_Range::ERROR_CODE_OUT_OF_RANGE => 'Out of range'
			]);
			$rank->setDefaultValue( $this->review->getRank() );
			$rank->setFieldValueCatcher( function( $value ) {
				$this->review->setRank( (int)$value );
			} );
			
			$positive_characteristics = new Form_Field_Textarea('positive', 'Positive:');
			$positive_characteristics->setDefaultValue( $this->review->getPositiveCharacteristics() );
			$positive_characteristics->setFieldValueCatcher( function( $value ) {
				$this->review->setPositiveCharacteristics( trim($value) );
			} );
			
			$negative_characteristics = new Form_Field_Textarea('negative', 'Negative:');
			$negative_characteristics->setDefaultValue( $this->review->getNegativeCharacteristics() );
			$negative_characteristics->setFieldValueCatcher( function( $value ) {
				$this->review->setNegativeCharacteristics( trim($value) );
			} );
			
			$summary = new Form_Field_Textarea('summary', 'Summary:');
			$summary->setDefaultValue( $this->review->getSummary() );
			$summary->setFieldValueCatcher( function( $value ) {
				$this->review->setSummary( trim($value) );
			} );
			
			$this->review_form = new Form('new_review', [
				$rank,
				$positive_characteristics,
				$negative_characteristics,
				$summary
			]);
			
			
			if($this->review->isAssessed()) {
				$this->review_form->setIsReadonly();
			}
			
		}
		
		return $this->review_form;
	}
	
	public function catchWriteReviewForm() : bool
	{
		$form = $this->getReviewForm();
		if(!$form->catch()) {
			return false;
		}
		
		$this->review->save();
		
		return true;
	}
	
	
	/**
	 * @return array<int,Product_EShopData>
	 */
	public function getPossibleProducts() : array
	{
		return $this->possible_products;
	}
	
	/**
	 * @return array<int,ProductReview>
	 */
	public function getAlreadyWrittenReviews() : iterable
	{
		return $this->already_written_reviews;
	}
	
}