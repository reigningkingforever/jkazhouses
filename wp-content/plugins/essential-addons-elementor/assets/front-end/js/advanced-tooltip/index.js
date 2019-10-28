/* 40. Global Tooltip
/*=================================*/
var EaelGlobalTooltip = function($scope, $) {
    var target = $scope,
        sectionId = target.data("id"),
        editMode = elementorFrontend.isEditMode(),
        settings = {};

    if (editMode) {
        var editorElements = null,
            sectionData = {};

        if (!window.elementor.hasOwnProperty("elements")) {
            return false;
        }

        editorElements = window.elementor.elements;

        if (!editorElements.models) {
            return false;
        }

        $.each(editorElements.models, function(index, elem) {

            if( elem.id == target.closest( '.elementor-top-section' ).data( 'id' ) ) {
                $.each(elem.attributes.elements.models,function(index,col){
                    $.each(col.attributes.elements.models,function(index,subSec){
                        $.each(subSec.attributes.elements.models,function(index,subCol){
                            $.each(subCol.attributes.elements.models, function(ind, subWidget) {

                                if ( sectionId == subWidget.id ) {

                                    // console.log( widget.id );

                                    sectionData = subWidget.attributes.settings.attributes;

                                    settings.switch = sectionData["eael_tooltip_section_enable"];
                                    settings.content = sectionData["eael_tooltip_section_content"];
                                    settings.position = sectionData["eael_tooltip_section_position"];
                                    settings.animation = sectionData["eael_tooltip_section_animation"];
                                    settings.arrow = sectionData["eael_tooltip_section_arrow"];
                                    settings.arrowType = sectionData["eael_tooltip_section_arrow_type"];
                                    settings.duration = sectionData["eael_tooltip_section_duration"];
                                    settings.delay = sectionData["eael_tooltip_section_delay"];
                                    settings.size = sectionData["eael_tooltip_section_size"];
                                    settings.trigger = sectionData["eael_tooltip_section_trigger"];

                                    settings.distance = sectionData["eael_tooltip_section_distance"];
                                    settings.maxWidth = sectionData["eael_tooltip_section_width"];

                                    if ( settings.switch == "yes" ) {
                                        target.addClass("eael-section-tooltip");
                                        generateTooltip();
                                    } else {
                                        target.removeClass("eael-section-tooltip");
                                    }

                                    if (0 !== settings.length) {
                                        return settings;
                                    }
                                }

                                if (!editMode || !settings) {
                                    return false;
                                }
                            });

                        });
                    });
                });
            }


            $.each(elem.attributes.elements.models, function(inde, column) {
                $.each(column.attributes.elements.models, function(ind, widget) {

                    if ( sectionId == widget.id ) {

                        sectionData = widget.attributes.settings.attributes;
                        settings.switch = sectionData["eael_tooltip_section_enable"];
                        settings.content = sectionData["eael_tooltip_section_content"];
                        settings.position = sectionData["eael_tooltip_section_position"];
                        settings.animation = sectionData["eael_tooltip_section_animation"];
                        settings.arrow = sectionData["eael_tooltip_section_arrow"];
                        settings.arrowType = sectionData["eael_tooltip_section_arrow_type"];
                        settings.duration = sectionData["eael_tooltip_section_duration"];
                        settings.delay = sectionData["eael_tooltip_section_delay"];
                        settings.size = sectionData["eael_tooltip_section_size"];
                        settings.trigger = sectionData["eael_tooltip_section_trigger"];

                        settings.distance = sectionData["eael_tooltip_section_distance"];
                        settings.maxWidth = sectionData["eael_tooltip_section_width"];

                        if ( settings.switch == "yes" ) {
                            target.addClass("eael-section-tooltip");
                            generateTooltip();
                        } else {
                            target.removeClass("eael-section-tooltip");
                        }

                        if (0 !== settings.length) {
                            return settings;
                        }
                    }

                    if (!editMode || !settings) {
                        return false;
                    }
                });
            });

            function generateTooltip() {
                target.attr("id", "eael-section-tooltip-" + sectionId);
                var $currentTooltip = "#" + target.attr("id");

                tippy( $currentTooltip, {
                    content: settings.content,
                    placement: settings.position,
                    animation: settings.animation,
                    arrow: settings.arrow,
                    arrowType: settings.arrowType,
                    duration: settings.duration,
                    distance: settings.distance,
                    delay: settings.content,
                    size: settings.size,
                    trigger: settings.trigger,
                    animateFill: false,
                    flipOnUpdate: true,
                    interactive: true,
                    maxWidth: settings.maxWidth,
                    zIndex: 999,
                    onShow: function(instance) {
                        settings.content = sectionData["eael_tooltip_section_content"];
                        settings.position = sectionData["eael_tooltip_section_position"];
                        settings.animation = sectionData["eael_tooltip_section_animation"];
                        settings.arrow = sectionData["eael_tooltip_section_arrow"];
                        settings.arrowType = sectionData["eael_tooltip_section_arrow_type"];
                        settings.duration = sectionData["eael_tooltip_section_duration"];
                        settings.delay = sectionData["eael_tooltip_section_delay"];
                        settings.size = sectionData["eael_tooltip_section_size"];
                        settings.trigger = sectionData["eael_tooltip_section_trigger"];

                        settings.distance = sectionData["eael_tooltip_section_distance"];
                        settings.maxWidth = sectionData["eael_tooltip_section_width"];

                        // Get tooltip enable/disable status
                        settings.switch = sectionData["eael_tooltip_section_enable"];

                        // Disable tooltip
                        if( settings.switch !== 'yes' ) {

                            instance.destroy();

                        } else {

                            instance.set({
                                content: settings.content,
                                placement: settings.position,
                                animation: settings.animation,
                                arrow: settings.arrow,
                                arrowType: settings.arrowType,
                                duration: settings.duration,
                                distance: settings.distance,
                                delay: settings.delay,
                                size: settings.size,
                                trigger: settings.trigger,
                                maxWidth: settings.maxWidth
                            });

                            var tippyPopper = instance.popper;
                            $( tippyPopper ).attr('data-tippy-popper-id', sectionId);

                        }
                    }
                });
            }
        });
    }
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', EaelGlobalTooltip );
});
