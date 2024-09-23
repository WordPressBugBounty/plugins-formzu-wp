(function($){
    function resizeThickbox(){

        var window_height = window.innerHeight
            || (document.documentElement && document.documentElement.clientHeight)
            || document.body.clientHeight;

        window_height -= 100;

        $('.thickbox').each(function(index, elem){

            var href = $(elem).attr('href');

            if (!href || typeof href != 'string') {
                return true;//jQuery continue
            }

            var splited_href = href.split('&height=');

            if (!splited_href[1]) {
                return true;//jQuery continue
            }

            var latter_half_array = splited_href[1].split('&');
            var thickbox_height = latter_half_array.shift();

            if (!thickbox_height.match(/^\d*$/g)) {
                return true;//jQuery continue
            }

            var origin_height = $(elem).attr('data-origin-height');

            if (Number(thickbox_height) < origin_height) {
                thickbox_height = origin_height;
            }
            if (Number(thickbox_height) <= window_height) {
                return true;
            }

            thickbox_height = window_height;
            var former_half_href = splited_href[0];
            var latter_half_href = latter_half_array.join('&');

            $(elem).attr('href', function(){

                var new_href = former_half_href + '&height=' + thickbox_height;

                if (latter_half_href && latter_half_href.match(/\S/g)) {
                    new_href += '&' + latter_half_href;
                }
                return new_href;
            });
        });
    }
    resizeThickbox();

    var resize_timeout = false;

    $(window).resize(function(){
        if (resize_timeout !== false) {
            clearTimeout(resize_timeout);
        }
        resize_timeout = setTimeout(function(){
            resizeThickbox();
        }, 200);
    });
})(jQuery);

