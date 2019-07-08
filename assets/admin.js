;(function ($, window, document, undefined) {
    $(function () {
        
        var image = $('.wp-jamstack-deployments-badge img');
        var imageSrc = image.prop('src');
        var refreshTimout = null;
        
        var updateNetlifyBadgeUrl = function () {
            if (!image.length) {
                return;
            }
            var d = new Date();
            image.prop('src', imageSrc + '?v=s_' + d.getTime());
            refreshTimout = setTimeout(updateNetlifyBadgeUrl, 15000);
        };

        refreshTimout = setTimeout(updateNetlifyBadgeUrl, 15000);
        
        $('.wp-jamstack-deployments-button').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: wpjd.ajaxurl,
                data: {
                    action: 'wp_jamstack_deployments_manual_trigger',
                    security: wpjd.deployment_button_nonce,
                },
                dataType: 'json',
                success: updateNetlifyBadgeUrl,
            });
            clearTimeout(refreshTimout);
        });

    });
})(jQuery, window, document);
