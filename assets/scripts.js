jQuery(document).ready(function($) {
    $('#custom-contact-form').submit(function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: VG.ajaxurl,
            data: formData,
            success: function(response) {
                $('#result').html("Form submitted");
            }
        });
    });
});
