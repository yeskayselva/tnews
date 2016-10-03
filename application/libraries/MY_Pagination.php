<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
  
class MY_Pagination extends CI_Pagination {

    public function __construct()
    {
    	
        parent::__construct();
        
    }
    public function pagination_link($url,$tot,$uri_segment,$limit = NULL){
    	
    	if($limit!=NULL)
    	$config["per_page"]    = $limit;
    	else
    	$config["per_page"]    = 10;
		$config["base_url"]    = $url;
		
		$config['num_links']   = 1;
		
		$config['uri_segment'] = $uri_segment;
		
		$config["total_rows"]  = $tot;		
		
		$this->initialize($config);
		
		return $this->create_links();
		
	}

	public function pagination_link_Date($url,$tot,$uri_segment,$limit = NULL){
    	
    	if($limit!=NULL)
    	$config["per_page"]    = $limit;
    	else
    	$config["per_page"]    = 10;
		$config["base_url"]    = $url;		
		
		$config['num_links']   = 1;
		
		$config['uri_segment'] = $uri_segment;
		
		$config["total_rows"]  = $tot;
		
		$config["first_url"]  = $url.'/0?' . http_build_query($_GET, '', "&");	

		$config['suffix'] = '?' . http_build_query($_GET, '', "&");		
		
		$this->initialize($config);
		
		return $this->create_links();
		
	}
}