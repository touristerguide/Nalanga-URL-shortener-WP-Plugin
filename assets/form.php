<div id="PUS_main" <?php if(isset($theme)) echo "class='".$theme."-c'"; ?>>					
	<div id="PUS_message"></div>				
	<!-- /#Nalanga Url shortener_message -->
	<form action="<?php echo self::$config["shortener_url"]; ?>" id="PUS_form">
		<div id="PUS_main_input">
			<label for="PUS_main_input"><?php _e("Long URL",'shortener-plugin') ?></label>
			<input type="text" placeholder="http://" id="PUS_url">	
			<span id="PUS_loading"></span>			
		</div>
		<!-- /#Nalanga Url shortener_main_input -->
		<div id="PUS_custom_container">
			<div id="PU_custom_input_container">
				<label for="PUS_custom_input"><?php _e("Custom Alias",'shortener-plugin') ?></label>
				<span><label for="PUS_custom_input"><?php if(self::$config["shortener_url"]) echo self::$config["shortener_url"]?></label></span>
				<input type="text" id="PUS_custom_input" placeholder="e.g. Touristerguide">				
			</div>
			<input type="hidden" id="PUS_share_text" value="<?php if(self::$config["shortener_share"]) echo self::$config["shortener_share"]?>">			
			<input type="hidden" id="PUS_token" value="<?php if(self::$config["shortener_api"]) echo self::$config["shortener_api"]?>">
			<!-- /#Nalanga Url shortener_custom_input_container -->
			<button type="submit"><?php _e("Shorten",'shortener-plugin') ?></button>
		</div>
		<!-- /#Nalanga Url shortener_custom_container -->
	</form>
	<!-- /#Nalanga Url shortener_form -->
</div>	