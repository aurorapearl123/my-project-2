<?php 
/**
 * File: metadata.php
 * Description: Defination of this module and its roles and sub-modules
 * 
 * Example of sub module
 * 
 * $module['sub'][] = array(
		'title'=>'Item Category',
		'description'=>'Manage all order categories in the commissary',
		'icon'=>'category.png',
		'roles'=>array('Add','View','Edit','Delete')
	);
 */
 
$submenu = array();
if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Order')) {
    $submenu[] = array('label'=>'Order','url'=>site_url('order/show'),'icon'=>'bullet','subitem'=>array());
} 

// if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Complaint')) {
//     $submenu[] = array('label'=>'Complaints','url'=>site_url('complaint/show'),'icon'=>'bullet','subitem'=>array());
// }

// main module
$module = array();
$module['main']['title'] 		= 'Order';
$module['main']['controller'] 	= 'Order';
$module['main']['description'] 	= 'Manage all Order';
$module['main']['icon'] 		= 'la-cart-arrow-down';
$module['main']['roles'] 		= array();

// sub module
$module['sub']['Order'] = array(
		'title'=> 'Order',
		'module_label'=> 'Order',
		'controller'=> 'Order',
		'description'=>'Manage all Order',
		'icon'=>'la-cart-arrow-down',
		'roles'=>array(   'Add Order',
		                  'Edit Existing Order',
		                  'Delete Existing Order',
						  'View Order',
						  'Print Order',
						  'Export Order'
						)
);

// $module['sub']['Complaints'] = array(
//     'title'=>'Complaints',
//     'module_label'=>'Complaints', // this will display on create/view/edit form
//     'controller'=>'complaint',
//     'description'=>'Manage all Complaints',
//     'icon'=>'la-folder',
//     'roles'=>array(     'Add Complaints',
//                         'View Complaints',
//                         'Edit Existing Complaints',
//                         'Delete Existing Complaints',
//     )
// );



//}
$module['main']['menu'] = $submenu;
$this->modules['Order']  = $module;
?>