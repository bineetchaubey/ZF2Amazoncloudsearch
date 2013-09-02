<?php
namespace ZF2Amazoncloudsearch\plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;


class Cloudsearch extends AbstractPlugin
{
    public $search_domain;
    public $domain_id;
    public $search_host;
    Public $search_region;
    public $document_endpoint;
    public $search_endpoint;
    public $http_code = 200;
    public $calendar_method = "2011-02-01";
    public $availableTypes = array("update", "add", "delete");

    public function __construct($config)
    {
      
        $this->search_domain =  $config['search_domain'] ;
        $this->domain_id =  $config['domain_id'] ;
        $this->search_region = $config['search_region'] ;
        $this->search_host = "http://doc-".$this->search_domain."-".$this->domain_id.".".$this->search_region;
        $this->document_endpoint = "http://doc-".$this->search_domain."-".$this->domain_id.".".$this->search_region."/".$this->calendar_method;
        $this->search_endpoint = "http://search-".$this->search_domain."-".$this->domain_id.".".$this->search_region."/".$this->calendar_method;
    }
      

  /**
   * Public document API call
   *
   * @param $type - add, deete, $params - the ranking algorithms or other variables
   */

    public function add($type, $params = array())
    {
        if (in_array($type, $this->availableTypes)) {
            return $this->call($this->document_endpoint ."/documents/batch", "POST",json_encode($params));
        }
        else {
            // perform error
            echo 'error';exit;
        }
    } 

    /**
   * Public document API call to upload file
   *
   * @param $type - add, deete, $params - the ranking algorithms or other variables
   */

    public function Upload_xml($url=null, $method='POST', $filepath)
    {
         //return $this->call($this->document_endpoint ."/documents/batch", "POST",json_encode($params));
         if(file_exists($filepath)){
         $url = $this->document_endpoint ."/documents/batch";
         $curl2 = curl_init();
		 $fp = fopen($filepath, 'r');
        if ($method == "POST")
        {
            curl_setopt($curl2, CURLOPT_POST, true);
            curl_setopt($curl2, CURLOPT_POSTFIELDS,'');
            curl_setopt($curl2, CURLOPT_INFILE, $fp);
            curl_setopt($curl2, CURLOPT_NOPROGRESS, false);
      			//curl_setopt($curl2, CURLOPT_PROGRESSFUNCTION, 'CURL_callback');
      			curl_setopt($curl2, CURLOPT_BUFFERSIZE, 128);
      			curl_setopt($curl2, CURLOPT_INFILESIZE, filesize($filepath));
            curl_setopt($curl2, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json')                                                                   
            );
            
        }

        curl_setopt($curl2, CURLOPT_URL, $url);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl2);
        $HttpCode = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
        $this->http_code = (int)$HttpCode;
        return $result;
        }else{
        	echo 'File is not found';exit;
        }
    } 

  /**
   * Public search API call
   *
   * @param $term - the search term, $params - the ranking algorithms or other variables
   */

    public function search($term, $return_fields = array(),$return_type="")
    {
        if (sizeof($return_fields) == 0) {
            return $this->call($this->search_endpoint ."/search?q=".urlencode($term), "GET", array());
        }
        else {
            return $this->call($this->search_endpoint ."/search?q='".urlencode($term)."'&return-fields=".implode(',',$return_fields), "GET", array());
        }
    }
    
   /**
   * Public search for custom and deeply search API call
   *
   * @param $term - the search term, $params - the ranking algorithms or other variables
   */

    public function searchbq ($keyword,$term, $return_fields = array(),$return_type="",$rank="price",$size=10,$start=0,$distance="nolocalsearch")
    {
        if (sizeof($return_fields) == 0) {
            return $this->call($this->search_endpoint ."/search?bq=".urlencode($term), "GET", array());
        }
        else {
        	
        	/**
        	* For Amazon cloud Search user search url like
        	*
        	*  <searchpointurl>/search?bg=(and <key-name1>:'value' <key-name2>:'value' )
        	* 
        	*   <searchpointurl>/search?bg=(and <key-name1>:'value' (or <key-name2>:'value2' <key-name2>:'value3') )
        	*
        	* 	for custom rank expesstion you are doing this in two way 
        	* 
        	*   1.  Make and new rank expestion in aws cloussearch console admin panel and define expesstion 
        	*		ie mathmatical expession and in search url simplay aapend that expression in url like
        	*       &rank=geo   // geo rank expession should be define on cloudsearch console admin panel
        	* 
        	*   2.  Second  way is include rank name and expession in dynamic way like that 
        	*		&rank=geo&rank-geo=urlencode(mathmatical expession)
        	*       in this way no need to make a rank expression on amazon cloudSearch console admin panel 
        	*   
        	*/
        	
        	/*$metersperdegreelatitude = $locationLat*111133;
            $metersperdegreelongitude = $metersperdegreelatitude*cos($locationLat);*/
        	
        	
        	if($distance !='nolocalsearch'){    
        		return $this->call($this->search_endpoint ."/search?q=".urlencode($keyword)."&bq=".urlencode($term)."&rank=geo&rank-geo=".urlencode($distance)."&rank=$rank&size=$size&start=$start&return-fields=".implode(',',$return_fields), "GET", array());
	
        	}
            return $this->call($this->search_endpoint ."/search?q=".urlencode($keyword)."&bq=".urlencode($term)."&rank=$rank&size=$size&start=$start&return-fields=".implode(',',$return_fields), "GET", array());
                      
        }
    }
    
    
     /**
   * Public search for custom and deeply search API call
   *
   * @param $term - the search term, $params - the ranking algorithms or other variables
   */

    public function searchWithNameAndCode($term, $return_fields = array(),$return_type="",$rank="price",$size=10,$start=0,$distance="nolocalsearch")
    {
        if (sizeof($return_fields) == 0) {
            return $this->call($this->search_endpoint ."/search?bq=".urlencode($term), "GET", array());
        }
        else {
        	
        	/**
        	* For Amazon cloud Search user search url like
        	*
        	*  <searchpointurl>/search?bg=(and <key-name1>:'value' <key-name2>:'value' )
        	* 
        	*   <searchpointurl>/search?bg=(and <key-name1>:'value' (or <key-name2>:'value2' <key-name2>:'value3') )
        	*
        	* 	for custom rank expesstion you are doing this in two way 
        	* 
        	*   1.  Make and new rank expestion in aws cloussearch console admin panel and define expesstion 
        	*		ie mathmatical expession and in search url simplay aapend that expression in url like
        	*       &rank=geo   // geo rank expession should be define on cloudsearch console admin panel
        	* 
        	*   2.  Second  way is include rank name and expession in dynamic way like that 
        	*		&rank=geo&rank-geo=urlencode(mathmatical expession)
        	*       in this way no need to make a rank expression on amazon cloudSearch console admin panel 
        	*   
        	*/
        	
        	/*$metersperdegreelatitude = $locationLat*111133;
            $metersperdegreelongitude = $metersperdegreelatitude*cos($locationLat);*/
        	
        	
        	if($distance !='nolocalsearch'){     		
        		return $this->call($this->search_endpoint ."/search?bq=".urlencode($term)."&rank=$rank&rank=geo&rank-geo=".urlencode($distance)."&size=$size&start=$start&return-fields=".implode(',',$return_fields), "GET", array());
	
        	}
            return $this->call($this->search_endpoint ."/search?bq=".urlencode($term)."&rank=$rank&size=$size&start=$start&return-fields=".implode(',',$return_fields), "GET", array());
                      
        }
    }
    
    
    
    
    
   /**
   * Public search single result with docId API call
   *
   * @param $term - the search term, $params - the ranking algorithms or other variables
   */

    public function searchSingle($term, $return_fields = array(),$return_type="",$size=10,$start=0)
    {
        if (sizeof($return_fields) == 0) {
            return $this->call($this->search_endpoint ."/search?bq=".urlencode($term)."&rank=-addeddate", "GET", array());
        }
        else {
            return $this->call($this->search_endpoint ."/search?bq=".urlencode($term)."&size=$size&start=$start&return-fields=".implode(',',$return_fields)."&rank=-addeddate", "GET", array());
        }
    }
    
     
	
  /**
   * Private function that return the results of a GET or POST call to your domain host.
   *
   * @param $url - url to send to, $method - the GET or POST, $parameters - the params to pass
   */
	
    private function call($url, $method, $parameters) {
        
        $curl2 = curl_init();
        if ($method == "POST")
        {
            curl_setopt($curl2, CURLOPT_POST, true);
            curl_setopt($curl2, CURLOPT_POSTFIELDS, $parameters);
            
            curl_setopt($curl2, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($parameters))                                                                   
            );
        }
        curl_setopt($curl2, CURLOPT_URL, $url);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);	
        $result = curl_exec($curl2);
        $HttpCode = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
        $this->http_code = (int)$HttpCode;
        return $result;
    }  

}