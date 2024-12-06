<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication\Installer;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Select;
use Jet\UI;
use JetApplication\Currencies;

/**
 *
 */
class Installer_Step_ConfigCurrenciesAndPricelists_Controller extends Installer_Step_Controller
{
	protected string $icon = 'money-bill-wave';
	
	/**
	 * @var string
	 */
	protected string $label = 'Config Currencies and Pricelists';
	
	
	/**
	 *
	 */
	public function main(): void
	{
		$count_of_vat_rates = 5;
		
		$filds = [];
		
		$currencies = Installer::getCurrencies();
		$default_currency = Installer::getDefaultCurrencyCode();
		
		foreach( Installer::getSelectedEshopLocales() as $locale ) {
			$locale_str = $locale->toString();
			
			$f = new Form_Field_Select( 'currency_'.$locale_str, UI::flag($locale).' '.$locale->getName() );
			$f->setDefaultValue( $currencies[$locale_str]??$default_currency );
			$f->setSelectOptions( Currencies::getScope() );
			$f->setFieldValueCatcher( function( string $currency_code ) use ($locale) {
				Installer::setCurrency( $locale, $currency_code );
			} );
			
			$filds[] = $f;
			
			
			
			$vat_rates = Installer::getVATRates( $locale );
			
			if(count($vat_rates)<$count_of_vat_rates) {
				for($c=count($vat_rates);$c<$count_of_vat_rates;$c++) {
					$vat_rates[] = '';
				}
			}
			
			
			foreach($vat_rates as $i=>$rate) {
				$f = new Form_Field_Float('/vat_rate/'.$locale_str.'/'.$i, '');
				$f->setDefaultValue( $rate!==''?$rate:null );
				$filds[] = $f;
			}
			
		}
		
		
		
		
		
		$form = new Form('setup_currency_form', $filds);
		
		
		
		if($form->catch()) {
			foreach( Installer::getSelectedEshopLocales() as $locale ) {
				$vat_rates = [];
				
				foreach($form->getFields() as $f) {
					if(
						!str_starts_with($f->getName(),'/vat_rate/'.$locale->toString().'/') ||
						!$f->hasValue() ||
						$f->getValue()===null
					) {
						continue;
					}
					
					$vat_rates[] = $f->getValue();
				}
				Installer::setVATRates( $locale, $vat_rates );
			}
			
			Installer::goToNext();
		}
		
		$this->view->setVar( 'form', $form );
		
		$this->render( 'default' );
	}
	
}
