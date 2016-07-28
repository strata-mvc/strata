(function($, undefined) {

    if ($('.home').length) {
        resizeHomeBoxes();
        makeButtonPretty();


    } else if ($('.search').length) {

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

        var params = getSearchParameters();
        console.log(params);
        if (params['q']) {
            $('.search form input[type=text]').val(params['q']);
        }
    }



    function resizeHomeBoxes()
    {
        var windowHeight = $(window).height(),
            workingHeight = windowHeight < 400 ? 400 : windowHeight;

        var boxes = $('.presentation-box');
        boxes.css('height', workingHeight);

        window.addEventListener("scroll", debounce(onScroll));
        onScroll();
    }

    function makeButtonPretty()
    {
        $('.action-button').click(function(){
             $('html, body').animate({
                scrollTop: $(".presentation.presentating-right.bg-1").offset().top
            });
        });

    }

    function onScroll()
    {
        var top = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0),
            height = parseInt($('.hero').height(), 10) - 200,
            opacity = 1;

        if (height < top) {
            opacity = 0;
        } else {
            opacity = 1 - (top / height );
        }

        $('.action-button').css("opacity", opacity);
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
