/*
 * ====================================================================================
 * ----------------------------------------------------------------------------------
 *  The WordPress plugin is built for Nalanga URL Shortener that is freely available on Wp repository
 *  Version 2.5.0
 *  Copyright (c) Nalanga by Touristerguide - https://touristerguide.com
 *  https://nalan.ga
 * ----------------------------------------------------------------------------------
 * ====================================================================================
 */ 
var getLocation=function(e){var t=document.createElement("a");t.href=e;return t};(function(e){e.fn.extend({shorten:function(t){var n={url:null,key:null,internal:false};var r=e.extend(n,t);var i=0;if(r.url===null){console.log("Please set the url to the api of the url shortener script.");i=1}if(r.key===null){console.log("Please set your API key.");i=1}if(i==0){e(this).each(function(){var t=e(this);var n=getLocation(t.attr("href"));if(n.hostname!=location.hostname){e.getJSON(r.url+"api?callback=?",{api:r.key,url:t.attr("href")},function(e){if(e.error=="0"){t.attr("href",e.short)}else{console.log(e.msg)}})}})}}});e(document).ready(function(){e(document).on("submit","#PUS_main form#PUS_form",function(t){t.preventDefault();var n=e(this);var r=e(this).parent("#PUS_main");var i=e(".current-container");var s=n.find("#PUS_url");var o=e(this).attr("action");var u=e("#PUS_share_text").val();if(!s.val()){r.find("#PUS_message").hide().html('Please enter a valid URL (including http:&#47;&#47;)').fadeIn("slow");r.find("#PUS_message").addClass("PUS_error")}else{r.find("#PUS_loading").fadeIn();e.getJSON(o+"api?callback=?",{api:r.find("#PUS_token").val(),url:s.val(),custom:r.find("#PUS_custom_input").val()},function(e){var t="<a href='https://twitter.com/share?url="+encodeURIComponent(e.short)+"&text="+encodeURI(u).replace(/%20/g,"+")+"' target='_blank'>Tweet</a> <a href='https://www.facebook.com/sharer.php?u="+e.short+"' target='_blank'>Share</a>";r.find("#PUS_loading").fadeOut();if(e.error){r.find("#PUS_message").hide().html(e.msg).fadeIn("slow");r.find("#PUS_message").addClass("PUS_error")}else{r.find("#PUS_message").hide();r.find("#PUS_custom_container").html(t);s.val(e.short);s.select()}})}});var t=e(".shortener_widget #PUS_main").width();if(t<450)e(".shortener_widget #PUS_main").addClass("widget_fix")})})(jQuery)