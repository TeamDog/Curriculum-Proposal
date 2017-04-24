<?php
$EXT_CONF['rowan'] = array(
	'title' => 'Rowan CPMS',
	'description' => 'CURRENTLY DISABLED --- Rowan CPMS Custom Files',
	'disable' => true, //WIP, not used currently, may be usful in future
	'version' => '1.0.0',
	'releasedate' => '2017-03-20',
	'author' => array('name'=>'Justin Gavin', 'email'=>'contact@justingavin.com', 'company'=>'Rowan University '),
	'config' => array(
		/*
		'input_field' => array(
			'title'=>'Example input field',
			'type'=>'input',
			'size'=>20,
		),
		'checkbox' => array(
			'title'=>'Example check box',
			'type'=>'checkbox',
		),
		*/
	),
	'constraints' => array(
		'depends' => array('php' => '5.4.4-', 'seeddms' => '4.3.0-'),
	),
	'icon' => 'icon.png',
	'class' => array(
		'file' => 'class.RowanCPMS.php',
		'name' => 'SeedDMS_RowanCPMS'
	),
	'views' => array(
		'RowanDashboard' => array(
								  'name' => 'Dashboard',
								  //'seeddms' => '4.3.0-'
								  ),

	),
	/*
	'language' => array(
		'file' => 'lang.php',
	),
	*/
);
?>
