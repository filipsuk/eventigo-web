$(function () {
    $.nette.init();

    // Submit tags filter when tag is checked
    $('#tags input[type=checkbox]').on('change', function() {
        $(this).closest('form').submit();
    });

    $('#main-settings input[type=checkbox]').on('change', function() {
        $(this).closest('form').submit();
    });

    // Move email subscription
    var box = $('.subscription-box');
    $('#subscription').appendTo(box);
    box.find('input').attr('form', 'frm-subscriptionTags-form');

    // Sticky subscription box
    // TODO: improve on mobile (now footer hidden on mobile to make it work)
    $(window).on("scroll", function() {
        var box = $('.subscription-box');
        if (!box.data('originalOffset')) {
            box.data('originalOffset', box.offset().top);
        }
        // Fixed above footer
        if ($('footer')[0].getBoundingClientRect().top - $(window).height() < 0) {
            box.removeClass('fixed')
                .addClass('fixed-down');
        }
        // Fixed on bottom of the screen
        else if ($('#homepage')[0].getBoundingClientRect().top  - $(window).height() < $('#homepage').offset().top - box.data('originalOffset') - box.height()) {
            box.addClass('fixed')
                .removeClass('fixed-down');
        }
        // In the middle of events list ready to be fixed
        else {
            box.removeClass('fixed');
        }
    });


    // Smooth scroll
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 300);
                return false;
            }
        }
    });

    
    $('input[name=subscribe]').click(function(e) {
        e.preventDefault(); // Prevent form submission
        // When user wants to subscribe with empty email, focus on text input
        var mail = $('#frm-subscriptionTags-form-email');
        if (mail.val().length < 5) {
            mail.focus();
            return;
        }

        // Subscribing by button (real_subscribe hidden input) :)
        var realSubscribe = $(this).siblings('input[name=real_subscribe]');
        realSubscribe.val(true);

        $('#frm-subscriptionTags-form').submit();
        realSubscribe.val(false);
    });

    // Scroll to flash messages if new appear
    $('#snippet--flash-messages').bind('DOMNodeInserted', '.flash', function(e) {
        if (!$('html,body').is(':animated')) { // && $(this)[0].getBoundingClientRect().top < 0) { // scroll if needed and only once (DOMNodeInserted fires 3x)
            $('html, body').animate({
                scrollTop: $(this).offset().top - 15
            }, 300);
        }

        // Hide login form after form is submitted
        // HACK should be catched better way
        if ($('#login-form').is(':visible')) {
            $('body').trigger('click.bs.dropdown.data-api');
            resetLoginMenu();
            $('#login-form [type=submit]').button('reset');
        }
    });

    // TODO separe this to own file / module / plugin / component
    $('#login-via-email-btn').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('ul').find('li.login-list-item').hide();
        $('#login-form').show().find('input[name=email]').focus();
    });

    $('#login-form').closest('.dropdown').on('hidden.bs.dropdown', function() {
        resetLoginMenu();
    });

    $.nette.ext({start: function (e) {
        if ($('#login-form').is(':visible')) {
            $('#login-form [type=submit]').button('loading');
        }
    }});
});


function resetLoginMenu()
{
    $('#login-form').hide();
    $('#login-form').find('input[name=email]').val('');
    $('li.login-list-item').show();
}