<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	

	/**
	 * Index Page for this controller.
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
		$data['result'] = $this->db->query('select * from trend_news where status = 1 limit 20')->result();
		$this->load->view('template/home_template',$data);
	}
	
	public function google_api($date = null)
	{
		if($date == null){
			$date = date("Ymd", time() + 86400);
		}
		$this->load->library('curl');
		$google_response = $this->curl->simple_post('http://www.google.com/trends/hottrends/hotItems', array('ajax'=>'1','htd'=>$date,'pn'=>'p3','htv'=>'l'));
		$google_api_response = json_decode($google_response);
		$google_api_feed = $google_api_response->trendsByDateList;
		// day by day feed
		foreach($google_api_feed as $row){
			
			//var_dump($row->date);
			echo $this->get_daily_feed($row->trendsList).'<br>';
		//	exit;
			//array('search_keyword'=>$row)
			
			
//			search_keyword
//related_search_keyword
//hotnessLevel
//source
//souce_site_name
//source_url_link
//img_url
//snippet
//traffic
//created_date
		}
		exit;
	}
	
	
	public function get_daily_feed($feed){
		// per day feed
		$insert_row_count = 0;
		$this->load->library('curl');
		foreach($feed as $feed_row){
			echo $feed_row->title.'<br>';
			$trend_date= $feed_row->date;
			$i = 1;
			$news_website = array();
			foreach($feed_row->newsArticlesList as $row){
				if($i == 1){
					$news_title=$row->title;
					$source_url_link=$row->link;
					$souce_site_name=$row->source;
					$snippet =$row->snippet;
				}
				$news_website[] = array('news_title'=>addslashes($row->title),'url'=>$row->link,'source_site_name'=>$row->source,'trend_date'=>$trend_date);
				$i++;
				//exit;
			}
			$img_url = isset($feed_row->imgUrl) ? $feed_row->imgUrl : 'no_image_found.jpg';
			$google_api = array('related_search_keyword'=>implode($feed_row->relatedSearchesList,','),'search_keyword'=>$feed_row->title,'traffic'=>$feed_row->trafficBucketLowerBound,'hotnessLevel'=>$feed_row->hotnessLevel,'img_url'=>$img_url,
				 'news_title'=>addslashes($news_title),'source_url_link'=>$source_url_link,'source_site_name'=>$souce_site_name,'snippet'=>addslashes($snippet),'trend_date'=>$trend_date);
			$check_row = get_data('google_trend_news',array('search_keyword'=>$feed_row->title,'trend_date >='=>date('Ymd')))->num_rows();
			//if($check_row == 0 && $trend_date == date('Ymd')){
				$insert_row_count = $insert_row_count + 1;
				insert_data('google_trend_news',$google_api);
				$last_insert_id = $this->db->insert_id();
			//	for($news_website as $news_website_row){
				for($j=0;$j < count($news_website);$j++){
					$news_website_row = $news_website[$j];
					$news_website_row['trend_news_id'] = $last_insert_id;
					$news_website_row['source_from'] = 1;
					insert_data('news_website',$news_website_row);
					//$this->curl->simple_post(site_url('welcome/insert_source_website'),$news_website_row);	
				}
			//}
		}
		return $insert_row_count;
	}
	
	public function insert_source_website(){
		if(insert_data('news_website',$_POST)){
			echo 'success';
		}else{
			echo 'error';
		}
	}
	
	public function scrape_website(){
		$no_of_rowupdated = 0;
		$result = $this->db->query('SELECT id,source_from,trend_news_id FROM `news_website` where status = 0 GROUP BY source_from,trend_news_id');
		$trend_api_table = $this->config->item('trend_api_table');
		if($result->num_rows()){
			
			foreach($result->result() as $row){
				$scrape_data = get_data($trend_api_table[$row->source_from],array('id'=>$row->trend_news_id))->row();
				
				insert_data('trend_news',array('title'=>$scrape_data->news_title,'source_link'=>$scrape_data->source_url_link,'img_url'=>$scrape_data->img_url,'content'=>$scrape_data->snippet,'status'=>1,'trend_date'=>$scrape_data->created_date));
				$no_of_rowupdated = $no_of_rowupdated + 1;
				update_data('news_website',array('status'=>2),array('source_from'=>$row->source_from,'trend_news_id'=>$row->trend_news_id));
			}
			
		}
		echo $no_of_rowupdated;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
