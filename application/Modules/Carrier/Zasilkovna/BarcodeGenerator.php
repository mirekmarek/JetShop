<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Carrier\Zasilkovna;



use GdImage;

class BarcodeGenerator
{
	public const CODES = array(
		212222, 222122, 222221, 121223, 121322, 131222, 122213, 122312, 132212, 221213, 221312, 231212, 112232, 122132,
		122231, 113222, 123122, 123221, 223211, 221132, 221231, 213212, 223112, 312131, 311222, 321122, 321221, 312212,
		322112, 322211, 212123, 212321, 232121, 111323, 131123, 131321, 112313, 132113, 132311, 211313, 231113, 231311,
		112133, 112331, 132131, 113123, 113321, 133121, 313121, 211331, 231131, 213113, 213311, 213131, 311123, 311321,
		331121, 312113, 312311, 332111, 314111, 221411, 431111, 111224, 111422, 121124, 121421, 141122, 141221, 112214,
		112412, 122114, 122411, 142112, 142211, 241211, 221114, 413111, 241112, 134111, 111242, 121142, 121241, 114212,
		124112, 124211, 411212, 421112, 421211, 212141, 214121, 412121, 111143, 111341, 131141, 114113, 114311, 411113,
		411311, 113141, 114131, 311141, 411131, 211412, 211214, 211232, 23311120
	);
	
	public const START = 103;
	public const STOP = 106;
	
	/**
	 * @param string $code
	 * @param int $density
	 * @param int $dpi
	 * @return GdImage|resource
	 */
	public static function generate(string $code, int $density = 1, int $dpi = 72)
	{
		
		$width = (((11 * strlen($code)) + 35) * ($density / $dpi));
		$height = ($width * .15 > .7) ? $width * .15 : .7;
		
		$width = round($width * $dpi);
		$height = round($height * $dpi);
		
		
		$image = imagecreatetruecolor($width, $height);
		
		
		imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255) );
		imagesetthickness($image, $density);
		
		$checksum = self::START;
		$encoding = array(self::CODES[self::START]);
		
		for ($i = 0; $i < strlen($code); $i++) {
			
			$checksum += (ord(substr($code, $i, 1)) - 32) * ($i + 1);
			
			$encoding[] = self::CODES[(ord( substr( $code, $i, 1 ) )) - 32];
		}
		
		$encoding[] = self::CODES[$checksum % 103];
		$encoding[] = self::CODES[self::STOP];
		
		$enc_str = implode($encoding);
		
		for ($i = 0, $x = 0, $inc = round(($density / $dpi) * 100); $i < strlen($enc_str); $i++) {
			
			$val = intval(substr($enc_str, $i, 1));
			
			$black = imagecolorallocate($image, 0, 0, 0);
			for ($n = 0; $n < $val; $n++, $x += $inc) {
				if ($i % 2 == 0) {
					imageline($image, $x, 0, $x, $height, $black);
				}
			}
			
		}
		
		return $image;
	}
}