<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	

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
	public function index(){
		$data['title'] = 'Home';
		$data['main_content'] = 'home';
		$data['result'] = $this->db->query('select * from trend_news where status = 1 order by id desc limit 70')->result();
		$this->load->view('template/home_template',$data);
	}
        
        // News are scrape from https://trends.google.com/trends/hottrends#pn=p3
	
	public function google_api($date = null)
	{
		if($date == null){
			$date = date("Ymd");
		}
		$this->load->library('curl');
		$google_response = $this->curl->simple_post('https://trends.google.com/trends/hottrends/hotItems', array('ajax'=>'1','htd'=>'','pn'=>'p3','htv'=>'l'));
		$google_api_response = json_decode($google_response);
              // var_dump($google_api_response);
                //exit;
		$google_api_feed = $google_api_response->trendsByDateList;
		// day by day feed
		foreach($google_api_feed as $row){
                    // Get today news only
                    if($date == $row->date){
                       echo $this->get_daily_feed($row->trendsList).'<br>';
                    }
		}
		exit;
	}
	
	
	public function get_daily_feed($feed){
		// per day feed
                $titleInsert = array();
		$insert_row_count = 0;
		$this->load->library('curl');
		foreach(array_reverse($feed) as $feed_row){
			
			$trend_date= $feed_row->date;
			$i = 1;
			$news_website = array();
                        
                        // same news with other source
			foreach($feed_row->newsArticlesList as $row){
				if($i == 1){
					$news_title=$row->title;
					$source_url_link=$row->link;
					$souce_site_name=$row->source;
					$snippet =$row->snippet;
				}
				$news_website[] = array('source_site_name'=>$row->source,'created_date'=>$trend_date);
				$i++;
				//exit;
			}
                        $related_search_keyword = $this->convert_searchresult_tostring($feed_row->relatedSearchesList);
			$img_url = isset($feed_row->imgUrl) ? $feed_row->imgUrl : 'no_image_found.jpg';
			$google_api = array('related_search_keyword'=>$related_search_keyword,
                                'search_keyword'=>$feed_row->title,
                                'traffic'=>$feed_row->trafficBucketLowerBound,
                                'hotnessLevel'=>$feed_row->hotnessLevel,'img_url'=>$img_url,
				 'news_title'=>addslashes($news_title),
                                'source_url_link'=>$source_url_link,
                                'source_site_name'=>$souce_site_name,
                                'snippet'=>addslashes($snippet),
                                'created_date'=>$trend_date);
			$check_row = get_data('google_hotTrend_news',array('search_keyword'=>$feed_row->title,'created_date >='=>date('Ymd'),'deleted'=>0))->num_rows();
			if($check_row == 0 && $trend_date == date('Ymd')){
				$insert_row_count = $insert_row_count + 1;
                                $titleInsert[] = $feed_row->title.'<br>';
				insert_data('google_hotTrend_news',$google_api);
				$last_insert_id = $this->db->insert_id();
			//	for($news_website as $news_website_row){
				for($j=0;$j < count($news_website);$j++){
					$news_website_row = $news_website[$j];
					$news_website_row['trend_news_id'] = $last_insert_id;
					$news_website_row['source_from'] = 1;
					insert_data('news_website',$news_website_row);
					//$this->curl->simple_post(site_url('welcome/insert_source_website'),$news_website_row);	
				}
			}
		}
		return json_encode(array('No of Rows'=>$insert_row_count,'insert Title'=>$titleInsert));
	}
	
	public function insert_source_website(){
		if(insert_data('news_website',$_POST)){
			echo 'success';
		}else{
			echo 'error';
		}
	}
	//Pages are display based on this scrape
        // now we take only single row based on source_from,trend_news_id
	public function scrape_website(){
		$no_of_rowupdated = 0;
		$result = $this->db->query('SELECT id,source_from,trend_news_id FROM `news_website` where status = 0 GROUP BY source_from,trend_news_id');
		$trend_api_table = $this->config->item('trend_api_table');
		if($result->num_rows()){
			
			foreach($result->result() as $row){
                               //get news content from origin table
				$scrape_data = get_data($trend_api_table[$row->source_from],array('id'=>$row->trend_news_id))->row();
				if(isset($scrape_data->snippet)){
					$snipped = $scrape_data->snippet;
				}else{
					$snipped = '';
				}
				insert_data('trend_news',array('title'=>$scrape_data->news_title,'source_link'=>$scrape_data->source_url_link,'img_url'=>$scrape_data->img_url,'content'=>$snipped,'status'=>1,'created_date'=>$scrape_data->created_date));
				$no_of_rowupdated = $no_of_rowupdated + 1;
				update_data('news_website',array('status'=>2),array('source_from'=>$row->source_from,'trend_news_id'=>$row->trend_news_id));
			}
			
		}
		echo $no_of_rowupdated;
	}
        
        function convert_searchresult_tostring($relatedSearchesList){
            if(is_object($relatedSearchesList)){
                foreach ($relatedSearchesList as $list_row){
                    $searchlist_arr[] = $list_row['query'];
                }
                $searchresult =  implode(', ',$searchlist_arr);
            }else{
               $searchresult = "";
            }
            
            return $searchresult;
        }
        
        function drop_table(){
            //$this->db->query('TRUNCATE TABLE google_hotTrend_news,news_website,google_trend_news');
        }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
