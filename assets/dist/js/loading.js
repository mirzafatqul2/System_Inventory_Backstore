$(window).on('load', function () {
    $('#loading-overlay').fadeOut();
});

window.addEventListener('pageshow', function () {
    $('#loading-overlay').fadeOut();
});

$('form').on('submit', function () {
    $('#loading-overlay').fadeIn();
});

$('a').on('click', function (e) {
    const href = $(this).attr('href');
    const target = $(this).attr('target');
    if (href && !href.startsWith('#') && !target) {
        $('#loading-overlay').fadeIn();
    }
});
