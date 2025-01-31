<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\BaseObject;

abstract class Core_Invoice_VATOverviewItem extends BaseObject
{
	protected float $vat_rate = 0.0;
	
	protected float $tax_base = 0.0;
	protected float $tax_base_before_correction = 0.0;
	protected float $tax_base_correction = 0.0;
	
	protected float $tax = 0.0;
	protected float $tax_before_correction = 0.0;
	protected float $tax_correction = 0.0;
	

	public function __construct( float $vat_rate )
	{
		$this->vat_rate = $vat_rate;
	}
	
	
	public function getVatRate(): float
	{
		return $this->vat_rate;
	}
	
	public function getTaxBase(): float
	{
		return $this->tax_base;
	}
	
	public function addTaxBase( float $tax_base ): void
	{
		$this->tax_base += $tax_base;
		$this->tax_base_before_correction = $this->tax_base;
	}
	
	public function getTax(): float
	{
		return $this->tax;
	}
	
	public function addTax( float $tax ): void
	{
		$this->tax += $tax;
		$this->tax_before_correction = $this->tax;
	}
	
	public function getTaxBaseBeforeCorrection(): float
	{
		return $this->tax_base_before_correction;
	}
	
	public function getTaxBaseCorrection(): float
	{
		return $this->tax_base_correction;
	}
	
	public function getTaxBeforeCorrection(): float
	{
		return $this->tax_before_correction;
	}
	
	public function getTaxCorrection(): float
	{
		return $this->tax_correction;
	}
	
	public function taxBaseCorrection( float $correction ) : void
	{
		$this->tax_base_correction += $correction;
		$this->tax_base += $correction;
	}
	
	public function taxCorrection( float $correction ) : void
	{
		$this->tax_correction += $correction;
		$this->tax += $correction;
	}
	
	
}