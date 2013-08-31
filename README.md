ZF2 module for amazon cloud Search 
==============

How we can use this 
--------------

- add a  'zf2/amazoncloudsearch' : 'dev'  in composer.json file 
- now excute composer install
- add Module in you application.config.php file

* How you can use this in your project *

	
	$cloudobj = $this->getServiceLocator()->get('cloudSearch');
         
         $data    = array(
            'name of field on cloud search' 
        );

         $result = $cloudobj->search('query',$data );

	

