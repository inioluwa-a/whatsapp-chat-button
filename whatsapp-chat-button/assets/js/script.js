/**
 * WhatsApp Chat Button JavaScript
 * 
 * Handles the WhatsApp button functionality including:
 * - Opening WhatsApp chat when clicked
 * - Scroll behavior
 * - Hover effects
 */
(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        const button = $('#whatsapp-chat-button');
        const wcbButton = $('.wcb-button');
        let lastScrollTop = 0;
        let scrollTimeout;
        
        /**
         * Handle button click
         */
        wcbButton.on('click', function(e) {
            e.preventDefault();
            
            const number = wcbSettings.whatsappNumber;
            const message = encodeURIComponent(wcbSettings.customMessage || '');
            
            if (!number) {
                console.error('WhatsApp number not configured');
                return;
            }
            
            // Format the number (remove spaces, dashes, etc.)
            const formattedNumber = number.replace(/[\s-]/g, '');
            
            // Create WhatsApp URL
            const whatsappUrl = `https://wa.me/${formattedNumber}?text=${message}`;
            
            // Open WhatsApp in a new tab
            window.open(whatsappUrl, '_blank');
            
            // Track click event if analytics is available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'whatsapp_click', {
                    'event_category': 'WhatsApp',
                    'event_label': 'Chat Button'
                });
            }
        });
        
        /**
         * Handle scroll behavior with debounce
         */
        $(window).on('scroll', function() {
            // Clear previous timeout
            clearTimeout(scrollTimeout);
            
            // Set new timeout
            scrollTimeout = setTimeout(function() {
                const st = $(window).scrollTop();
                
                // Show/hide button based on scroll direction
                if (st > lastScrollTop && st > 100) {
                    // Scrolling down and not at the top
                    button.addClass('hide');
                } else {
                    // Scrolling up or at the top
                    button.removeClass('hide');
                }
                
                lastScrollTop = st;
            }, 100); // 100ms debounce
        });
        
        /**
         * Add hover effect with CSS transitions
         */
        wcbButton.hover(
            function() {
                $(this).addClass('wcb-hover');
            },
            function() {
                $(this).removeClass('wcb-hover');
            }
        );
        
        /**
         * Check if device is mobile
         */
        function isMobile() {
            return window.matchMedia('(max-width: 768px)').matches;
        }
        
        /**
         * Adjust button position on resize
         */
        $(window).on('resize', function() {
            if (isMobile()) {
                button.addClass('wcb-mobile');
            } else {
                button.removeClass('wcb-mobile');
            }
        }).trigger('resize');
    });
    
})(jQuery); 