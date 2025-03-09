<?php /** @noinspection PhpUndefinedClassInspection */

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\PDFGenerator;

use JetApplication\PDF;
use JetApplication\PDF_Generator;
use Jet\Locale;
use TCPDF;


class Main extends PDF_Generator
{
	
	/** @noinspection PhpMethodParametersCountMismatchInspection */
	public function generate( PDF $pdf ): string
	{
		require 'TCPDF/tcpdf.php';
		
		$eshop = $pdf->getEshop();
		

		$current_locale = Locale::getCurrentLocale();
		Locale::setCurrentLocale( $eshop->getLocale() );
		
		$tcpdf = new class(
			orientation: 'P',
			unit: 'mm',
			format: 'A4',
			unicode: true,
			encoding: 'UTF-8'
		) extends TCPDF {
			protected PDF $pdf;
			
			public function setPdf( PDF $pdf ): void
			{
				$this->pdf = $pdf;
			}
			
			public function Header() {
			}
			
			public function Footer() : void  {
				$this->SetY(-15);
				$this->SetFont('dejavusans', '', 7);
				$this->setDrawColor( 50, 50, 50 );
				$this->Cell(0, 6, '', "B1", false, 'L', 0, '', 0, false, 'T', 'M');
				$this->Ln();
				

				$this->Cell(0, 6, '  '.$this->pdf->getTemplateFooter().'        '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
			}
		};
		
		$tcpdf->setPdf( $pdf );
		
		$tcpdf->AddPage();
		
		$tcpdf->setFont( 'dejavusans' );
		$tcpdf->setCellHeightRatio( 1.3 );
		
		
		$tcpdf->writeHTML( $pdf->getTemplateHtml() );
		
		$output = $tcpdf->Output('', 'S');
		
		Locale::setCurrentLocale( $current_locale );
		
		return $output;

	}
}