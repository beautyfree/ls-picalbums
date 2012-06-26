$(document).ready(
	function() {
        function sort_elements(sortable_selector, placeholder, remote_url) {
           $(sortable_selector).sortable( {
                    handle : '.move',
                    placeholder : placeholder,
                    opacity : 0.6,
                    update : function() {
                        order = {};
                        $(sortable_selector).children('li').each(
                                function(idx, elm) {
                                    order[elm.id.split('_')[1]] = idx;
                                });

                        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]]
                                + remote_url;
                        var postData = {
                            sortdata : order
                        };

                        picalbums.ajax(posturl, postData, function(result) {
                            // Если не было получено результат
                            if (!result) {
                                jQuery.notifier.error('Error', 'Please try again later');
                                return;
                            }
                            // Результат получен, но с ошибкой
                            if (result.bStateError) {
                                jQuery.notifier.error(null, result.sMsg);
                            } else {
                                // Выводим пришедшее сообщение
                                if (result.sMsg)
                                    jQuery.notifier.notice(null, result.sMsg);

                            }
                        }.bind(this));
                    }
            });
        }

        sort_elements("#edit_pictures_listing", "pictures_setting_sort", 'ajaxsortpictures/');
        sort_elements("#category_listing_ul", "category_settings_setting_sort", 'ajaxsortcatset/');
});
