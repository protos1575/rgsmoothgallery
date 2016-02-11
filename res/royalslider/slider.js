;(function ( $, window, document, undefined ) {
    "use strict";

    if (!$.matthies) {
        $.matthies = {};
    }

    var defaultSliderOptions = {
        // options go here
        // as an example, enable keyboard arrows nav
        //keyboardNavEnabled: true
        // autoHeight: true,
        // minAutoHeight: '300px',
        autoScaleSlider: true,
        autoScaleSliderWidth: 200,
        autoScaleSliderHeight: 200,
        //imageScaleMode: 'fill',
        /*
         imageScaleMode: function (slideObject) {
         if (slideObject.isFullscreen) {
         return 'fit-if-smaller';
         }
         return 'fill';
         },
         */
        imageScalePadding: 0,
        //slidesSpacing: 0,
        //addActiveClass: true,
        transitionType: 'fade',
        transitionSpeed: 1000,
        //arrowsNavAutoHide: false,
        arrowsNavHideOnTouch: true,
        /*
        thumbs: {
            spacing: 1,
            navigation: false

        },
        */
        //controlNavigation: \'thumbnails\',
        //		controlNavigation: function(slideObject) {
        //			return \'thumbnails\';
        //		},
        //controlNavigation: 'tabs',
        //controlNavigation: 'bullets',
        controlNavigation: 'thumbnails',
        fullscreen: {
            enabled: true
            //buttonFS: false,
            /*
             buttonFS: function(slideObject) {
             return slideObject.isFullscreen;
             },
             */
            //					nativeFS: true,
        },
        autoPlay: {
        //    enabled: true
        },
        loop: true,
        video: {
            autoHideArrows: false,
            //    autoHideControlNav: true,
            youTubeCode: '<iframe src="https://www.youtube.com/embed/%id%?rel=1&autoplay=1&showinfo=1" frameborder="no" width="560" height="315"></iframe>'
        }
    };

    defaultSliderOptions.transitionType = 'slide';


    var thumbSliderOptions = $.extend(true, {}, defaultSliderOptions);
    thumbSliderOptions.controlNavigation = 'bullets';
//	thumbSliderOptions.autoScaleSliderHeight = 5;

    $.matthies.applySlider = function(elements) {

        $.each(elements, function (index, value) {
            if ($(value).children().length) {
                var options = defaultSliderOptions;

                if ($("*", $(value)).length > 1) {
                    options = thumbSliderOptions;
                    //console.log('thumbslideroptiones');
                }

                var sliderObj = $(value).royalSlider(options),
                    slider = sliderObj.data('royalSlider');

                if (slider.hasTouch) {
                    //$(".rsFullscreenBtn", sliderObj).removeClass("rsHidden");
                } else {
                    var btn = $(value).find('.rsFullscreenBtn').addClass('rsHidden');
                    $(value).hover(function () {
                        btn.removeClass('rsHidden');
                    }, function () {
                        btn.addClass('rsHidden');
                    });

                }

                $('.rsContainer', $(value)).on('click', function(e) {
                    //console.log(e.target);
                    if($(e.target).hasClass('rsSlide')) {
                        slider.exitFullscreen();
                    }
                });


                if (sliderObj) {


                    slider.updateSliderSize = function (force) {
                        $.rsProto.updateSliderSize.call(this, force);
                    };

                    slider.ev.on('rsEnterFullscreen', function () {
//                        slider.stopAutoPlay();
                        //    slider.st.imageScaleMode = "fit-if-smaller";
                        slider.st.fullscreen.buttonFS = true;
                        setTimeout(function () {
                            //        slider.updateSliderSize(true);
                        }, 200);
                    });

                    slider.ev.on('rsExitFullscreen', function () {
//                        slider.startAutoPlay();
                        slider.stopVideo();
                        //    slider.st.imageScaleMode = "fill";
//                        slider.st.fullscreen.buttonFS = false;
                        setTimeout(function () {
                            //        slider.updateSliderSize(true);
                        }, 2000);
                    });

                    slider.ev.on('rsOnCreateVideoElement', function (e, url) {
                        if (!slider.isFullscreen) {
                            slider.enterFullscreen();
                            setTimeout(function () {
                                slider.updateSliderSize(true);
                            }, 200);
                        }
                        setTimeout(function () {
                            slider.updateSliderSize(true);
                        }, 20);
                        // url - path to video from data-rsVideo attribute
                        // slider.videoObj - jQuery object that holds video HTML code
                        // slider.videoObj must be IFRAME, VIDEO or EMBED element, or have class rsVideoObj
                        //					slider.videoObj = $('<iframe title="YouTube video player" width="640" height="480" src="' + url + '?rel=0&amp;hd=1?autoplay=1&amp;controls=1&amp;showinfo=1&amp;rel=0&amp;autohide=0&rel=0" frameborder="0" allowfullscreen></iframe>');
                    });
                    slider.ev.on('rsOnDestroyVideoElement', function (e) {
//                        slider.stopAutoPlay();
                        // slider.videoObj - jQuery object that holds video HTML code
                        if (slider.numSlides == 1) {
                            slider.exitFullscreen();
                        }
                    });

                    $(value).closest(".royalSlider").mouseleave(function (e) {
                        //slider.goTo(0);
//                        slider.toggleAutoPlay();
                    });

                    slider.ev.on('rsSlideClick', function () {
                        //console.log('slide click', slider, slider.ev.target);
                        if (slider.isFullscreen) {
                            //slider.exitFullscreen();
                        }
                    });
                }
            }

        });
    }

})( jQuery, window, document );

jQuery(document).ready(function ($) {

    $.matthies.applySlider($('.royalSlider'));
});
