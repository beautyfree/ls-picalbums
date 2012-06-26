var ajax_uploader = null;

function getAjaxUploader() {
    return ajax_uploader;
}

function setAjaxUploader(najax_uploader) {
    ajax_uploader = najax_uploader;
}

function createUploader() {
	setAjaxUploader(new qq.FileUploader({
		element: document.getElementById('file-uploader'),
		action: aRouter[picalbumsConfig["picalbums_router_name"]]+"ajaxuploadserviceajax",
		params: {'security_ls_key': LIVESTREET_SECURITY_KEY},
		allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
		sizeLimit: picalbumsConfig["ajax_max_size_upload_file"],
		maxConnections: picalbumsConfig["ajax_upload_max_connections"],
		onComplete: function(id, fileName, responseJSON) {
								picmulti.picalbums.addPhoto(responseJSON, '');
								if(getAjaxUploader() && getAjaxUploader().getInProgress() == 0) {
									$('.album-loader').css('display', 'none');
									if(picalbumsConfig["ajax_upload_progress_disable"] == false)
										setTimeout(function() {$('.qq-upload-list').fadeOut("slow", function() { $(this).empty(); $('.qq-upload-list').css('display', 'block'); });}, 500);
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
