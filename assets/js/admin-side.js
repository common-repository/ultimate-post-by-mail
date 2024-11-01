function pwShow(variable){
    jQuery(variable).type ="text";
}
function cwsMailChange(type, val1, val2, val3){
    jQuery("#cws_mail_host").val(val1);
    jQuery("#cws_mail_security").val(val2);
    jQuery("#cws_mail_port").val(val3);
    cwsMailCurrent(type);
}

function cwsMailCurrent(type){
    if(type==="other") {
        jQuery("#cws_mail_host").prop("readonly",false);
        jQuery("#cws_mail_security").prop("readonly",false);
        jQuery("#cws_mail_port").prop("readonly",false);
        return;
    }
    jQuery("#cws_mail_host").prop("readonly",true);
    jQuery("#cws_mail_security").prop("readonly",true);
    jQuery("#cws_mail_port").prop("readonly",true);
}

(function($){
    $(document).ready(function(){
        cwsMailCurrent($('input.cws_mail_server_type:checked').val());
    });
})(jQuery);