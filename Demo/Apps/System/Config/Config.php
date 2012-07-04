<?php
return array(
	'routes'=>array(
		array(
			'rule'     => '/:app/:c/*',
			'type'     => 'Syx_Route_Route',
			'defaults' => array(
				'a' => 'index'
			),
			'reqs'     => array('c' => '\w+')
		),
		'normal' => array(
			'rule' => '/:app/:c/:a/*',
			'type' => 'Syx_Route_Route'
		)
	)
);