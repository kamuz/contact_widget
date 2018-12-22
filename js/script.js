jQuery(document).ready(function($){
    // Get Form
    var form = $('#ajax-contact');

    // Messages
    var formMessages = $('#form-messages');

    // Form Event Handler
    $(form).submit(function(event){
        // Stop browser from submitting form
        event.preventDefault();
        console.log("Contact form submited");

        // Serialize Data
        var formData = $(form).serialize();
        console.log(formData);

        // Submit with AJAX
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData,
        }).done(function(response){
            // Make sure message is success
            $(formMessages).removeClass('error');
            $(formMessages).addClass('success');

            // Set message
            $(formMessages).text(response);

            // Clear form fields
            $('#name').val('');
            $('#email').val('');
            $('#message').val('');
        }).fail(function(data){
            // Make sure message is error
            $(formMessages).removeClass('success');
            $(formMessages).addClass('error');

            // Set message text
            if(data.responseText !== ''){
                $(formMessages).text(data.responseText);
            }else{
                $(formMessages).text('An error occured');
            }
        });
    });
});