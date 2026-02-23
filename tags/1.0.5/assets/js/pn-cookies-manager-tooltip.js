(function($) {
    'use strict';

    class PN_COOKIES_MANAGER_Tooltip {
        constructor(element, options = {}) {
            this.element = element;
            this.$element = $(element);
            this.options = {
                maxWidth: 300,
                position: 'top',
                delayTouch: [0, 4000],
                ...options
            };

            this.isVisible = false;
            this.tooltip = null;
            this.touchTimeout = null;
            this.init();
        }

        init() {
            this.content = this.$element.attr('title') || '';
            this.$element.removeAttr('title');

            if (!this.content) return;

            this.createTooltip();
            this.bindEvents();
        }

        createTooltip() {
            this.tooltip = $(
                '<div class="pn-cookies-manager-tooltip-box pn-cookies-manager-tooltip-box--top">' +
                    '<div class="pn-cookies-manager-tooltip-box__content"></div>' +
                    '<div class="pn-cookies-manager-tooltip-box__arrow"></div>' +
                '</div>'
            );

            this.tooltip.find('.pn-cookies-manager-tooltip-box__content').text(this.content);
            this.tooltip.css('max-width', this.options.maxWidth + 'px');

            $('body').append(this.tooltip);
        }

        bindEvents() {
            this.$element.on('mouseenter.pncmTooltip', () => this.show());
            this.$element.on('mouseleave.pncmTooltip', () => this.hide());

            this.$element.on('touchstart.pncmTooltip', () => {
                if (this.isVisible) {
                    this.hide();
                } else {
                    this.show();
                    if (this.options.delayTouch[1] > 0) {
                        clearTimeout(this.touchTimeout);
                        this.touchTimeout = setTimeout(() => this.hide(), this.options.delayTouch[1]);
                    }
                }
            });
        }

        show() {
            if (!this.tooltip || this.isVisible) return;

            this.position();
            this.tooltip.addClass('pn-cookies-manager-tooltip-box--visible');
            this.isVisible = true;
        }

        hide() {
            if (!this.tooltip || !this.isVisible) return;

            this.tooltip.removeClass('pn-cookies-manager-tooltip-box--visible');
            this.isVisible = false;
            clearTimeout(this.touchTimeout);
        }

        position() {
            var elemRect = this.element.getBoundingClientRect();

            // Temporarily show for measuring
            this.tooltip.css({ visibility: 'hidden', opacity: 0 }).addClass('pn-cookies-manager-tooltip-box--visible');
            var tooltipRect = this.tooltip[0].getBoundingClientRect();
            this.tooltip.removeClass('pn-cookies-manager-tooltip-box--visible').css({ visibility: '', opacity: '' });

            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            var top, left;
            var position = this.options.position;

            // Top position (default)
            top = elemRect.top + scrollTop - tooltipRect.height - 10;
            left = elemRect.left + scrollLeft + (elemRect.width / 2) - (tooltipRect.width / 2);

            // Flip to bottom if not enough space above
            if (top - scrollTop < 5) {
                position = 'bottom';
                top = elemRect.bottom + scrollTop + 10;
            }

            // Clamp horizontal position
            if (left < scrollLeft + 5) {
                left = scrollLeft + 5;
            } else if (left + tooltipRect.width > scrollLeft + window.innerWidth - 5) {
                left = scrollLeft + window.innerWidth - tooltipRect.width - 5;
            }

            this.tooltip
                .removeClass('pn-cookies-manager-tooltip-box--top pn-cookies-manager-tooltip-box--bottom')
                .addClass('pn-cookies-manager-tooltip-box--' + position);

            this.tooltip.css({
                top: top + 'px',
                left: left + 'px'
            });
        }

        destroy() {
            this.$element.off('.pncmTooltip');
            if (this.content) {
                this.$element.attr('title', this.content);
            }
            if (this.tooltip) {
                this.tooltip.remove();
            }
            clearTimeout(this.touchTimeout);
            this.$element.removeData('pncm-tooltip');
        }
    }

    // jQuery plugin
    $.fn.PN_COOKIES_MANAGER_Tooltip = function(options) {
        return this.each(function() {
            if (!$(this).data('pncm-tooltip')) {
                $(this).data('pncm-tooltip', new PN_COOKIES_MANAGER_Tooltip(this, options));
            }
        });
    };

    // Global: hide all tooltips on outside click/touch
    $(document).on('mousedown touchstart', function(e) {
        if (!$(e.target).closest('.pn-cookies-manager-tooltip').length &&
            !$(e.target).closest('.pn-cookies-manager-tooltip-box').length) {
            $('.pn-cookies-manager-tooltip-box--visible').each(function() {
                $(this).removeClass('pn-cookies-manager-tooltip-box--visible');
            });
            $('.pn-cookies-manager-tooltip').each(function() {
                var instance = $(this).data('pncm-tooltip');
                if (instance) {
                    instance.isVisible = false;
                    clearTimeout(instance.touchTimeout);
                }
            });
        }
    });

    // Hide tooltips on scroll
    $(window).on('scroll', function() {
        $('.pn-cookies-manager-tooltip-box--visible').each(function() {
            $(this).removeClass('pn-cookies-manager-tooltip-box--visible');
        });
        $('.pn-cookies-manager-tooltip').each(function() {
            var instance = $(this).data('pncm-tooltip');
            if (instance) {
                instance.isVisible = false;
                clearTimeout(instance.touchTimeout);
            }
        });
    });

    // Hide tooltips on ESC key
    $(document).on('keyup', function(e) {
        if (e.key === 'Escape') {
            $('.pn-cookies-manager-tooltip-box--visible').each(function() {
                $(this).removeClass('pn-cookies-manager-tooltip-box--visible');
            });
            $('.pn-cookies-manager-tooltip').each(function() {
                var instance = $(this).data('pncm-tooltip');
                if (instance) {
                    instance.isVisible = false;
                    clearTimeout(instance.touchTimeout);
                }
            });
        }
    });

})(jQuery);
