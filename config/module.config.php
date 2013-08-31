<?php
return array(
	'service_manager' => array(
        'factories' => array(
            'cloudSearch' => function ($sm ){
            	   
            	    $config = $sm->get('config');
                    $config = $config['Cloudsearchdata'];
                    $cloudsearchobj = new  ZF2Amazoncloudsearch\Plugin\Cloudsearch($config);
                    return $cloudsearchobj;
            }
        )
    ),
);