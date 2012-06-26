var ajax_uploader = null;

function getAjaxUploader() {
    return ajax_uploader;
}

function setAjaxUploader(najax_uploader) {
    ajax_uploader = najax_uploader;
}

function createUploader() {
	setAjaxUploader(new qq.jQueryUIUploader({
		element: document.getElementById('file-uploader'),
		action: aRouter[picalbumsConfig["picalbums_router_name"]]+"ajaxuploadserviceajax",
		params: {'security_ls_key': LIVESTREET_SECURITY_KEY},
		allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
		sizeLimit: picalbumsConfig["ajax_max_size_upload_file"],
		maxConnections: picalbumsConfig["ajax_upload_max_connections"],

        template: '<div class="jq-uploader">' +
						'<div class="jq-upload-button"><i></i>' + ls.lang.get('picalbums_ajaxuploader_button_title') + '</div>' +
						'<ul class="jq-upload-list"></ul>' +
						'<div class="jq-upload-dialog"></div>' +
					'</div>',

        fileTemplate: '<li>' +
							ls.lang.get('picalbums_swf_file') + ': <span class="jq-upload-file"></span>' +
							'(<span class="jq-upload-size"></span>)' +
							'<br/><div class="jq-upload-progress"></div>' +
							'<button class="jq-upload-cancel" ><i></i><span>' + ls.lang.get('picalbums_ajaxuploader_cancel') + '</span></button>' +
							'<span class="jq-upload-failed-text">' + ls.lang.get('picalbums_ajaxuploader_failed') + '</span>' +
						'</li>',

		onComplete: function(id, fileName, responseJSON) {
								picmulti.picalbums.addPhoto(responseJSON, '');
								if(getAjaxUploader() && getAjaxUploader().getInProgress() == 0) {
									$('.album-loader').css('display', 'none');
									if(picalbumsConfig["ajax_upload_progress_disable"] == false)
										setTimeout(function() {$('.jq-upload-list').fadeOut("slow", function() { $(this).empty(); $('.jq-upload-list').css('display', 'block'); });}, 500);
								}
							},
		onSubmit: function(id, fileName){
								$('.album-loader').css('display', 'block');
							},
		debug: false
	}));
}

$(document).ready(function() {
    createUploader();
	$('ul.multiupload_tabs li').css('cursor', 'pointer');
	var isFlashLoad = false;
    $('ul.multiupload_tabs.multiupload_tabs1 li').click(function(){
        var thisClass = this.className.slice(0,16);
        if(thisClass == 'multiupload_tab2') {
            if(isFlashLoad == false) {
                if ($.browser.flash) {
                    picmulti.picalbums.initSwfUpload({
                        post_params: { }
                    });
                }
            }
            isFlashLoad = true;
        }

        $('div.multiupload_tab1').css('display','none');
        $('div.multiupload_tab2').css('display','none');
        $('div.multiupload_tab3').css('display','none');
        $('div.' + thisClass).css('display','block');
        $('ul.multiupload_tabs.multiupload_tabs1 li').removeClass('tab-current');
        $(this).addClass('tab-current');
    });
});
