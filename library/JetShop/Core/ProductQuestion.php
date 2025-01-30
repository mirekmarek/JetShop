<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_ProductQuestions;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShopEntity_Definition;
use JetApplication\Product;
use JetApplication\Product_EShopData;

#[DataModel_Definition(
	name: 'product_questions',
	database_table_name: 'product_questions',
)]
#[EShopEntity_Definition(
	admin_manager_interface: Admin_Managers_ProductQuestions::class
)]
abstract class Core_ProductQuestion extends EShopEntity_WithEShopRelation implements EShopEntity_Admin_Interface, EShopEntity_HasGet_Interface
{
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasGet_Trait;
	
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
		type: DataModel::TYPE_STRING,
		max_len: 9999
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Question:'
	)]
	protected string $question = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $answered = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $assessed_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $display = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $answered_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Answer:'
	)]
	protected string $answer = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $customer_id = 0;
	
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
	
	
	public function getQuestion(): string
	{
		return $this->question;
	}
	
	public function setQuestion( string $question ): void
	{
		$this->question = $question;
	}
	
	public function isAnswered(): bool
	{
		return $this->answered;
	}
	
	public function setAnswered( bool $answered ): void
	{
		$this->answered = $answered;
		if( $this->answered ) {
			$this->assessed_date_time = Data_DateTime::now();
		} else {
			$this->assessed_date_time = null;
		}
	}
	
	public function getAssessedDateTime(): ?Data_DateTime
	{
		return $this->assessed_date_time;
	}
	
	public function isDisplay(): bool
	{
		return $this->display;
	}
	
	public function setDisplay( bool $display ): void
	{
		$this->display = $display;
		if( $this->display ) {
			$this->answered_date_time = Data_DateTime::now();
		} else {
			$this->answered_date_time = null;
		}
		
		$this->actualizeProduct();
	}
	
	public function getAnsweredDateTime(): ?Data_DateTime
	{
		return $this->answered_date_time;
	}
	
	
	public function getAnswer(): string
	{
		return $this->answer;
	}
	
	public function setAnswer( string $answer ): void
	{
		$this->answer = $answer;
	}
	
	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	public function setCustomerId( int $customer_id ): void
	{
		$this->customer_id = $customer_id;
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
		$_questions = static::dataFetchCol(
			select: ['id'],
			where: [
				'product_id' => $this->product_id,
				'AND',
				'display'   => true
			]
		);
		
		$count = count( $_questions );
		Product::updateQuestions(
			$this->product_id,
			$count
		);
		
		$product = Product::load( $this->product_id );
		if(
			$product &&
			$product->isVariant()
		) {
			foreach( $product->getVariants() as $variant ) {
				Product::updateQuestions(
					$variant->getId(),
					$count
				);
			}
		}
		
	}
	
	/**
	 * @param Product_EShopData $product
	 *
	 * @return static[]
	 */
	public static function getQuestions( Product_EShopData $product ): array
	{
		$p_id = $product->getId();
		if($product->isVariant()) {
			$p_id = $product->getVariantMasterProductId();
		}
		
		$reviews = static::fetch(['product_questions'=>[
			'product_id' => $p_id,
			'AND',
			'display' => true
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
	
	public function answerAndDisplay() : void
	{
		$this->answered = true;
		$this->assessed_date_time = Data_DateTime::now();
		$this->display = true;
		$this->answered_date_time = Data_DateTime::now();
		$this->save();
		$this->actualizeProduct();
	}
	
	
	public function answerAndDoNotDisplay() : void
	{
		$this->answered = true;
		$this->assessed_date_time = Data_DateTime::now();
		$this->display = false;
		$this->answered_date_time = null;
		$this->save();
		$this->actualizeProduct();
	}
	
	public function isNotAnswered() : void
	{
		$this->answered = false;
		$this->assessed_date_time = null;
		$this->display = false;
		$this->answered_date_time = null;
		$this->save();
		$this->actualizeProduct();
	}
	
	
}