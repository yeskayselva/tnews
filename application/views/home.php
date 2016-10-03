<div id="templatemo_content">
		<div id="templatemo_left_column">
			<?php foreach($result as $row){ ?>
			<div class="templatemo_postbody">       
                <h1><a href='<?php echo $row->source_link; ?>' ><?php echo $row->title; ?></a></h1>
                <div class="publish_info">Post Date: <?php echo $row->trend_date;?> · Tags: <a href="#">XHTML</a> · <a href="#">CSS</a> · <a href="#">Website Templates</a></div>
			  <p><img src="<?php echo $row->img_url; ?>" alt="image"/>
			  <p><?php echo $row->content; ?></p>
			  <div class="comment"><a href="#">Comments (18)</a></div>
		  </div>
			
			<?php }?>
            </div>
		</div>
	</div>