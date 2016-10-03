<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_data($tbl,$arr=NULL,$limit=NULL,$offset=NULL){
	$CI =& get_instance();
	$query = $CI->db->get_where($tbl, $arr, $limit, $offset);
	return $query;
}
function select_data($tbl,$arr=NULL,$whr=NULL){
	$CI =& get_instance();
	if ( ! is_null($whr)){
		
		$whr=" WHERE ".implode(' AND ', array_map(function ($v, $k) { return $k . '=' . $v; }, $whr, array_keys($whr)));
	}
	if ( ! is_null($arr)){
		$query = $CI->db->query("select ".implode(',',$arr)." from ".$tbl.$whr);
		return $query;
	}
	
}
function get_table($tbl){
	$CI =& get_instance();
	$query = $CI->db->get($tbl);
	return $query;
}
function insert_data($tbl,$arr=NULL){
	$CI =& get_instance();
	$query = $CI->db->insert($tbl, $arr);
	return $query;
}
function update_data($tbl,$arr=NULL,$upd){
	$CI =& get_instance();
	$query = $CI->db->update($tbl, $arr, $upd);
	return $query;
}
function delete_data($tbl,$arr=NULL){
	$CI =& get_instance();
	$query = $CI->db->delete($tbl,$arr); 
	return $query;
}
function select_box($tbl,$arr=NULL,$whr=NULL){
	
	$CI =& get_instance();
	
	if ( ! is_null($whr)){
		
		$query=select_data($tbl,$arr,$whr);
			
			if($query->num_rows()){
				
				$option="";
				
				foreach($query->result() as $row){
					
					$option.="<option value='".$row->$arr['id']."'> ".$row->$arr['name']."  </option>";	
					
				}
				
				return $option;	
				
			}
		
		
	}else{
			
			$query=select_data($tbl,$arr);
			
			if($query->num_rows()){
				
				$option="";
				
				foreach($query->result() as $row){
					
					$option.="<option value='".$row->$arr['id']."'> ".$row->$arr['name']."  </option>";	
					
				}
				
				return $option;	
				
			}

	}
}

function get_opp_stage_name($id){
	$CI =& get_instance();
	$book_stage = $CI->config->item('customer_io_event');
	return $book_stage[$id];
}


function clean_mobile($mobile){
	
	return substr($mobile, -10);
}