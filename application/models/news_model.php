<?php
class News_model extends CI_Model
{

function insert_source_website($post_data)
{
	if(insert_data('news_website',$post_data)){
		echo 'success';
	}else{
		echo 'error';
	}
}


}