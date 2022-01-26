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
        });

    $('.ui.form#reg-form')
        .form({
            fields: {
                name: ['empty', 'minLength[4]', 'maxLength[70]'],
                username: ['empty', 'minLength[4]', 'maxLength[70]'],
                email: ['empty', 'email', 'minLength[4]', 'maxLength[70]'],
                password: {
                    rules: [
                        {type: 'empty'},
                        {type: 'minLength[4]'},
                        {type: 'maxLength[32]'},
                        {type: 'match[confirm-password]', prompt: 'Password match Password Confirmation field.'}
                    ]
                }
            },
            inline: true,
            on: 'blur'
        })
    ;
});