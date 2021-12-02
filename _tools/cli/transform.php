<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace Jet;

require 'init/init.php';

$readDir = null;


$transformPages = function($path, $manifest_data) {
	//var_dump($path, $manifest_data);
	$pages_dir = $path.SysConf_Jet_Modules::getPagesDir().'/';
	if(!IO_Dir::exists($pages_dir)) {
		IO_Dir::create($pages_dir);;
	}

	foreach($manifest_data['pages'] as $base_id=>$pages) {
		foreach($pages as $page_id=>$page_data) {
			$relative_path_fragment = $page_data['relative_path_fragment'];
			unset($page_data['relative_path_fragment']);

			$page_data['id'] = $page_id;


			$page_data_dir_path = $path.SysConf_Jet_Modules::getPagesDir().'/'.$base_id.'/'.$relative_path_fragment.'/';
			if(!IO_Dir::exists($page_data_dir_path)) {
				IO_Dir::create($page_data_dir_path);
			}

			$page_data_path = $page_data_dir_path.SysConf_Jet_MVC::getPageDataFileName();
			IO_File::writeDataAsPhp($page_data_path, $page_data);


		}
	}
};

$transformMenuItems = function($path, $manifest_data) {
	//var_dump($path, $manifest_data);

	foreach($manifest_data['menu_items'] as $set_id=>$items) {
		$target_file = $path.SysConf_Jet_Modules::getMenuItemsDir().'/'.$set_id.'.php';

		IO_File::writeDataAsPhp( $target_file, $items );
	}
};


$readDir = function($path) use (&$readDir, $transformPages, $transformMenuItems) {
	$dirs = IO_Dir::getSubdirectoriesList($path);

	foreach($dirs as $path=>$name) {
		$manifest_file_path = $path.SysConf_Jet_Modules::getManifestFileName();

		if(IO_File::isReadable($manifest_file_path)) {
			$manifest_data = require $manifest_file_path;


			if(isset($manifest_data['pages'])) {
				$transformPages($path, $manifest_data);

				unset($manifest_data['pages']);
				IO_File::writeDataAsPhp( $manifest_file_path, $manifest_data );
			}

			if(isset($manifest_data['menu_items'])) {
				$transformMenuItems($path, $manifest_data);

				unset($manifest_data['menu_items']);
				IO_File::writeDataAsPhp( $manifest_file_path, $manifest_data );
			}

		}

		$readDir($path);
	}
};

$readDir( SysConf_Path::getModules() );