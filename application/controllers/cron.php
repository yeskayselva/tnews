<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {
	
	

	/**
	
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    
        function __construct()
	{
            parent::__construct();

        }
        
        /*
         * Each story has unique id this api get all store id
         * Each store listed by different news website
         * We fetch first listed news & source website info 
         * news_website table is common it's store all catagory of website 
         * if source_table = 2 -google_trend_news,1=google_hotTrend_news, In this api we add google_trend_news only
         */
	public function google_trends()
	{
		$this->load->library('curl');
		$this->load->model('news_model');
		//latest?hl=en-US&tz=-330&cat=all&fi=15&fs=15&geo=IN&ri=300&rs=15&sort=0
		//$google_response = $this->curl->simple_get('https://www.google.co.in/trends/api/stories/latest?hl=en-US&tz=-330&cat=all&fi=15&fs=15&geo=IN&ri=300&rs=15&sort=0');
		//Each story has unique id this api get all store id
                $google_response = file_get_contents('https://www.google.co.in/trends/api/stories/latest?hl=en-US&tz=-330&cat=all&fi=15&fs=15&geo=IN&ri=300&rs=15&sort=0');
		// $google_response is string substr Clean string into json 
                $sub_str_json = substr($google_response, 5);
		$array_format = json_decode($sub_str_json);
		$trendingStoryIds = $array_format->trendingStoryIds;
		$j = 1;
		//foreach($array_format->storySummaries->trendingStories as $key => $value){
			//var_dump($value);
			//echo $i++." - ".$image = $value->image->imgUrl;
			//echo $value->articles[0]->articleTitle;
			//echo $value->articles[0]->source."<br>";
			//exit;
	//	}
//exit;
//preg_match_all("/\[([^\]]*)\]/", $google_response, $matches);
//$google_api_id = json_decode($matches[0][1]);
	for($i = 0;$i < sizeof($trendingStoryIds);$i++){
		$google_detail_response = file_get_contents('https://www.google.co.in/trends/api/stories/'.$trendingStoryIds[$i].'?hl=en-US&tz=-330&sw=10');
		$remove_substr = substr($google_detail_response, 5);
		$stories = json_decode($remove_substr);
		//echo $stories->title;
                // In list of source we take only top list
		if(isset($stories->widgets[0]->articles[0])){
			$imageUrl = $stories->widgets[0]->articles[0]->imageUrl;
			$title =  $stories->widgets[0]->articles[0]->title."<br>";
			$source =  $stories->widgets[0]->articles[0]->source;
			$url = $stories->widgets[0]->articles[0]->url;
			$time = $stories->widgets[0]->articles[0]->time."<br>";	
		}
		// Check this condition this news already avail or not
		$chck_insert = get_data('news_website',array('url'=>$url))->num_rows();
		if($chck_insert == 0){
			insert_data('google_trend_news',array('source_site_name'=>$source,'news_title'=>addslashes($title),'source_url_link'=>$url,'img_url'=>$imageUrl,'created_date'=>date("Y-m-d H:i:s")));
			$last_insert_id = $this->db->insert_id();
			$news_website = array('news_title'=>addslashes($title),'source_from'=>2,'trend_news_id'=>$last_insert_id,'url'=>$url,'source_site_name'=>$source,'created_date'=>date("Y-m-d H:i:s"));
			echo $j++."<br>";
                        // We have added source webstie detail by below line
			$this->news_model->insert_source_website($news_website);	
		}
	
		//exit;
	}

}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
