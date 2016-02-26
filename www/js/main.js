$(function () {
    $.nette.init();

    // Submit tags filter when tag is checked
    $('#tags input[type=checkbox]').on('change', function() {
        $(this).closest('form').submit();
    });
});