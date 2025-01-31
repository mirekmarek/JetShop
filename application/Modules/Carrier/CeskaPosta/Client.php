<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\CeskaPosta;


use Jet\Exception;
use Jet\Locale;
use JetApplication\Carrier_DeliveryPoint;
use SimpleXMLElement;

class Client {
	
	protected Main $carrier;
	
	public function __construct( Main $carrier )
	{
		$this->carrier = $carrier;
	}
	
	public function downloadUpToDateDeliveryPointsList(): array
	{
		
		return array_merge(
			$this->downloadUpToDateDeliveryPointsList_NaPostu(),
			$this->downloadUpToDateDeliveryPointsList_Balikovna()
		
		);
	}
	
	public function downloadUpToDateDeliveryPointsList_NaPostu() : array
	{
		$XML_URL = $this->carrier->getConfig()->getXMLURLPosta();
		$data = simplexml_load_file( $XML_URL );
		
		if(!$data) {
			throw new Exception('Unable to load and parse '.$XML_URL);
			return  [];
		}
		
		$list = [];
		
		foreach( $data as $item ) {
			/**
			 * @var SimpleXMLElement $item
			 */
			if( !(string)$item->PSC ) {
				continue;
			}
			
			$point = new Carrier_DeliveryPoint();
			
			$point->setCarrier( $this->carrier );
			$point->setPointLocale( new Locale('cs_CZ') );
			$point->setPointType( 'posta' );
			
			$point->setPointCode( (string)$item->PSC );
			
			[$latitude, $longitude] = static::JTSK2WGS( (float)$item->SOUR_X, (float)$item->SOUR_Y );
			
			[ $street ] = explode(',', $item->ADRESA);
			
			$point->setLatitude( $latitude );
			$point->setLongitude( $longitude );
			
			$point->setZip( (string)$item->PSC );
			$point->setName( (string)$item->NAZEV );
			$point->setStreet( $street );
			$point->setTown( (string)$item->OBEC );
			
			
			
			foreach( $item->OTEV_DOB as $oph_item ) {
				foreach( $oph_item->den as $oph_day ) {
					
					$day = '';
					foreach( $oph_day->attributes() as $v ) {
						$day = (string)$v;
					}
					
					$point->addOpeningHours(
						$day,
						(string)$oph_day->od_do->od,
						(string)$oph_day->od_do->do
					);
				}
			}
			
			$list[] = $point;
		}
		
		
		return $list;
		
	}
	
	
	public function downloadUpToDateDeliveryPointsList_Balikovna() : array
	{
		
		$XML_URL = $this->carrier->getConfig()->getXMLURLBalikovna();
		$data = simplexml_load_file( $XML_URL );
		
		if(!$data) {
			throw new Exception('Unable to load and parse '.$XML_URL);
			return  [];
		}
		
		$list = [];
		
		foreach( $data as $item ) {
			/**
			 * @var SimpleXMLElement $item
			 */
			if( !(string)$item->PSC ) {
				continue;
			}
			
			$point = new Carrier_DeliveryPoint();
			
			$point->setCarrier( $this->carrier );
			$point->setPointLocale( new Locale('cs_CZ') );
			
			$point->setPointType( 'balikovna' );
			//$point->setPointType( (string)$item->TYP );
			$point->setPointCode( (string)$item->PSC );
			
			
			[$latitude, $longitude] = static::JTSK2WGS( (float)$item->SOUR_X, (float)$item->SOUR_Y );
			
			[ $street ] = explode(',', $item->ADRESA);
			
			$point->setLatitude( $latitude );
			$point->setLongitude( $longitude );
			
			$point->setZip( (string)$item->PSC );
			$point->setName( (string)$item->NAZEV );
			$point->setStreet( $street );
			$point->setTown( (string)$item->OBEC );
			
			
			foreach( $item->OTEV_DOBY as $oph_item ) {
				foreach( $oph_item->den as $oph_day ) {
					
					$day = '';
					foreach( $oph_day->attributes() as $k=>$v ) {
						$day = (string)$v;
					}
					
					$point->addOpeningHours(
						$day,
						(string)$oph_day->od_do->od,
						(string)$oph_day->od_do->do
					);
				}
			}
			
			$list[] = $point;
		}
		
		
		return $list;
		
	}
	
	
	
	protected static function JTSK2WGS( float $x, float $y, int $h = 200 ) : array
	{
		$a = 6377397.15508;
		$e = 0.081696831215303;
		$n = 0.97992470462083;
		
		$u_ro = 12310230.12797036;
		$sin_UQ = 0.863499969506341;
		$cos_UQ = 0.504348889819882;
		$sin_VQ = 0.420215144586493;
		$cos_VQ = 0.907424504992097;
		$alfa = 1.000597498371542;
		$k = 1.003419163966575;
		$ro = sqrt( $x * $x + $y * $y );
		$epsilon = 2 * atan( $y / ( $ro + $x ) );
		$D = $epsilon / $n;
		$S = 2 * atan( exp( 1 / $n * log( $u_ro / $ro ) ) ) - M_PI_2;
		$sinS = sin( $S );
		$cosS = cos( $S );
		$sinU = $sin_UQ * $sinS - $cos_UQ * $cosS * cos( $D );
		$cosU = sqrt( 1 - $sinU * $sinU );
		$sinDV = sin( $D ) * $cosS / $cosU;
		$cosDV = sqrt( 1 - $sinDV * $sinDV );
		$sinV = $sin_VQ * $cosDV - $cos_VQ * $sinDV;
		$cosV = $cos_VQ * $cosDV + $sin_VQ * $sinDV;
		$l_jtsk = 2 * atan( $sinV / ( 1 + $cosV ) ) / $alfa;
		$t = exp( 2 / $alfa * log( ( 1 + $sinU ) / $cosU / $k ) );
		$pom = ( $t - 1 ) / ( $t + 1 );
		do {
			$sinB = $pom;
			$pom = $t * exp( $e * log( ( 1 + $e * $sinB ) / ( 1 - $e * $sinB ) ) );
			$pom = ( $pom - 1 ) / ( $pom + 1 );
		} while( abs( $pom - $sinB ) > 0.000000000000001 );
		
		$b_jtsk = atan( $pom / sqrt( 1 - $pom * $pom ) );
		
		
		$f_1 = 299.152812853;
		$e2 = 1 - ( 1 - 1 / $f_1 ) * ( 1 - 1 / $f_1 );
		$ro = $a / sqrt( 1 - $e2 * sin( $b_jtsk ) * sin( $b_jtsk ) );
		$x = ( $ro + $h ) * cos( $b_jtsk ) * cos( $l_jtsk );
		$y = ( $ro + $h ) * cos( $b_jtsk ) * sin( $l_jtsk );
		$z = ( ( 1 - $e2 ) * $ro + $h ) * sin( $b_jtsk );
		
		$dx = 570.69;
		$dy = 85.69;
		$dz = 462.84;
		$wz = -5.2611 / 3600 * M_PI / 180;
		$wy = -1.58676 / 3600 * M_PI / 180;
		$wx = -4.99821 / 3600 * M_PI / 180;
		$m = 3.543 * pow( 10, -6 );
		$xn = $dx + ( 1 + $m ) * ( $x + $wz * $y - $wy * $z );
		$yn = $dy + ( 1 + $m ) * ( -$wz * $x + $y + $wx * $z );
		$zn = $dz + ( 1 + $m ) * ( $wy * $x - $wx * $y + $z );
		
		$a = 6378137.0;
		$f_1 = 298.257223563;
		$a_b = $f_1 / ( $f_1 - 1 );
		$p = sqrt( $xn * $xn + $yn * $yn );
		$e2 = 1 - ( 1 - 1 / $f_1 ) * ( 1 - 1 / $f_1 );
		$theta = atan( $zn * $a_b / $p );
		$st = sin( $theta );
		$ct = cos( $theta );
		$t = ( $zn + $e2 * $a_b * $a * $st * $st * $st ) / ( $p - $e2 * $a * $ct * $ct * $ct );
		$lat = atan( $t );
		$long = 2 * atan( $yn / ( $p + $xn ) );
		
		
		$lat = $lat / M_PI * 180;
		$long = $long / M_PI * 180;
		
		return [
			$lat,
			$long,
		];
	}
	
	
}