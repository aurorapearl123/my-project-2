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
if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Employee')) {
    $submenu[] = array('label'=>'Physical Count','url'=>site_url('employee/show'),'icon'=>'bullet','subitem'=>array());
} 

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Employment')) {
    $submenu[] = array('label'=>'Employments','url'=>site_url('employment/show'),'icon'=>'bullet','subitem'=>array());
}

// main module
$module = array();
$module['main']['title'] 		= 'Employees';
$module['main']['controller'] 	= 'employee';
$module['main']['description'] 	= 'Manage all Employees';
$module['main']['icon'] 		= 'la-group';
$module['main']['roles'] 		= array();

// sub module
$module['sub']['Employee'] = array(
		'title'=>'Employees',
		'module_label'=>'Employee',
		'controller'=>'employee',
		'description'=>'Manage all Employees',
		'icon'=>'la-group',
		'roles'=>array(   'Add Employee',
		                  'Edit Existing Employee',
		                  'Delete Existing Employee',
						  'View Employee',
						  'Print Employees',
						  'Export Employees'
						)
);

$module['sub']['Family Member'] = array(
		'title'=>'Family Members',
		'module_label'=>'Family Member',
		'controller'=>'family_member',
		'description'=>'Manage all Family Members',
		'icon'=>'la-group',
		'roles'=>array(   'Add Family Member',
		                  'Edit Existing Family Member',
		                  'Delete Existing Family Member',
						  'View Family Member',
						)
);

$module['sub']['Education Background'] = array(
		'title'=>'Education Background',
		'module_label'=>'Education Background',
		'controller'=>'education_background',
		'description'=>'Manage all Education Background',
		'icon'=>'la-group',
		'roles'=>array(   'Add Education Background',
		                  'Edit Existing Education Background',
		                  'Delete Existing Education Background',
						  'View Education Background',
						)
);

$module['sub']['Service Eligibility'] = array(
		'title'=>'Service Eligibilities',
		'module_label'=>'Service Eligibility',
		'controller'=>'service_eligibility',
		'description'=>'Manage all Service Eligibility',
		'icon'=>'la-group',
		'roles'=>array(   'Add Service Eligibility',
		                  'Edit Existing Service Eligibility',
		                  'Delete Existing Service Eligibility',
						  'View Service Eligibility',
						)
);

$module['sub']['Work Experience'] = array(
		'title'=>'Work Experiencies',
		'module_label'=>'Work Experience',
		'controller'=>'work_experience',
		'description'=>'Manage all Work Experience',
		'icon'=>'la-group',
		'roles'=>array(   'Add Work Experience',
		                  'Edit Existing Work Experience',
		                  'Delete Existing Work Experience',
						  'View Work Experience',
						)
);

$module['sub']['Training Program'] = array(
		'title'=>'Training Programs',
		'module_label'=>'Training Program',
		'controller'=>'training_program',
		'description'=>'Manage all Training Programs',
		'icon'=>'la-group',
		'roles'=>array(   'Add Training Program',
		                  'Edit Existing Training Program',
		                  'Delete Existing Training Program',
						  'View Training Program',
						)
);

$module['sub']['Employment'] = array(
    'title'=>'Employments',
    'module_label' => 'Employment',
    'controller'=>'employment',
    'description'=>'Manage all Employments',
    'icon'=>'la-suitcase',
    'roles'=>array( 'Add Employment',
                    'Edit Existing Employment',
                    'Delete Existing Employment',
                    'View Employment',
                    'Promote Employment',
                    'Demote Employment',
                    'Re-Assign Employment',
                    'Terminate Employment',
                    'Print Employments',
                    'Export Employments',
    )
);




//}
$module['main']['menu'] = $submenu;
$this->modules['Employee']  = $module;
?>