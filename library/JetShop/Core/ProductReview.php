<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_Admin_Interface;
use JetApplication\Entity_Admin_Trait;
use JetApplication\Admin_Managers_ProductReviews;
use JetApplication\Entity_HasGet_Interface;
use JetApplication\Entity_HasGet_Trait;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Entity_Definition;
use JetApplication\Product;
use JetApplication\Product_EShopData;

#[DataModel_Definition(
	name: 'product_reviews',
	database_table_name: 'product_reviews',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_ProductReviews::class
)]
abstract class Core_ProductReview extends Entity_WithEShopRelation implements Entity_Admin_Interface, Entity_HasGet_Interface
{
	use Entity_HasGet_Trait;
	use Entity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Author - name:'
	)]
	protected string $author_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Author - e-nail:'
	)]
	protected string $author_email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $rank = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Positive characteristics:'
	)]
	protected string $positive_characteristics = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Negative characteristics:'
	)]
	protected string $negative_characteristics = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Summary:'
	)]
	protected string $summary = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $assessed = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $assessed_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $approved = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $approved_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Our comments:'
	)]
	protected string $our_comments = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $customer_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $customer_bonus_added = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $customer_bonus_added_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $source = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $source_id = '';
	
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ): void
	{
		$product = Product::load( $product_id );
		if( !$product ) {
			return;
		}
		
		if( $product->isVariant() ) {
			$product_id = $product->getVariantMasterProductId();
		}
		
		$this->product_id = $product_id;
	}
	
	public function getAuthorName(): string
	{
		return $this->author_name;
	}
	
	public function setAuthorName( string $author_name ): void
	{
		$this->author_name = $author_name;
	}
	
	public function getAuthorEmail(): string
	{
		return $this->author_email;
	}
	
	public function setAuthorEmail( string $author_email ): void
	{
		$this->author_email = $author_email;
	}
	
	public function getRank(): int
	{
		return $this->rank;
	}
	
	public function setRank( int $rank ): void
	{
		$this->rank = $rank;
	}
	
	public function getPositiveCharacteristics(): string
	{
		return $this->positive_characteristics;
	}
	
	public function setPositiveCharacteristics( string $positive_characteristics ): void
	{
		$this->positive_characteristics = $positive_characteristics;
	}
	
	public function getNegativeCharacteristics(): string
	{
		return $this->negative_characteristics;
	}
	
	public function setNegativeCharacteristics( string $negative_characteristics ): void
	{
		$this->negative_characteristics = $negative_characteristics;
	}
	
	public function getSummary(): string
	{
		return $this->summary;
	}
	
	public function setSummary( string $summary ): void
	{
		$this->summary = $summary;
	}
	
	public function isAssessed(): bool
	{
		return $this->assessed;
	}
	
	public function setAssessed( bool $assessed ): void
	{
		$this->assessed = $assessed;
		if( $this->assessed ) {
			$this->assessed_date_time = Data_DateTime::now();
		} else {
			$this->assessed_date_time = null;
		}
	}
	
	public function getAssessedDateTime(): ?Data_DateTime
	{
		return $this->assessed_date_time;
	}
	
	public function isApproved(): bool
	{
		return $this->approved;
	}
	
	public function setApproved( bool $approved ): void
	{
		$this->approved = $approved;
		if( $this->approved ) {
			$this->approved_date_time = Data_DateTime::now();
		} else {
			$this->approved_date_time = null;
		}
		
		$this->actualizeProduct();
	}
	
	public function getApprovedDateTime(): ?Data_DateTime
	{
		return $this->approved_date_time;
	}
	
	
	public function getOurComments(): string
	{
		return $this->our_comments;
	}
	
	public function setOurComments( string $our_comments ): void
	{
		$this->our_comments = $our_comments;
	}
	
	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	public function setCustomerId( int $customer_id ): void
	{
		$this->customer_id = $customer_id;
	}
	
	public function isCustomerBonusAdded(): bool
	{
		return $this->customer_bonus_added;
	}
	
	public function setCustomerBonusAdded( bool $customer_bonus_added ): void
	{
		$this->customer_bonus_added = $customer_bonus_added;
	}
	
	public function getCustomerBonusAddedDateTime(): ?Data_DateTime
	{
		return $this->customer_bonus_added_date_time;
	}
	
	public function setCustomerBonusAddedDateTime( Data_DateTime|null|string $date_time ): void
	{
		$this->customer_bonus_added_date_time = Data_DateTime::catchDateTime( $date_time );
	}
	
	public function getSource(): string
	{
		return $this->source;
	}
	
	public function setSource( string $source ): void
	{
		$this->source = $source;
	}
	
	public function getSourceId(): string
	{
		return $this->source_id;
	}
	
	public function setSourceId( string $source_id ): void
	{
		$this->source_id = $source_id;
	}
	
	
	public function actualizeProduct(): void
	{
		$_rank = static::dataFetchCol(
			select: ['rank'],
			where: [
				'product_id' => $this->product_id,
				'AND',
				'approved'   => true
			]
		);
		
		$count = count( $_rank );
		$rank = 0;
		
		if( $count ) {
			foreach( $_rank as $r ) {
				$rank += $r;
			}
			
			$rank = round( $rank / $count );
		}
		
		Product::updateReviews(
			$this->product_id,
			$count,
			$rank
		);
		
		$product = Product::load( $this->product_id );
		if(
			$product &&
			$product->isVariant()
		) {
			foreach( $product->getVariants() as $variant ) {
				Product::updateReviews(
					$variant->getId(),
					$count,
					$rank
				);
			}
		}
		
	}
	
	/**
	 * @param Product_EShopData $product
	 *
	 * @return static[]
	 */
	public static function getReviews( Product_EShopData $product ): array
	{
		$p_id = $product->getId();
		if($product->isVariant()) {
			$p_id = $product->getVariantMasterProductId();
		}
		
		$reviews = static::fetch(['product_reviews'=>[
			'product_id' => $p_id,
			'AND',
			'approved' => true
		]],
			order_by: ['-id']
		);
		
		return $reviews;
	}

	public function getAdminTitle() : string
	{
		$product = Product::get( $this->product_id );
		$title = $product?->getAdminTitle()??$this->product_id;
		$title .= ' / ';
		$title .= $this->author_name;
		
		return $title;
	}
	
	public function approve() : void
	{
		$this->assessed = true;
		$this->assessed_date_time = Data_DateTime::now();
		$this->approved = true;
		$this->approved_date_time = Data_DateTime::now();
		$this->save();
		$this->actualizeProduct();
	}
	
	
	public function reject() : void
	{
		$this->assessed = true;
		$this->assessed_date_time = Data_DateTime::now();
		$this->approved = false;
		$this->approved_date_time = null;
		$this->save();
		$this->actualizeProduct();
	}
	
}