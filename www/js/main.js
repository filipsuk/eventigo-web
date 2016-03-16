$(function () {
    $.nette.init();

    // Submit tags filter when tag is checked
    $('#tags input[type=checkbox]').on('change', function() {
        $(this).closest('form').submit();
    });

    // Move email subscription
    $('#subscription').appendTo('.subscription-box');
    $('.subscription-box input').attr('form', $('form').attr('id'));

    // Sticky subscription box
    // TODO: improve on mobile (now footer hidden on mobile to make it work)
    $(window).on("scroll", function() {
        var box = $('.subscription-box');
        // Fixed above footer
        if ($('footer')[0].getBoundingClientRect().top - $(window).height() < 0) {
            box.removeClass('fixed')
                .addClass('fixed-down');
        }
        // Fixed on bottom of the screen
        else if ($('#main')[0].getBoundingClientRect().top  - $(window).height() < -420) {
            box.addClass('fixed')
                .removeClass('fixed-down');;
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

    // When user wants to subscribe with empty email, focus on text input
    $('input[name=subscribe]').click(function(e) {
        var mail = $('#frm-subscriptionTags-form-email');
        if (mail.val().length < 5) {
            mail.focus();
            e.preventDefault(); // Prevent form submission
        }
    });

    // Scroll to flash messages if new appear
    $('#snippet--flash-messages').bind('DOMNodeInserted', '.flash', function(e) {
        if (!$('html,body').is(':animated') && $(this)[0].getBoundingClientRect().top < 0) { // scroll if needed and only once (DOMNodeInserted fires 3x)
            $('html, body').animate({
                scrollTop: $(this).offset().top - 15
            }, 300);
        }
    });

});
