<?php

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetShop;

use JetApplication\EShop;
use JetApplication\Managers_General;
use JetApplication\PDF_TemplateText_EShopData;

abstract class Core_PDF {
	protected EShop $eshop;
	
	protected PDF_TemplateText_EShopData $template;
	
	protected string $template_code = '';
	
	protected string $template_html = '';
	
	protected string $template_header = '';
	protected string $template_footer = '';
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function setEshop( EShop $eshop ): void
	{
		$this->eshop = $eshop;
	}
	
	public function getTemplateCode(): string
	{
		return $this->template_code;
	}
	
	public function setTemplateCode( string $template_code ): void
	{
		$this->template_code = $template_code;
	}
	
	public function getTemplate(): PDF_TemplateText_EShopData
	{
		return $this->template;
	}
	
	public function setTemplate( PDF_TemplateText_EShopData $template ): void
	{
		$this->template = $template;
	}
	
	public function setTemplateHtml( string $template_html ): void
	{
		$this->template_html = $template_html;
	}
	
	public function getTemplateHtml(): string
	{
		return $this->template_html;
	}
	
	public function getTemplateHeader(): string
	{
		return $this->template_header;
	}
	
	public function setTemplateHeader( string $template_header ): void
	{
		$this->template_header = $template_header;
	}
	
	public function getTemplateFooter(): string
	{
		return $this->template_footer;
	}
	
	public function setTemplateFooter( string $template_footer ): void
	{
		$this->template_footer = $template_footer;
	}
	
	
	
	public function generatePDF() : string
	{
		return Managers_General::PDFGenerator()->generate( $this );
	}
}