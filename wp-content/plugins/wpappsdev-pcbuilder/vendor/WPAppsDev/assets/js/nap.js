jQuery(document).ready(function($) {

	//metaboxes toggle if a post format is chosen
	jQuery.fn.extend({
		ShowPostFormats: function () {

			$this = jQuery(this);
			var theSelectedFormat  = $this.attr("id");
			//console.log(theSelectedFormat);

			//post formats / option pairs
			var post_formats = {};
			post_formats['post-format-0'] = "#tj_blog_standard_info";
			post_formats['post-format-gallery'] = "#tj_blog_gallery_info";
			// post_formats['post-format-link'] = "#xtrimIT_link_post_fields";
			// post_formats['post-format-quote'] = "#xtrimIT_quote_post_fields";
			post_formats['post-format-audio'] = "#tj_blog_audio_info";
			post_formats['post-format-video'] = "#tj_blog_video_info";


				for (var key in post_formats) {
					jQuery(post_formats[key]).css({"display":"none"});
				}

			jQuery(post_formats[theSelectedFormat]).css({"display":"block"});


		}
	});

	jQuery("#post-formats-select input:checked").ShowPostFormats();

	jQuery("#post-formats-select").on("change", function(event){
		jQuery("#post-formats-select input:checked").ShowPostFormats();
	});

	//jQuery('#tj_blog_standard_info').css({"display":"none"});


	//metaboxes toggle if a page template is chosen
	jQuery.fn.extend({
		ShowPageFields: function () {

			$this = jQuery(this);
			var theSelectedTemplate  = $this.attr("value");
			console.log(theSelectedTemplate);

			//page templates
			var page_templates = {};


			jQuery("#tj_page_header_home").css({"display":"none"});
			jQuery("#postimagediv").css({"display":"none"});
			if(theSelectedTemplate=='default'){
				jQuery("#tj_page_header_home").css({"display":"block"});
				jQuery("#postimagediv").css({"display":"block"});
			}
			if(theSelectedTemplate== undefined){
				jQuery("#postimagediv").css({"display":"block"});
			}
		}
	});

	jQuery("#page_template option:selected").ShowPageFields();

	jQuery("#page_template").on("change", function(event){
		jQuery("#page_template option:selected").ShowPageFields();

});	});