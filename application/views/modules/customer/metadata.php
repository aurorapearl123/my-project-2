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
if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Customer')) {
    $submenu[] = array('label'=> 'Customer','url'=>site_url('customer/show'),'icon'=>'bullet','subitem'=>array());
} 


// main module
$module = array();
$module['main']['title'] 		= 'Customer';
$module['main']['controller'] 	= 'Customer';
$module['main']['description'] 	= 'Manage all Customer';
$module['main']['icon'] 		= 'la-group';
$module['main']['roles'] 		= array();

// sub module
$module['sub']['Customer'] = array(
		'title'=> 'Customer',
		'module_label'=> 'Customer',
		'controller'=> 'Customer',
		'description'=>'Manage all Customer',
		'icon'=>'la-group',
		'roles'=>array(   'Add Customer',
		                  'Edit Existing Customer',
		                  'Delete Existing Customer',
						  'View Customer',
						  'Print Customer',
						  'Export Customer'
						)
);
//
//$module['sub']['Branch'] = array(
//    'title'=>'Branches',
//    'module_label'=>'Branch', // this will display on create/view/edit form
//    'controller'=>'branch',
//    'description'=>'Manage all Branches',
//    'icon'=>'la-building',
//    'roles'=>array(   'Add Branch',
//        'View Branch',
//        'Edit Existing Branch',
//        'Delete Existing Branch',
//    )
//);

$module['main']['menu'] = $submenu;
$this->modules['Customer']  = $module;
?>