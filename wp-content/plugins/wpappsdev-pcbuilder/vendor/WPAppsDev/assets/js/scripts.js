jQuery(document).ready(function ($) {
	jQuery('.upload_gallery_button').click(function (event) {
		var current_gallery = jQuery(this).closest('label');

		if (event.currentTarget.id === 'clear-gallery') {
			//remove value from input
			current_gallery.find('.gallery_values').val('').trigger('change');

			//remove preview images
			current_gallery.find('.gallery-screenshot').html('');
			return;
		}

		// Make sure the media gallery API exists
		if (typeof wp === 'undefined' || !wp.media || !wp.media.gallery) {
			return;
		}
		event.preventDefault();


		var gallery = wp.media.gallery;
		var ids = current_gallery.find('.gallery_values').val(); // your current attachment ids
		var frame = gallery.edit('[gallery' + (ids.length ? ' ids="' + ids + '"' : '') + ']');

		frame.state('gallery-edit').on('update', function (selection) {

			//clear screenshot div so we can append new selected images
			current_gallery.find('.gallery-screenshot').html('');

			var element, preview_html = '', preview_img;
			var ids = selection.models.map(
				function (e) {
					element = e.toJSON();
					preview_img = typeof element.sizes.thumbnail !== 'undefined' ? element.sizes.thumbnail.url : element.url;
					preview_html = "<div class='screen-thumb'><img src='" + preview_img + "'/></div>";
					current_gallery.find('.gallery-screenshot').append(preview_html);
					return e.id;
				}
			);

			current_gallery.find('.gallery_values').val(ids.join(',')).trigger('change');
		}
		);
		return false;
	});
});

jQuery(document).ready(function ($) {

	// the upload image button, saves the id and outputs a preview of the image
	var imageFrame;
	$('.meta_box_upload_image_button').click(function (event) {
		event.preventDefault();

		var options, attachment;

		$self = $(event.target);
		$div = $self.closest('div.meta_box_image');

		// if the frame already exists, open it
		if (imageFrame) {
			imageFrame.open();
			return;
		}

		// set our settings
		imageFrame = wp.media({
			title: 'Choose Image',
			multiple: false,
			library: {
				type: 'image'
			},
			button: {
				text: 'Use This Image'
			}
		});

		// set up our select handler
		imageFrame.on('select', function () {
			selection = imageFrame.state().get('selection');

			if (!selection)
				return;

			// loop through the selected files
			selection.each(function (attachment) {
				console.log(attachment);
				var src = attachment.attributes.sizes.full.url;
				var id = attachment.id;

				$div.find('.meta_box_preview_image').attr('src', src);
				$div.find('.meta_box_upload_image').val(id);
			});
		});

		// open the frame
		imageFrame.open();
	});

	// the remove image link, removes the image id from the hidden field and replaces the image preview
	$('.meta_box_clear_image_button').click(function () {
		var defaultImage = $(this).parent().siblings('.meta_box_default_image').text();
		$(this).parent().siblings('.meta_box_upload_image').val('');
		$(this).parent().siblings('.meta_box_preview_image').attr('src', defaultImage);
		return false;
	});

	// the file image button, saves the id and outputs the file name
	var fileFrame;
	$('.meta_box_upload_file_button').click(function (e) {
		e.preventDefault();

		var options, attachment;

		$self = $(event.target);
		$div = $self.closest('div.meta_box_file_stuff');

		// if the frame already exists, open it
		if (fileFrame) {
			fileFrame.open();
			return;
		}

		// set our settings
		fileFrame = wp.media({
			title: 'Choose File',
			multiple: false,
			library: {
				type: 'file'
			},
			button: {
				text: 'Use This File'
			}
		});

		// set up our select handler
		fileFrame.on('select', function () {
			selection = fileFrame.state().get('selection');

			if (!selection)
				return;

			// loop through the selected files
			selection.each(function (attachment) {
				console.log(attachment);
				var src = attachment.attributes.url;
				var id = attachment.id;

				$div.find('.meta_box_filename').text(src);
				$div.find('.meta_box_upload_file').val(src);
				$div.find('.meta_box_file').addClass('checked');
			});
		});

		// open the frame
		fileFrame.open();
	});

	// the remove image link, removes the image id from the hidden field and replaces the image preview
	$('.meta_box_clear_file_button').click(function () {
		$(this).parent().siblings('.meta_box_upload_file').val('');
		$(this).parent().siblings('.meta_box_filename').text('');
		$(this).parent().siblings('.meta_box_file').removeClass('checked');
		return false;
	});

	// function to create an array of input values
	function ids(inputs) {
		var a = [];
		for (var i = 0; i < inputs.length; i++) {
			a.push(inputs[i].val);
		}
		//$("span").text(a.join(" "));
	}
	// repeatable fields
	$('.meta_box_repeatable_add').on('click', function () {
		// clone
		var row = $(this).closest('.meta_box_repeatable').find('tbody tr:last-child');
		var clone = row.clone();
		clone.find('select.chosen').removeAttr('style', '').removeAttr('id', '').removeClass('chzn-done').data('chosen', null).next().remove();
		clone.find('input.regular-text, textarea, select').val('');
		clone.find('input[type=checkbox], input[type=radio]').attr('checked', false);
		row.after(clone);
		// increment name and id
		clone.find('input, textarea, select')
			.attr('name', function (index, name) {
				return name.replace(/(\d+)/, function (fullMatch, n) {
					return Number(n) + 1;
				});
			});
		var arr = [];
		$('input.repeatable_id:text').each(function () { arr.push($(this).val()); });
		clone.find('input.repeatable_id')
			.val(Number(Math.max.apply(Math, arr)) + 1);
		if (!!$.prototype.chosen) {
			clone.find('select.chosen')
				.chosen({ allow_single_deselect: true });
		}
		//
		return false;
	});

	// $('.meta_box_repeatable_remove').on('click', function () {
	// 	$(this).closest('tr').remove();
	// 	return false;
	// });

	$("body").on('click', '.meta_box_repeatable_remove', function (e) {
		e.preventDefault();
		$(this).closest('tr').remove();
		return false;
	});

	$('.meta_box_repeatable tbody').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.hndle'
	});

	// post_drop_sort
	$('.sort_list').sortable({
		connectWith: '.sort_list',
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		cancel: '.post_drop_sort_area_name',
		items: 'li:not(.post_drop_sort_area_name)',
		update: function (event, ui) {
			var result = $(this).sortable('toArray');
			var thisID = $(this).attr('id');
			$('.store-' + thisID).val(result)
		}
	});

	$('.sort_list').disableSelection();

	// turn select boxes into something magical
	if (!!$.prototype.chosen)
		$('.chosen').chosen({ allow_single_deselect: true });

	//metaboxes toggle if a post format is chosen

	//    function ShowPostFormats() {

	// 	$this = $(this);
	// 	var theSelectedFormat  = $this.attr("id");
	// 	console.log(theSelectedFormat);

	// 	//post formats / option pairs
	// 	var post_formats = {};
	// 	post_formats['post-format-0'] = "#xtrimIT_standard_post_fields";
	// 	// post_formats['post-format-gallery'] = "#xtrimIT_gallery_post_fields";
	// 	// post_formats['post-format-link'] = "#xtrimIT_link_post_fields";
	// 	// post_formats['post-format-quote'] = "#xtrimIT_quote_post_fields";
	// 	post_formats['post-format-audio'] = "#xt_blog_audio_info";
	// 	post_formats['post-format-video'] = "#xt_blog_video_info";

	// 	for (var key in post_formats) {
	// 		$(post_formats[key]).css({"display":"none"});
	// 	}
	// 	$(post_formats[theSelectedFormat]).css({"display":"block"});
	//    }


	// $("#post-formats-select input:radio").ShowPostFormats();

	// $("#post-formats-select").on("click", function(event){
	// 	$("#post-formats-select input:checked").ShowPostFormats();
	// });
});