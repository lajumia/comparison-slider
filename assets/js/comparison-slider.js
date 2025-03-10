jQuery(document).ready(function($) {
    // If the comparison slider is present on the page lets initialise it
    if (jQuery(".comparison-slider")[0]) {
        let compSlider = jQuery(".comparison-slider");

        // Let's loop through the sliders and initialise each of them
        compSlider.each(function() {
            let compSliderWidth = jQuery(this).width() + "px";
            jQuery(this).find(".resize img").css({ width: compSliderWidth });
            drags(jQuery(this).find(".divider"), jQuery(this).find(".resize"), jQuery(this));
        });

        // If the user resizes the window, update our variables and resize the images
        jQuery(window).on("resize", function() {
            let compSliderWidth = compSlider.width() + "px";
            compSlider.find(".resize img").css({ width: compSliderWidth });
        });
    }
});

// This is where all the magic happens
function drags(dragElement, resizeElement, container) {
    let touched = false;
    window.addEventListener('touchstart', function() {
        touched = true;
    });
    window.addEventListener('touchend', function() {
        touched = false;
    });

    dragElement.on("mousedown touchstart", function(e) {
        dragElement.addClass("draggable");
        resizeElement.addClass("resizable");

        let startX = e.pageX ? e.pageX : e.originalEvent.touches[0].pageX;
        let dragWidth = dragElement.outerWidth();
        let posX = dragElement.offset().left + dragWidth - startX;
        let containerOffset = container.offset().left;
        let containerWidth = container.outerWidth();
        let minLeft = containerOffset + 10;
        let maxLeft = containerOffset + containerWidth - dragWidth - 10;

        dragElement.parents().on("mousemove touchmove", function(e) {
            if (touched === false) {
                e.preventDefault();
            }

            let moveX = e.pageX ? e.pageX : e.originalEvent.touches[0].pageX;
            let leftValue = moveX + posX - dragWidth;

            if (leftValue < minLeft) {
                leftValue = minLeft;
            } else if (leftValue > maxLeft) {
                leftValue = maxLeft;
            }

            let widthValue = (leftValue + dragWidth / 2 - containerOffset) * 100 / containerWidth + "%";

            jQuery(".draggable").css("left", widthValue).on("mouseup touchend touchcancel", function() {
                jQuery(this).removeClass("draggable");
                resizeElement.removeClass("resizable");
            });

            jQuery(".resizable").css("width", widthValue);
        }).on("mouseup touchend touchcancel", function() {
            dragElement.removeClass("draggable");
            resizeElement.removeClass("resizable");
        });

    }).on("mouseup touchend touchcancel", function(e) {
        dragElement.removeClass("draggable");
        resizeElement.removeClass("resizable");
    });
}
