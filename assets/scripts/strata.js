(function($, undefined) {

    var _cache = [];


    if ($('.home').length) {
        resizeHomeBoxes();
        makeButtonPretty();

    } else if ($('.search').length) {

        var params = getSearchParameters();
        if (params['q']) {
            $('.search form input[type=text]').val(params['q']);
        }
    }

    // =========

    function resizeHomeBoxes()
    {

        // Give objects room to animate.
        $('*[data-slide]').css("opacity", 0);

        cacheSizes();

        // Reset predefined background colors on objects that will
        // fade in.
        $('*[data-focus-color], *[data-highlight-color]').css('background', 'transparent');

        onScroll();

        window.addEventListener("scroll", debounce(onScroll));
        window.addEventListener("resize", debounce(onResize));
    }

    function makeButtonPretty()
    {
        $('.action-button').click(function(){
            var top = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0);

            for (var i = 1; i < _cache['color-stops'].length; i++) {
                if (top + 30 < _cache['color-stops'][i].focusAt) {
                    $('html, body').animate({
                        'scrollTop' : _cache['color-stops'][i].focusAt
                    });
                    break;
                }
            }
        });
    }

    function cacheSizes()
    {
        var windowHeight = $(window).height(),
            workingHeight = windowHeight < 400 ? 400 : windowHeight,
            halfHeight = workingHeight / 2,
            top = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0);

        _cache['heights'] = {
            'window' : windowHeight,
            'working' : workingHeight
        };

        $('*[data-push-top]').css("margin-top", halfHeight);
        $('*[data-push-bottom]').css("margin-bottom", halfHeight);

        // Keep the color stops close-by as we will be calling them often.
        _cache['color-stops'] = [];

        $('*[data-step]').each(function(){
            var element = $(this),
                top = element.offset().top;

            _cache['color-stops'].push({
                focusAt: top + halfHeight,
                changeOn: top - (workingHeight * .3),
                node: element
            });
        });
    }

    function onResize()
    {
        cacheSizes();
        onScroll();
    }

    function onScroll()
    {
        var top = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0),
            bgcolor = false,
            bgcolorHighlight = false,
            nextElement = null,
            i = 0,
            len = _cache['color-stops'].length;

        for ( ; i < len; i++) {
            if (top < _cache['color-stops'][i].changeOn) {
                break;
            }
        }

        // Take a step back to get the last valid answer
        i--;

        // Apply coloring based on the current focus element
        var currentElement = _cache['color-stops'][i].node;
        currentElement.css("opacity", 1);
        bgcolor = currentElement.attr("data-focus-color");

        var highlight = currentElement.find('*[data-highlight-color]');
        if (highlight.length > 0) {
            bgcolorHighlight = highlight.attr("data-highlight-color");
        }

        if (i+1 >= _cache['color-stops'].length) {
            $('.action-button').hide();
        } else {
            $('.action-button').show();
            $('.action-button').css("background-color", _cache['color-stops'][i+1].node.attr("data-focus-color"));
        }

        if (bgcolor) {
            $('.home, *[data-focus-color]').css("background-color", bgcolor);
        }

        if (bgcolorHighlight) {
            $('*[data-highlight-color]').css("background-color", bgcolorHighlight);
        } else if (bgcolor) {
            $('*[data-highlight-color]').css("background-color", bgcolor);
        }



        // Hide the previous blocks
        for (var previous = (i - 1); previous > 0; previous--) {
            _cache['color-stops'][previous].node.css("opacity", 0);
        }

        // Hide the upcoming blocks
        for (var remaining = (i + 1); remaining < len; remaining++) {
            _cache['color-stops'][remaining].node.css("opacity", 0);
        }

    }


    function getSearchParameters() {
        var prmstr = window.location.search.substr(1);
        return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
    }

    function transformToAssocArray( prmstr ) {
        var params = {};
        var prmarr = prmstr.split("&");
        for ( var i = 0; i < prmarr.length; i++) {
            var tmparr = prmarr[i].split("=");
            params[tmparr[0]] = tmparr[1];
        }
        return params;
    }

    function debounce(func, wait, immediate)
    {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };



})(jQuery);
