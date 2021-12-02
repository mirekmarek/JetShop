<?php
namespace JetShop;

use Jet\BaseObject;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;

abstract class Core_Exports_FileGenerator extends BaseObject
{
	protected string $input_charset = 'UTF-8';

	protected string $output_charset = 'UTF-8';

	protected string $target_path = '';

	protected string $tmp_file_path = '';

	public function __construct( string $export_code, Shops_Shop $shop, string $target_file_name )
	{
		$this->target_path = Exports::getRootPath().$shop->getKey().'/'.$target_file_name;

		$this->tmp_file_path = SysConf_Path::getTmp().'export_'.$export_code.'_'.$shop->getKey().'_'.date('YmdHis').'.xml';
	}

	public function getInputCharset(): string
	{
		return $this->input_charset;
	}

	public function setInputCharset( string $input_charset ): void
	{
		$this->input_charset = $input_charset;
	}

	public function getOutputCharset(): string
	{
		return $this->output_charset;
	}

	public function setOutputCharset( string $output_charset ): void
	{
		$this->output_charset = $output_charset;
	}

	public function getTargetPath(): string
	{
		return $this->target_path;
	}

	public function setTargetPath( string $target_path ): void
	{
		$this->target_path = $target_path;
	}

	public function getTmpFilePath(): string
	{
		return $this->tmp_file_path;
	}

	public function setTmpFilePath( string $tmp_file_path ): void
	{
		$this->tmp_file_path = $tmp_file_path;
	}


	public function start() : void
	{
		IO_File::write($this->tmp_file_path, '');
	}

	public function done() : void
	{
		$target_dir = dirname($this->target_path);
		if(!IO_Dir::exists($target_dir)) {
			IO_Dir::create($target_dir);
		}

		IO_File::copy( $this->tmp_file_path, $this->target_path );
		IO_File::delete( $this->tmp_file_path );
	}

	protected function _line( string $line ) : void
	{
		if($this->input_charset!=$this->output_charset) {
			$line = iconv($this->input_charset, $this->output_charset, $line);
		}

		$line .= PHP_EOL;

		IO_File::append( $this->tmp_file_path, $line );
	}



}