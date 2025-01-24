<?php

namespace JetApplicationModule\Payment\GoPay;

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