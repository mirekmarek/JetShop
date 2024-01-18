<?php
namespace JetApplication;


interface Admin_Entity_Common_Manager_Interface {
	public static function getName( int $id ) : string;
	
	public static function showName( int $id ) : string;
	
	public static function showActiveState( int $id ): string;
	
	public static function getEditUrl( int $id, array $get_params=[] ) : string;
	
	public static function getCurrentUserCanEdit() : bool;
	
	public static function getCurrentUserCanCreate() : bool;
	
	public static function getCurrentUserCanDelete() : bool;
	
	public static function getEntityInstance(): Entity_Basic|Admin_Entity_Common_Interface;
	
	public static function getEntityNameReadable() : string;
	
}