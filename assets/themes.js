/**
 * Nalanga Url shortener jQuery Plugin
 * Touristerguide/ Nalanga - https://touristerguide.com
 **/
jQuery(document).ready(function(){
  jQuery(document).on('change',".PUS_theme-trigger",function(){
  	var c=jQuery(this).val();
  	jQuery("#PUS_widget_title").text(jQuery('.PUS_theme-trigger :selected').text());
    jQuery("#PUS_demo").find("#PUS_main").removeClass();
    jQuery("#PUS_demo").find("#PUS_main").addClass(c+"-c");
    if(c=="cc"){
    	jQuery(".PUS_custom_message").fadeIn();
    }else{
    	jQuery("#PUS_custom_message").hide();
    }
    jQuery('body,html').animate({ scrollTop: 0 });    	
  });     
  jQuery("#quick_url_shorten_admin_button").click(function(e){
    e.preventDefault();
    var c=jQuery("#quick_url_shorten_admin_form");
    var appurl=c.attr("data-url");
    var url=c.find("#quick_url_shorten_admin_input");

    jQuery.getJSON(appurl+"api?callback=?",
      {
        api: c.find("#quick_url_shorten_admin_api").val(),
        url: url.val(),
        custom: c.find("#quick_url_shorten_admin_custom").val()
      },
      function(html) {
        if(html.error){
          c.find('#quick_url_shorten_admin_message').hide().html('<div id="message" class="error">'+html.msg+'</div>').fadeIn('slow');               
        }else{          
          c.find('#quick_url_shorten_admin_message').hide();
          url.val(html.short);  
          url.select();                      
        }     
    });        
  });
});