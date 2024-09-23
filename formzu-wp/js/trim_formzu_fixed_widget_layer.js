(function($){
    var $fixed_widgets = $('.formzu-fixed-widget');
    var $widget;
    var widget_bottom = {
        "left": 20,
        "right": 20
    };
    var max_width = {
        "left": 0,
        "right": 0
    };
    var left_or_right;
    var widget_height = 0;
    var widget_width = 0;

    function isRightOrLeft($elem){

        var right = $elem.css('right');
        var left = $elem.css('left');

        if (right == '0px' && left == '0px') {
            return false;
        }
        if (right == '0px') {
            return 'right';
        }
        if (left == '0px'){
            return 'left';
        }
        return false;
    }

    for (var i = 0, l = $fixed_widgets.length; i < l; i++) {
        if (!$fixed_widgets[i]) {
            continue;
        }

        $widget = $($fixed_widgets[i]);
        left_or_right = isRightOrLeft($widget)

        if (!left_or_right) {
            continue;
        }

        $widget.css('bottom', widget_bottom[left_or_right]);

        widget_height = $widget.outerHeight(true);

        widget_bottom[left_or_right] += widget_height;

        var user_agent = window.navigator.userAgent.toLowerCase();

        if (user_agent.indexOf('msie') != -1) {//ie6 - ie10
            widget_width = $widget.width();
        } else if (user_agent.indexOf('trident/7') != -1) {//ie11
            widget_width = $widget.width();
        } else {
            widget_width = $widget.outerWidth(true);
        }

        if (max_width[left_or_right] < widget_width) {
            max_width[left_or_right] = widget_width;
        }
    }

    for (var i = 0, l = $fixed_widgets.length; i < l; i++) {
        if (!$fixed_widgets[i]) {
            continue;
        }

        $widget = $($fixed_widgets[i]);
        left_or_right = isRightOrLeft($widget)

        if (!left_or_right) {
            continue;
        }

        $widget.css('width', max_width[left_or_right] + 10);
    }
})(jQuery);

