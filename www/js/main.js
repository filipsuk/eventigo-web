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

});
