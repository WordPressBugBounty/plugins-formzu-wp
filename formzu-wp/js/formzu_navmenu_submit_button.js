(function($){
    undefined;
    $(function(){

        function bePressableButton(eneble) {
            var visible = eneble ? 'hidden' : 'visible'; 
            var disable = eneble ? false : true;

            $('#' + formzu_ajax_obj.metabox_id).find('.spinner').css('visibility', visible);
            $('#' + formzu_ajax_obj.submit_id).prop('disabled', disable);
        }


        function submitNavFormSelect(e){
            e.preventDefault();
            bePressableButton(false);

            var form_id = $('#' + formzu_ajax_obj.select_id).val();

            if (!form_id) {
                alert("フォームID : " + form_id + "\n無効な値が入力されました。正確な値を入力してください。");
                bePressableButton(true);
                return false;
            }

            $.ajax({
                type: "POST",
                url: formzu_ajax_obj.ajaxurl,
                dataType: "html",
                data: {
                    "id": form_id,
                    "security": formzu_ajax_obj.nonce,
                    "action": formzu_ajax_obj.action
                },
                success: function(response){
                    $('#menu-to-edit').append(response);
                    bePressableButton(true);
                },
                error: function(XMLHttpRequest, textStatus, error){
                    console.error(error);
                    console.log(XMLHttpRequest);
                    console.log(textStatus);
                }
            });
            return false;
        }

        $('#' + formzu_ajax_obj.submit_id).bind('click', submitNavFormSelect);
    });
})(jQuery);

