<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Order\Delivery\Methods\PersonalTakeover\CeskaPostaBalikovna;

use JetShop\Delivery_Method_Module_PersonalTakeover;
use JetShop\Delivery_PersonalTakeover_Place;
use JetShop\Shops_Shop;
use SimpleXMLElement;

/**
 *
 */
class Main extends Delivery_Method_Module_PersonalTakeover
{
	protected static string $method_code = 'PersonalTakeover.CeskaPostaBalikovna';


	public function getPlacesList( Shops_Shop $shop ): iterable
	{
		if($shop->getLocale()->toString()!='cs_CZ') {
			return [];
		}

		$data = simplexml_load_file( 'http://napostu.ceskaposta.cz/vystupy/balikovny.xml' );

		if(!$data) {
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


			$place = new Delivery_PersonalTakeover_Place();

			$place->setShop( $shop );
			$place->setPlaceCode( (string)$item->PSC );
			$place->setMethodCode( static::$method_code );

			[$latitude, $longitude] = static::JTSK2WGS( (float)$item->SOUR_X, (float)$item->SOUR_Y );

			[ $street ] = explode(',', $item->ADRESA);

			$place->setLatitude( $latitude );
			$place->setLongitude( $longitude );

			$place->setZip( (string)$item->PSC );
			$place->setName( (string)$item->NAZEV );
			$place->setStreet( $street );
			$place->setTown( (string)$item->OBEC );


			foreach( $item->OTEV_DOBY as $oph_item ) {
				foreach( $oph_item->den as $oph_day ) {

					$day = '';
					foreach( $oph_day->attributes() as $k=>$v ) {
						$day = (string)$v;
					}

					$place->addOpeningHours(
						$day,
						(string)$oph_day->od_do->od,
						(string)$oph_day->od_do->do
					);
				}
			}

			$list[] = $place;
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