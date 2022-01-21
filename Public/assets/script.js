$(document).ready(function () {
    // fix main menu to page on passing
    $(".main.menu").visibility({
        type: "fixed"
    });

    // // lazy load images
    // $(".image").visibility({
    //     type: "image",
    //     transition: "vertical flip in",
    //     duration: 500
    // });

    // show dropdown on hover
    $(".main.menu  .ui.dropdown").dropdown({
        on: "click"
    });

    $('.ui.label.with-popup').popup({
            on: 'hover'
        });

    // profile menu
    $('.profile-menu .item').tab();

    $('.ui.upward.dropdown.item')
        .dropdown({
            direction: 'upward'
        })
    ;

});