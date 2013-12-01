jQuery(document).ready(function() {
    // Handle status.
    jQuery('.scripto-toggle-status').click(function(event) {
        event.preventDefault();
        var id = jQuery(this).attr('id');
        var current = jQuery('#' + id);
        id = id.substr(id.indexOf('-') + 1);
        var ajaxUrl = jQuery(this).attr('href') + '/scripto/ajax/update';
        jQuery(this).addClass('transmit');
        if (jQuery(this).hasClass('not-to-transcribe')) {
            jQuery.post(ajaxUrl,
                {
                    status: 'To transcribe',
                    id: id
                },
                function(data) {
                    current.addClass('to-transcribe');
                    current.removeClass('not-to-transcribe');
                    current.removeClass('transmit');
                    if (current.text() != '') {
                        current.text(Omeka.messages.scripto.toTranscribe);
                    }
                }
            );
        } else {
            jQuery.post(ajaxUrl,
                {
                    status: 'Not to transcribe',
                    id: id
                },
                function(data) {
                    current.addClass('not-to-transcribe');
                    current.removeClass('to-transcribe');
                    current.removeClass('transmit');
                    if (current.text() != '') {
                        current.text(Omeka.messages.scripto.notToTranscribe);
                    }
                }
            );
        }
    });
});
