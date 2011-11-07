/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.4
 * @filesource
 */

// Fire off the file browser
$.ee_filebrowser();

// Make sure we can create these methods without issues
EE.namespace('EE.publish.file_browser');

/**
 * Fires up the filebrowser for text areas
 */
EE.publish.file_browser.textarea = function() {
	// Bind the image html buttons
	$.ee_filebrowser.add_trigger(".btn_img a, .file_manipulate", function(file) {
		var textarea,
			replace = '',
			props = '',
			open = '',
			close = '';

		// A bit of working around various textareas, text inputs, tec
		if ($(this).closest("#markItUpWrite_mode_textarea").length) {
			textareaId = "write_mode_textarea";
		} else {
			textareaId = $(this).closest(".publish_field").attr("id").replace("hold_field_", "field_id_");
		}

		if (textareaId != undefined) {
			textarea = $("#"+textareaId);
			textarea.focus();		
		}

		// We also need to allow file insertion into text inputs (vs textareas) but markitup
		// will not accommodate this, so we need to detect if this request is coming from a 
		// markitup button or another field type.

		// Fact is - markitup is actually pretty crappy for anything that doesn't specifically
		// use markitup. So currently the image button only works correctly on markitup textareas.

		if ( ! file.is_image) {
			props = EE.upload_directories[file.upload_location_id].file_properties;

			open = EE.upload_directories[file.upload_location_id].file_pre_format;
			open += "<a href=\"{filedir_"+file.upload_location_id+"}"+file.file_name+'" '+props+" >";

			close = "</a>";
			close += EE.upload_directories[file.upload_location_id].file_post_format;
		} else {
			props = EE.upload_directories[file.upload_location_id].properties;

			open = EE.upload_directories[file.upload_location_id].pre_format;
			close = EE.upload_directories[file.upload_location_id].post_format;

			// Include any user additions before or after the image link
			replace = EE.filebrowser.image_tag.replace(/src="(.*)\[!\[Link:!:http:\/\/\]!\](.*)"/, 'src="$1{filedir_'+file.upload_location_id+'}'+file.file_name+'$2"');

			// Figure out dimensions
			dimensions = '';
			if (typeof file.file_hw_original != "undefined" && file.file_hw_original != '') {
				dimensions = file.file_hw_original.split(' ');
				dimensions = 'height="'+dimensions[0]+'" width="'+dimensions[1]+'"';
			};

			replace = replace.replace(/\/?>$/, dimensions+' '+props+' />');

			replace = open + replace + close;
		}


		if (textarea.is("textarea")) {
			if ( ! textarea.is('.markItUpEditor')) {
				textarea.markItUp(myNobuttonSettings);
				textarea.closest('.markItUpContainer').find('.markItUpHeader').hide();
				textarea.focus();
			}

			// Handle images and non-images differently
			if ( ! file.is_image) {
				$.markItUp({
					key:"L",
					name:"Link",
					openWith: open,
					closeWith: close,
					placeHolder:file.file_name
				});
			} else {
				$.markItUp({
					replaceWith: replace
				});
			}
		} else {
			textarea.val(function(i, v) {
				v += open + replace + close;
				return magicMarkups(v);
			});
		}
	});
};

/**
 * Fire up the file browser for file fields
 */
EE.publish.file_browser.file_field = function() {
	/**
	 * Changes the hidden inputs, thumbnail and file name when a file is selected
	 * @param {Object} file File object with information about the file upload
	 * @param {Object} field jQuery object of the field
	 */
	function file_field_changed(file, field) {
		var container = $("input[name="+field+"]").closest(".publish_field");

		if (file.is_image == false) {
			container.find(".file_set").show().find(".filename").html("<img src=\""+EE.PATH_CP_GBL_IMG+"default.png\" alt=\""+EE.PATH_CP_GBL_IMG+"default.png\" /><br />"+file.file_name);
		} else {
			container.find(".file_set").show().find(".filename").html("<img src=\""+file.thumb+"\" /><br />"+file.file_name);
		}

		$("input[name="+field+"_hidden]").val(file.file_name);
		$("select[name="+field+"_directory]").val(file.upload_location_id);
	}

	// Look for every file input on the publish form and establish the 
	// file browser trigger. Also establishes the remove file handler.
	$("input[type=file]", "#publishForm").each(function() {
		var container = $(this).closest(".publish_field"),
			trigger = container.find(".choose_file"),
			content_type = $(this).data('content-type'),
			directory = $(this).data('directory'),
			settings = {
				"content_type": content_type,
				"directory": directory
			};

		$.ee_filebrowser.add_trigger(trigger, $(this).attr("name"), settings, file_field_changed);

		container.find(".remove_file").click(function() {
			container.find("input[type=hidden]").val("");
			container.find(".file_set").hide();
			return false;
		});
	});
};

$(function() {
	if (EE.filebrowser.publish == true) {
		// Give Markitup time to activate
		setTimeout(function() {
			EE.publish.file_browser.file_field();
			EE.publish.file_browser.textarea();
		}, 15)
	};
});