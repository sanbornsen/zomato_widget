<?php

/*
Plugin Name: Zomato User Widget
Plugin URI: http://www.zomato.com
Description: User widget
Version: 1.0
Author: Zomans (Sudipta Sen)
Author URI: http://www.zomato.com/team
License: none
*/

class zomato_user extends WP_Widget{
	

	function __construct(){
		parent::__construct(false, $name = __('Zomato User Widget'));
	}


	function form($instance){

		//Set up some default widget settings.
		$defaults = array( 'uid' => __('0', 'example'), 'height' => __('250px', 'example'), 'width' => __('170px', 'example') );	
		$instance = wp_parse_args( (array) $instance, $defaults ); 	
		
		
		
		?>
		<style>
			.widget-field{
				width: 100%;
			}
		</style>
		<p>
		    <label for="<?php echo $this->get_field_id( 'uid' ); ?>"><?php _e('User ID:', 'example'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'uid' ); ?>" name="<?php echo $this->get_field_name( 'uid' ); ?>" 
		    <?php if(isset($instance['uid'])):?>
		    	value="<?php echo $instance['uid']; ?>"
			<?php endif;?>
		    placeholder="<?php echo $defaults['uid']; ?>" class="widget-field" required />
		</p>
		<!-- 
		<p>
		    <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height:', 'example'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" 
		    <?php if(isset($instance['height'])):?>
		    	value="<?php echo $instance['height']; ?>"
			<?php endif;?>
		    placeholder="<?php echo $defaults['height']; ?>" style="width:100%;" />
		</p>
		 -->
		<p>
		    <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:', 'example'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>"
		    <?php if(isset($instance['width'])):?>
		    	value="<?php echo $instance['width']; ?>"
			<?php endif;?>
		    placeholder="<?php echo $defaults['width']; ?>" class="widget-field" />
		</p>
		 
		<?php
	}

	function update( $new_instance, $old_instance ) {
	    $instance = $old_instance;
	 
	    $instance['uid'] = str_replace(" ", "", $new_instance['uid']);
	    //$instance['height'] = $new_instance['height'];
	    $instance['width'] = str_replace(" ", "", $new_instance['width']);

	    // Setting height to 100% in default cases
	    /*
	    if($instance['height'] == ''){
	    	$instance['height'] = '100%';
	    }
		*/
	    // Setting width to 100% in default cases
	    if($instance['width'] == '' or (substr(trim($instance['width']), -1) != '%' and intval($instance['width'])<160)){
	    	$instance['width'] = '200px';
	    }

	    // Not saving any value, incase the uid in none
	    if($instance['uid'] != '')
	    	return $instance;
	}



	function widget($args, $instance){
		
		extract( $args );
		$uid = $instance['uid'];
		$height = $instance['height'];
		$width = $instance['width'];
		$error = "";
		$content = "";
		

		if($uid != 0){
			try {
				$ch = curl_init("http://www.zomato.com/php/getPublicUserData?user_id=".$uid);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
				$content = curl_exec($ch);
				curl_close($ch);	
			}
			catch (Exception $e){
				$error = 'There are some problem in finding this user !! \n';
			}
		}
		else{
			$content = "";
		}

		if($content!=""){
			$usr_data = json_decode($content, true);
		}
		
		if($usr_data["error"]==0 and $uid != 0){
			$name = $usr_data['user_name'];
			$bio = $usr_data['user_bio'];
			$profile_link = $usr_data['profile_link'];
			$profile_image_link = $usr_data['profile_image_link'];
			$user_rating = $usr_data['user_rating'];
			$user_status = $usr_data['user_status'];
			$num_of_review = $usr_data['num_of_reviews'];
			$num_of_photos = $usr_data['num_of_photos'];
			$num_of_followers = $usr_data['num_of_followers'];
			$zomato_logo_link = $usr_data['zomato_logo_link'];
			

			

			// Image saving for the blur effect
			$profile_img = dirname(__FILE__)."/images/".$uid.".jpg";
			file_put_contents($profile_img, file_get_contents($profile_image_link));
			$arr = array_slice(explode("/", $profile_img), -5);
			$profile_image = implode("/", $arr);
	 
			$css_path = implode("/", array_slice(explode("/", dirname(__FILE__)."/css/zomato.css"), -5));
			$js_path = implode("/", array_slice(explode("/", dirname(__FILE__)."/js/blur.js"), -5));
			$jq_path = implode("/", array_slice(explode("/", dirname(__FILE__)."/js/jq.js"), -5));
			echo $before_widget;
			 
			?>


			<div style='width:<?=$width?>'>

			<!-- Widget content starts here -->

			<meta charset="utf-8" />
			<link rel="stylesheet" type="text/css" href="<?=$css_path?>">
			<script type="text/javascript" src="<?=$jq_path?>"></script>
			<script type="text/javascript" src="<?=$js_path?>"></script>

			    
			<style>
			    body {
			        min-width: 100% !important;
			    }
			</style>




			<div class="uw-body">

			    <div class="uw-header">

			        <div id="blurred_image_widget" class="blurred_image_widget" style="width:226px; height:513px; background-image: url('<?=$profile_image?>'); background-size: cover; background-repeat: no-repeat; background-attachment: scroll; "></div>

			        <a href="<?=$profile_link?>" target="_blank">

			            <div class="uw-bg"></div>

			            <img id="source-image-widget-profile" src="<?=$profile_image_link?>" alt="">

			                        <div id="source-image-widget" class="hidden source_image_widget" style="background-image:url('<?=$profile_image?>');"></div>

			            <p class="uw-name uw-p">
			                <?=$name?>            
			            </p>
			            <?php if($bio!=""):?>
			            <p class="uw-bio uw-p">
			            	<?=$bio?>
						</p>
						<script type="text/javascript">
							var height = parseInt($(".uw-bio").css("line-height"));
							var lineCount = 2;
							height *= lineCount;

							$(".uw-bio").css("height", height + "px");
						</script>
						<?php endif;?>

			            <p class="level uw-p">
			                <span data-icon="ú" class="uflc-1"><?=$user_rating?></span> <?=$user_status?>            </p>

			            <div class="clear"></div>

			        </a>

			        <ul class="uw-stats">

			            <li>
			                <a target="_blank" href="http://www.zomato.com/sanborn#reviews">
			                    <span class="uw-stats--number"><?=$num_of_review?></span>
			                    <span class="uw-stats--label">Reviews</span>
			                </a>
			            </li>

			            <li>
			                <a target="_blank" href="http://www.zomato.com/sanborn#photos">
			                    <span class="uw-stats--number"><?=$num_of_photos?></span>
			                    <span class="uw-stats--label">Photos</span>
			                </a>
			            </li>

			            <li>
			                <a target="_blank" href="http://www.zomato.com/sanborn#network">
			                    <span class="uw-stats--number"><?=$num_of_followers?></span>
			                    <span class="uw-stats--label">Followers</span>
			                </a>
			            </li>
			        </ul>
			        <!-- /uw-stats -->

			    </div>
			    <!-- /uw-header -->

			    <div class="clear"></div>

			</div>


			<div class="uw-powered">
			    <a href="http://www.zomato.com/" target="_blank">
			       <img src="<?=$zomato_logo_link?>" alt="Zomato" />
			    </a>
			</div>


			<div class="clear"></div>


			<script type="text/javascript">

			   /*
					Javascript to blur the image
			   */


			   var onImgLoad = function(selector, callback){
				    $(selector).each(function(){
				        if (this.complete || /*for IE 10-*/ $(this).height() > 0) {
				            callback.apply(this);
				        }
				        else {
				            $(this).on('load', function(){
				                callback.apply(this);
				            });
				        }
				    });
				};
	
				//calling the blur function
				//once the image has been loaded
				onImgLoad($('.source_image_widget'), function(){
					$('.blurred_image_widget').blurjs({
				          source: '.source_image_widget',
				          radius: 8,
				          cache: true,
				          // overlay: 'rgba(255,255,255,0.4)',
				          offset: {
				            x: 0,
				            y: 0
				          }
				      });
				});
			</script>

			<!-- Widget content ends here -->



			</div>
			
			<?php
			echo $after_widget;
		}
		else{
			echo "No Zomato User Found!! ";
		}
				
	}

}


// Activating and registering the widget

add_action('widgets_init', function() {
	register_widget('zomato_user');
})

?>
