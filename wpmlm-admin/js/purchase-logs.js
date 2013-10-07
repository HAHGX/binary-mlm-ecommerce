(function($){
	$.extend(WPMLM_Purchase_Logs_Admin, {
		blur_timeout : null,
		reset_textbox_width : true,

		init : function() {
			$(function(){
				$('table.purchase-logs').delegate('.wpmlm-purchase-log-status', 'change', WPMLM_Purchase_Logs_Admin.event_log_status_change).
				                         delegate('.wpmlm-purchase-log-tracking-id', 'focus', WPMLM_Purchase_Logs_Admin.event_tracking_id_focused).
				                         delegate('.column-tracking a.add', 'click', WPMLM_Purchase_Logs_Admin.event_button_add_clicked).
				                         delegate('.wpmlm-purchase-log-tracking-id', 'blur', WPMLM_Purchase_Logs_Admin.event_tracking_id_blurred).
				                         delegate('.column-tracking a.save', 'click', WPMLM_Purchase_Logs_Admin.event_button_save_clicked).
				                         delegate('.column-tracking .send-email a', 'click', WPMLM_Purchase_Logs_Admin.event_button_send_email_clicked).
				                         delegate('.wpmlm-purchase-log-tracking-id', 'keypress', WPMLM_Purchase_Logs_Admin.event_enter_key_pressed).
				                         delegate('.column-tracking a.save', 'mousedown', WPMLM_Purchase_Logs_Admin.event_disable_textbox_resize).
				                         delegate('.column-tracking a.save', 'focus', WPMLM_Purchase_Logs_Admin.event_disable_textbox_resize);

			});
		},

		event_enter_key_pressed : function(e) {
			var code = e.keyCode ? e.keyCode : e.which;
			if (code == 13) {
				$(this).siblings('.save').click();
				e.preventDefault();
			}
		},

		event_button_send_email_clicked : function() {
			var t = $(this);

			var post_data = {
				'action' : 'wpmlm_purchase_log_send_tracking_email',
				'log_id' : t.closest('div').data('log-id'),
				'nonce'  : WPMLM_Purchase_Logs_Admin.nonce
			};

			var ajax_callback = function(response) {
				if (response != 'success') {
					alert(WPMLM_Purchase_Logs_Admin.send_tracking_email_error_dialog);
					t.show().siblings('em').remove();
				} else {
					t.siblings('em').addClass('sent').text(WPMLM_Purchase_Logs_Admin.sent_message);
					t.remove();
				}
			};

			t.hide().after('<em>' + WPMLM_Purchase_Logs_Admin.sending_message + '</em>');
			$.post(ajaxurl, post_data, ajax_callback);

			return false;
		},

		event_button_save_clicked : function() {
			var t = $(this), textbox = t.siblings('.wpmlm-purchase-log-tracking-id'), spinner = t.siblings('.ajax-feedback');

			var post_data = {
				'action' : 'wpmlm_purchase_log_save_tracking_id',
				'value'  : textbox.val(),
				'log_id' : t.parent().data('log-id'),
				'nonce'  : WPMLM_Purchase_Logs_Admin.nonce
			};

			var ajax_callback = function(response) {
				spinner.toggleClass('ajax-feedback-active');
				textbox.blur();
				if (response == 'success') {
					t.parent().removeClass('empty');
					WPMLM_Purchase_Logs_Admin.reset_tracking_id_width(t.siblings('.wpmlm-purchase-log-tracking-id'));
				} else {
					alert(WPMLM_Purchase_Logs_Admin.tracking_error_dialog);
				}
			};

			t.hide();
			spinner.toggleClass('ajax-feedback-active');
			textbox.width(160);

			$.post(ajaxurl, post_data, ajax_callback);

			return false;
		},

		event_disable_textbox_resize : function() {
			WPMLM_Purchase_Logs_Admin.reset_textbox_width = false;
		},

		event_button_add_clicked : function() {
			$(this).siblings('.wpmlm-purchase-log-tracking-id').trigger('focus');
			return false;
		},

		reset_tracking_id_width : function(t) {
			var reset_width = function() {
				if (WPMLM_Purchase_Logs_Admin.reset_textbox_width) {
					t.siblings('a.save').hide();
					t.width('');
					if (t.val() === '') {
						t.siblings('.add').show();
					}
				}

				WPMLM_Purchase_Logs_Admin.reset_textbox_width = true;
			};

			WPMLM_Purchase_Logs_Admin.blur_timeout = setTimeout(reset_width, 100);
		},

		event_tracking_id_blurred : function() {
			var t = $(this);

			WPMLM_Purchase_Logs_Admin.reset_tracking_id_width(t);
		},

		event_tracking_id_focused : function() {
			var t = $(this);
			t.width(128);
			t.siblings('a.save').show();
			t.siblings('a.add').hide();
		},

		event_log_status_change : function() {
			var post_data = {
					nonce      : WPMLM_Purchase_Logs_Admin.nonce,
					action     : 'wpmlm_change_purchase_log_status',
					id         : $(this).data('log-id'),
					new_status : $(this).val(),
					status     : WPMLM_Purchase_Logs_Admin.current_view
				},
				spinner = $(this).siblings('.ajax-feedback'),
				t = $(this);
			spinner.addClass('ajax-feedback-active');
			var ajax_callback = function(response) {
				spinner.removeClass('ajax-feedback-active');
				if (response == -1) {
					alert(WPMLM_Purchase_Logs_Admin.status_error_dialog);
				} else {
					$('ul.subsubsub').replaceWith(response);
				}
			};

			$.post(ajaxurl, post_data, ajax_callback);
		}
	});

})(jQuery);

WPMLM_Purchase_Logs_Admin.init();