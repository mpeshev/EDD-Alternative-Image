jQuery(function($){
	// Image Uploader as per WordPress Core
	$( document ).on('click', '#set-post-sub-thumbnail', function(e) {  
		
		var imgfield,showimgfield;
		var self = $(e.currentTarget); 
		imgfield = jQuery(this).prev('input').attr('id');
		
		var file_frame;
		
		// If the media frame already exists, reopen it.
		if ( file_frame ) { 
			file_frame.open();
		  return;
		}
		
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			multiple: false  // Set to true to allow for multiple files to be selected
		});

		file_frame.on( 'menu:render:default', function(view) {
	        // Store our views in an object.
	        var views = {};

	        // Unset default menu items
	        view.unset('library-separator');
	        view.unset('gallery');
	        view.unset('featured-image');
	        view.unset('embed');

	        // Initialize the views in our view object.
	        view.set(views);
	    });
		
		// When an image is selected, run a callback.
		file_frame.on( 'insert', function() {

			// Get selected size from media uploader
			var selected_size = jQuery('.attachment-display-settings .size').val();
			
			var selection = file_frame.state().get('selection');
			selection.each( function( attachment, index ) {
				attachment = attachment.toJSON();
				
				// Selected attachment url from media uploader
				var attachment_url = attachment.sizes[selected_size].url;
				var attachment_id = attachment.id;
				
				var thumb = attachment.sizes.full.url;
				
				if( undefined !== attachment.sizes.thumbnail ) {
					var thumb = attachment.sizes.thumbnail.url;
				}
				
				jQuery('#'+imgfield).val(attachment_url);
				jQuery('#'+imgfield+'-id').val(attachment_id);
				jQuery('#eai-image-view').html('<img src="'+thumb+'" alt="Image" />');
				jQuery('#eai-image-view').after('<a title="remove sub-featured image" href="#" id="remove-post-sub-thumbnail" class="eai_thickbox">Remove alternative featured image</a>');
				self.remove();
				
			});
		});

		// Finally, open the modal
		file_frame.open();
		jQuery(document).find('a:contains("DX Share Media")').click();
		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html) {
			
			if(imgfield)  {
				var mediaurl = jQuery('img',html).attr('src');
				jQuery('#'+imgfield).val(mediaurl);
				jQuery('#'+showimgfield).html('<img src="'+mediaurl+'" alt="Image" />');
				tb_remove();
				imgfield = '';
			} else {
				window.original_send_to_editor(html);
			}
		};
		
	});

	$( document ).on('click', '#remove-post-sub-thumbnail', function() {
		$('#eai-image').val('');
		$('#eai-image-view').html('');
		$('#eai-image').after('<a title="Set alternative featured image" href="#" id="set-post-sub-thumbnail" class="eai_thickbox">Set alternative featured image</a>');
		$(this).remove();
	});

});