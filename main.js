$(document).ready(function() {
    $('#register-toggle').on('click', function() {
        if($('.form-container.-register:visible').length <= 0){
            $('.form-container.-register').slideDown(100);
            return;
        }
        $('.form-container.-register').slideUp(100);
    })
});