<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\VirtualProductHandler\Vouchers;


use Jet\Data_DateTime;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\Discounts_Code;
use JetApplication\Discounts_Discount;
use JetApplication\EMail_TemplateProvider;
use JetApplication\EShop;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Order_Item_SetItem;
use JetApplication\PDF_TemplateProvider;
use JetApplication\Product_VirtualProductHandler;


class Main extends Product_VirtualProductHandler implements EMail_TemplateProvider, PDF_TemplateProvider
{
	protected static array $all_templates = [
		'b2c_cs_CZ' => [
			'default' => [
				'title' => 'Výchozí',
				'background' => 'cz/default/background.png',
				'pdf_html' => 'cz/default/pdf_html.html',
				'pdf_img' => [
					'x' => 10,
					'y' => 50,
					'w' => 190,
					'h' => 90
				],
				'positions' => [
					'code' => [
						'font' => 'fonts/RBNo3.1-Book.ttf',
						'x' => 500,
						'y' => 590,
						'color' => [0, 0, 0],
						'size' => 45,
					],
					'value' => [
						'font' => 'fonts/RBNo3.1-Bold.ttf',
						'x' => 325,
						'y' => 447,
						'color' => [255, 255, 255],
						'size' => 71,
						
						'x_p_value' => 1000,
						'x_p' => 65
					],
					'number' => [
						'font' => 'fonts/RBNo3.1-Book.ttf',
						'x' => 1520,
						'y' => 50,
						'color' => [0, 0, 0],
						'size' => 17,
					],
				]
			],
		],
		
		
		'b2c_sk_SK' => [
			'default' => [
				'title' => 'Výchozí',
				'background' => 'sk/default/background.png',
				'pdf_html' => 'sk/default/pdf_html.html',
				'pdf_img' => [
					'x' => 10,
					'y' => 50,
					'w' => 190,
					'h' => 90
				],
				'positions' => [
					'code' => [
						'font' => 'fonts/RBNo3.1-Book.ttf',
						'x' => 500,
						'y' => 590,
						'color' => [0, 0, 0],
						'size' => 45,
					],
					'value' => [
						'font' => 'fonts/RBNo3.1-Bold.ttf',
						'x' => 325,
						'y' => 447,
						'color' => [255, 255, 255],
						'size' => 71,
						
						'x_p_value' => 100,
						'x_p' => 60
					
					],
					'number' => [
						'font' => 'fonts/RBNo3.1-Book.ttf',
						'x' => 1520,
						'y' => 50,
						'color' => [0, 0, 0],
						'size' => 17,
					],
				]
			],
		],
	];
	
	protected array $templates;
	
	protected EShop $eshop;
	protected Order $order;
	protected Order_Item|Order_Item_SetItem $item;
	protected int $n;
	
	protected Discounts_Code $generated_code;
	protected string $generated_coupon_image_path;
	protected string $generated_pdf;
	
	protected bool $send = true;
	
	public function dispatchOrder( Order $order, Order_Item|Order_Item_SetItem $item ) : void
	{
		$this->eshop = $order->getEshop();
		$this->order = $order;
		$this->item = $item;
		$this->templates = static::$all_templates[$order->getEshop()->getKey()];
	
		$this->generate();
	}
	
	public function getSend(): bool
	{
		return $this->send;
	}
	
	public function setSend( bool $send ): void
	{
		$this->send = $send;
	}
	
	
	
	public static function getTemplatesDir() : string
	{
		return __DIR__.'/gift_coupons_templates/';
	}
	
	public function getSaveDir()  : string
	{
		$dir = SysConf_Path::getData().'/gift_coupons_generated/';
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create($dir);
		}
		
		return $dir;
	}
	
	public function generate() : void
	{
		$template_id = 'default';
		
		
		for($this->n=0;$this->n<$this->item->getNumberOfUnits();$this->n++){
			
			$generated = GeneratedVouchers::generated(
				$this->order,
				$this->item,
				$this->n
			);
			
			if(!$generated) {
				$this->generated_code = $this->generate_code();
				$this->generate_save();
			} else {
				$this->generated_code = Discounts_Code::load([
					$this->order->getEshop()->getWhere(),
					'AND',
					'code' => $generated->getCouponCode()
				]);
			}
			
			
			
			
			$this->generated_coupon_image_path = $this->generate_image(
				template_id: $template_id,
				value:$this->generated_code->getDiscount(),
				code: $this->generated_code->getCode(),
				number: $this->order->getId().'-'.$this->n,
			);
			
			$this->generated_pdf = $this->generate_PDF();
			
			IO_File::write(static::getSaveDir().$this->order->getId().'-'.$this->n.'_'.$this->generated_code->getCode().'.pdf', $this->generated_pdf);
			
			if($this->send) {
				$this->generate_send();
			}
			
		}
	
	}
	
	public function generate_save() : void
	{
		$rec = new GeneratedVouchers();
		$rec->setEshopKey( $this->eshop->getKey() );
		$rec->setOrderId( $this->order->getId() );
		$rec->setOrderItemId( $this->item->getId() );
		$rec->setN( $this->n );
		$rec->setCouponCode( $this->generated_code->getCode() );
		$rec->setCouponValue( $this->generated_code->getDiscount() );
		$rec->setGeneratedDateTime( Data_DateTime::now() );
		
		$rec->save();
	}
	
	public function generate_code() : Discounts_Code
	{
		return Discounts_Code::generate(
			$this->eshop,
			'',
			6,
			function( Discounts_Code $discounts_code ) {
				$amount = $this->item->getPricePerUnit_WithVat();
				
				$discounts_code->setMinimalOrderAmount( 0 );
				$discounts_code->setNumberOfCodesAvailable( 1 );
				$discounts_code->setDiscountType( Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_AMOUNT );
				$discounts_code->setMinimalOrderAmount( $amount );
				$discounts_code->setDiscount( $amount );
				$discounts_code->setActiveTill( new Data_DateTime( date('Y-m-d 23:59:59', strtotime('+6 months')) ) );
				$discounts_code->setInternalNotes( 'Dárkový poukaz, objednávka '.$this->order->getNumber() );
				
			}
		);
		
	}
	
	
	public function generate_image(
		$template_id,
		$value,
		$code,
		$number
	) : string
	{
		$dir = static::getTemplatesDir();
		$template = $this->templates[$template_id];
		
		$bg = imagecreatefrompng( $dir. $template['background'] );
		
		
		$placeText = function($position, $value, $is_number=false) use ($bg, $template, $dir) {
			$p = $template['positions'][$position];
			
			$x_p = 0;
			if( isset($p['x_p_value']) ) {
				if($value<$p['x_p_value']) {
					$x_p = $p['x_p'];
				}
			}
			
			if($is_number) {
				$value = number_format($value, 0, ',', ' ');
			}
			
			$font = $dir . $p['font'];
			$color = imagecolorallocate($bg, $p['color'][0], $p['color'][1], $p['color'][2]);
			
			imagettftext($bg, $p['size'], 0, $p['x']+$x_p, $p['y'], $color, $font, $value);
		};
		
		
		$placeText('value', $value);
		$placeText('code', $code);
		$placeText('number', $number);
		
		ob_start();
		imagepng( $bg );
		$image = ob_get_contents();
		ob_end_clean();
		
		$coupon_image_path = $this->getSaveDir().$number.'_'.$code.'.png';
		IO_File::write($coupon_image_path, $image);
		
		return $coupon_image_path;
	}
	
	public function generate_PDF() : string
	{
		$pdf = new PDFTemplate();
		
		$pdf->setCouponImagePath( $this->generated_coupon_image_path );
		$pdf->setValidTill( $this->generated_code->getActiveTill() );
		
		return $pdf->generatePDF( $this->order->getEshop() );
	}
	
	public function generate_send() : void
	{
		$template = new EMailTemplate();
		$template->setOrder( $this->order );
		$template->setPdf( $this->generated_pdf );
		
		$email = $template->createEmail( $this->order->getEshop(), false );
		
		$email->setTo( $this->order->getEmail() );
		$email->setSaveHistoryAfterSend(true);
		
		$email->send();
	}
	
	
	
	public function getEMailTemplates(): array
	{
		return [
			new EMailTemplate()
		];
	}
	
	public function getPDFTemplates(): array
	{
		return [
			new PDFTemplate()
		];
	}
}