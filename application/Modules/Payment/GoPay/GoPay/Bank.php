<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Payment\GoPay;


use Jet\IO_File;

class GoPay_Bank {
	public static function getList(): array
	{
		$list = IO_File::read( __DIR__.'/banks.csv' );
		
		$list = explode("\n", $list);
		
		$res = [];
		foreach($list as $line) {
			$line = trim($line);
			$line = explode(';', $line);
			
			$res[$line[0]] = $line[1];
		}
		
		return $res;
	}
	
}