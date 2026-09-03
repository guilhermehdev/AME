//add submit class to a button
//use data-required(bool) and data-msg-required(string) on form controls.
//return true if all fields are validated.

(function ( $ ) {
            
$.fn.formValidatePost = function(){
        
        var form = $(this);         
        var action = form.attr('action');
        var formdata = form.serializeArray();
        var valid = false; 
                                  
        $.each(formdata,function( index, obj ) {
            var el = form.find($('[name=' + obj.name + ']'));
            var req = el.data('required');                             
            var msg = el.data('msg-required');
            var value = el.val();
                                              
            if(req == true && value === '') { 
                $('#tip-'+obj.name).remove();
                var tip = $('<div id=\"tip-'+obj.name+'\"><span style=\"position:absolute;left:15px;margin-top:-9px;border:10px solid #ccc;display:block;background-color: transparent;border-top-color:transparent;border-left-color:transparent;border-right-color:transparent;border-bottom-color:#d9534f;z-index: 9200;\" ></span>\n\
<span style=\"position: absolute;margin-top: 10px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;font-size: 11px;color: #fff;background-color: #d9534f;border: 1px #fff solid;border-radius: 7px;box-shadow: 3px 3px 3px rgba(0,0,0,0.3);z-index: 9200;cursor: pointer;padding: 5px 8px;\" id=\"popup\">'+msg+'</span></div>');
                
                tip.insertAfter($(el));              
                $(el).focus();           
                valid = false;  
                
                document.getElementById('popup').addEventListener("click", function(){
                    $('#tip-'+obj.name).remove();
                });

                document.getElementById($(el).attr('id')).addEventListener("input", function(){
                    if(el.val().length > 0){
                        $(el).removeClass('border-red').addClass('borderless');
                        $('#tip-'+obj.name).remove();
                        valid = true;
                    } else {
                        $('#tip-'+obj.name).remove();
                        tip.insertAfter($(el));
                        $(el).focus().addClass('border-red');           
                        valid = false;
                    }
                });
                return false;
            } else {           
                $('#tip-'+obj.name).remove();
                valid = true;
            } 

        });
            
        $.ajax({
            type: "POST",
            url: action,
            data: formdata,
            beforeSend: function() { 
                $(form).find('.submit').attr('disabled','true');
                if(!valid){
                    $(form).find('.submit').removeAttr('disabled');                        
                    return false;
                }                        
            },
            success: function (result) {
                
            },
            error: function (request, status, erro) {
                               
            }
        }).done(function() {            
            $(form).find('.submit').removeAttr('disabled');
        }).fail(function() {

        });
        
        return valid;
};
})(jQuery);