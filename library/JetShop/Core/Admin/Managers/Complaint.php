<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Complaint;
use JetApplication\Order;

interface Core_Admin_Managers_Complaint extends Admin_EntityManager_WithEShopRelation_Interface
{
	public function showComplaintStatus( Complaint $complaint ) : string;
	
	public function showOrderComplaints( Order $order ) : void;
}