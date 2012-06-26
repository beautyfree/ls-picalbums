var picmulti = {};
picmulti.picalbums = (function() {
    this.swfu;

    this.initSwfUpload = function(opt) {
        $(picmulti.swfupload).bind('load', function() {
            this.swfu = picmulti.swfupload.init({});

            $(this.swfu).bind('eUploadProgress', this.swfHandlerUploadProgress);
            $(this.swfu).bind('eFileDialogComplete', this.swfHandlerFileDialogComplete);
            $(this.swfu).bind('eUploadSuccess', this.swfHandlerUploadSuccess);
            $(this.swfu).bind('eUploadComplete', this.swfHandlerUploadComplete);
            $(this.swfu).bind('eFileQueued', {swfu:this.swfu}, this.swfHandlerFileQueued);
            $(this.swfu).bind('eFileQueueError', this.swfHandlerFileQueueError);

            $(this.swfu).bind('eUploadStart', this.swfHandlerUploadStart);
        }.bind(this));

        picmulti.swfupload.loadSwf();
    }

    this.swfHandlerUploadStart = function(e, file) {
        $('#log li#' + file.id).find('span.progressvalue').text('0%');
    }


    this.swfHandlerFileQueued = function(e, file, errorCode, message) {
        alert('Size of the file ' + file.name + ' is greater than limit');
    }

    this.swfHandlerFileQueued = function(e, file) {
        var listitem='<li id="'+file.id+'" >'+
                ls.lang.get('picalbums_swf_file') + ': <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
                '<div class="progressbar" ><div class="progress" ></div></div>'+
                '<button class="cancel" ><i></i>' + ls.lang.get('picalbums_ajaxuploader_cancel') + '</button>'+
                '</li>';
        
        $('#log').append(listitem);
        $('#log').attr('style', '');
        $('#queuestatus').attr('style', '');
        $('.flash-album-loader').css('display', 'block');

        $('li#' + file.id + ' .cancel').bind('click', function() {
            e.data.swfu.cancelUpload(file.id);
            $('li#' + file.id).slideUp('fast');
        });
    }

    this.swfHandlerUploadProgress = function(e, file, bytesLoaded, percent) {
        //Show Progress
        $('#log li#' + file.id).find('div.progress').css('width', percent + '%');
        $('#log li#' + file.id).find('span.progressvalue').text(percent + '%');
    }

    this.swfHandlerFileDialogComplete = function(e, numFilesSelected, numFilesQueued) {
        $('#queuestatus').text('Files Selected: ' + numFilesSelected + ' / Queued Files: ' + numFilesQueued);
    }

    this.swfHandlerUploadSuccess = function(e, file, serverData) {
        var item = $('#log li#' + file.id);
        item.find('div.progress').css('width', '100%');
        item.find('span.progressvalue').text('100%');

        picmulti.picalbums.addPhoto($.parseJSON(serverData), '');
    }

    this.swfHandlerUploadComplete = function(e, file, next) {
        if (next == 0) {
            $('.flash-album-loader').css('display', 'none');
            $('#log').fadeOut("slow", function() {
                $(this).empty();
            });
            $('#queuestatus').fadeOut("slow", function() {
                $(this).html('');
            });
        }
    }

    this.addPhoto = function(response, description) {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxaddpicture/';

        var postData = { album_target_id: picalbums_album_id,
                         picture_description : description,
                         picture_path_html: response.sFilePictureUpload,
                         picture_path_minimal_html: response.sFileMiniatureUpload,
                         picture_path_original_html: response.sFileOriginalUpload,
                         exif: response.exif,
                         picture_path_block_html: response.sFileBlockUpload};

        picalbums.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
                if (result.docanccel && result.docanccel == 'do') {
                    if (getAjaxUploader()) {
                        getAjaxUploader()._handler.cancelAll();
                        $('.album-loader').css('display', 'none');
                        if (picalbumsConfig["ajax_upload_progress_disable"] == false)
                            setTimeout(function() {
                                $('.qq-upload-list').fadeOut("slow", function() {
                                    $(this).empty();
                                    $('.qq-upload-list').css('display', 'block');
                                });
                            }, 500);
                    }
                }
            } else {
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                $('#picture_img_title').val('');
                $('#picture_img_file').val('');

                if (result.aResult) {
                    $('.pictures_for_ajax_upload #empty_album').remove();
                    $('.pictures_for_ajax_upload').append(result.aResult);
                }
            }
        }.bind(this));
    }

    this.upload = function() {
        picalbums.ajaxSubmit(aRouter[picalbumsConfig["picalbums_router_name"]] + "ajaxuploadserviceflash", $('#picalbums-upload-form'), function(data) {
            if (data.bStateError) {
                $('#picalbums_photo_empty').remove();
                $.notifier.error(data.sMsgTitle, data.sMsg);
            } else {
                picmulti.picalbums.addPhoto(data, $('#picalbums-upload-form').find('[name="title"]').val());
            }
        });
    }

    return this;
}).call({});

picmulti.swfupload = (function() {
    this.swfu = null;
    this.initOptions = function() {
        this.swfOptions = {
            // Backend Settings
            upload_url: aRouter[picalbumsConfig["picalbums_router_name"]] + "ajaxuploadserviceflash",
            post_params: {'SSID':SESSION_ID, 'security_ls_key': LIVESTREET_SECURITY_KEY},

            // File Upload Settings
            file_types : "*.jpg; *.JPG;*.png;*.gif;*.jpeg;",
            file_types_description : "Images",
            file_upload_limit : "0",

            // Event Handler Settings
            file_queued_handler : this.handlerFileQueued,
            file_queue_error_handler : this.handlerFileQueueError,
            file_dialog_complete_handler : this.handlerFileDialogComplete,
            upload_start_handler :this.handlerUploadStart,
            upload_progress_handler : this.handlerUploadProgress,
            upload_error_handler : this.handlerUploadError,
            upload_success_handler : this.handlerUploadSuccess,
            upload_complete_handler : this.handlerUploadComplete,

            // Button Settings
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_text_left_padding: 6,
            button_text_top_padding: 3,
            button_cursor: SWFUpload.CURSOR.HAND,

            button_width : 114,
            button_height : 29,
            button_placeholder : $('#swfuploadbutton')[0],
            button_image_url: DIR_PICALBUM_PLUGIN + "/images/fuploadb.png",

            // Flash Settings
            flash_url : DIR_ROOT_ENGINE_LIB + '/external/swfupload/swfupload.swf',

            custom_settings : {},

            // Debug Settings
            debug: false
        };
    }

    this.loadSwf = function() {
        $.getScript(DIR_ROOT_ENGINE_LIB + '/external/swfupload/swfupload.swfobject.js', function() {

        }.bind(this));

        $.getScript(DIR_ROOT_ENGINE_LIB + '/external/swfupload/swfupload.js', function() {
            this.initOptions();
            $(this).trigger('load');
        }.bind(this));
    }

    this.init = function(opt) {
        if (opt) {
            $.extend(true, this.swfOptions, opt);
        }
        this.swfu = new SWFUpload(this.swfOptions);
        return this.swfu;
    }

    this.handlerUploadStart = function(file) {
        $(this).trigger('eUploadStart', [file]);
    }

    this.handlerFileQueueError = function(file, errorCode, message) {
        $(this).trigger('eFileQueueError', [file, errorCode, message]);
    }

    this.handlerFileDialogComplete = function(numFilesSelected, numFilesQueued) {
        $(this).trigger('eFileDialogComplete', [numFilesSelected, numFilesQueued]);
    }

    this.handlerUploadProgress = function(file, bytesLoaded) {
        var percent = Math.ceil((bytesLoaded / file.size) * 100);
        $(this).trigger('eUploadProgress', [file, bytesLoaded, percent]);
    }

    this.handlerUploadError = function(file, errorCode, message) {
        $(this).trigger('eUploadError', [file, errorCode, message]);
    }

    this.handlerFileQueued = function(file) {
        $(this).trigger('eFileQueued', [file]);
        this.startUpload();
    }

    this.handlerUploadSuccess = function(file, serverData) {
        $(this).trigger('eUploadSuccess', [file, serverData]);
    }

    this.handlerUploadComplete = function(file) {
        var next = this.getStats().files_queued;
        if (next > 0) {
            this.startUpload();
        }
        $(this).trigger('eUploadComplete', [file, next]);
    }

    return this;
}).call({});

var picalbums = (function() {
    this.ajaxSubmit = function(url, form, callback, more) {
        more = more || {};
        if (typeof(form) == 'string') {
            form = $('#' + form);
        }
        if (url.indexOf('http://') != 0 && url.indexOf('https://') != 0) {
            url = aRouter['ajax'] + url + '/';
        }

        var options = {
            type: 'POST',
            url: url,
            dataType: more.dataType || 'json',
            data: { security_ls_key: LIVESTREET_SECURITY_KEY },
            success: callback || function(msg) {
                console.log("base success: ");
                console.log(msg);
            }.bind(this),
            error: more.error || function(x, s, e) {
                console.log("base error: ");
                console.log(x);
            }.bind(this)
        }

        form.ajaxSubmit(options);
    }

    // Выполнение AJAX запроса, автоматически передает security key
    this.ajax = function(url, params, callback) {
        params = params || {};
        params.security_ls_key = LIVESTREET_SECURITY_KEY;

        $.each(params, function(k, v) {
            if (typeof(v) == "boolean") {
                params[k] = v ? 1 : 0;
            }
        })

        if (url.indexOf('http://') != 0 && url.indexOf('https://') != 0) {
            url = aRouter['ajax'] + url + '/';
        }

        $.ajax({
            type: "POST",
            url: url,
            data: params,
            dataType: 'json',
            success: callback
        });
    }

    this.albumShowNext = function(album_target_id, start, limit, pictureSize) {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxalbumshownextpictures/';
        var postData = { album_target_id: album_target_id,
            start : start,
            limit: limit};

        picalbums.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                if (result.aResult) {
                    $('#pictures_for_ajax_upload_' + album_target_id).append(result.aResult);
                }

                if (pictureSize > (start + limit)) {
                    if ((pictureSize - ((start + limit))) > limit) {
                        $('#show_next_link_' + album_target_id).empty().append(
                            '<a class="friends-last-foto-get-more" id="" href="" onclick="picalbums.albumShowNext(' + album_target_id + ', ' + (start + limit) + ', ' + limit + ', ' + pictureSize + '); return false;">' + ls.lang.get('picalbums_show_friendpage_yet') + limit + ls.lang.get('picalbums_show_friendpage_yet_middle') + pictureSize + ls.lang.get('picalbums_show_friendpage_yet_end') + '</a>');
                    } else {
                        $('#show_next_link_' + album_target_id).empty().append(
                            '<a class="friends-last-foto-get-more" id="" href="" onclick="picalbums.albumShowNext(' + album_target_id + ', ' + (start + limit) + ', ' + limit + ', ' + pictureSize + '); return false;">' + ls.lang.get('picalbums_show_friendpage_all') + pictureSize + ls.lang.get('picalbums_show_friendpage_all_end') + '</a>');
                    }
                } else {
                    $('#show_next_link_' + album_target_id).fadeOut("slow", function() {
                        $(this).remove();
                    });
                }
            }
        }.bind(this));
    }

    // Добавление альбома
    this.appendAlbum = function(formObj, redirect) {
        // Получаем обьект формы
        formObj = $('#' + formObj);

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxappendalbum/';
        var postData = { album_title_text: formObj.find('[name="album_title_text"]').val(),
                         album_description_text: formObj.find('[name="album_description_text"]').val(),
                         album_need_moder: formObj.find('[name="album_need_moder"]').val(),
                         album_visibility: formObj.find('[name="album_visibility"]').val(),
                         album_add_user_target_id: formObj.find('[name="album_add_user_target_id"]').val(),
                         album_category_id: formObj.find('[name="category_id"]').val(),
                         album_tags: formObj.find('[name="album_tag_text"]').val()};

        this.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                setTimeout(function() {
                    window.location.href = redirect + result.albumulr + '/';
                }, 400);
            }
        }.bind(this));
    }

    this.appendAlbumForCopy = function(formObj, redirect) {
        // Получаем обьект формы
        formObj = $('#' + formObj);

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxappendalbum/';
        var postData = { album_title_text: formObj.find('[name="album_title_text"]').val(),
                         album_description_text: formObj.find('[name="album_description_text"]').val(),
                         album_need_moder: formObj.find('[name="album_need_moder"]').val(),
                         album_visibility: formObj.find('[name="album_visibility"]').val(),
                         album_add_user_target_id: formObj.find('[name="album_add_user_target_id"]').val(),
                         album_category_id: formObj.find('[name="category_id"]').val(),
                         album_tags: formObj.find('[name="album_tag_text"]').val(),
                         show_user_albums: true};

        this.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                $('#copy-picture-container').html(result.sUserAlbumsDialog);

                this.ajax(aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxcopypicture/',
                          { copy_to_album_id : result.iAlbumId, copy_picture_id : $('.showedpicture').attr('id')},
                    function(result) {
                    if (!result) {
                        $.notifier.error('Error', 'Please try again later');
                        return;
                    }
                    if (result.bStateError) {
                        $.notifier.error(null, result.sMsg);
                    } else {
                        if (result.sMsg)
                            $.notifier.notice(null, result.sMsg);

                        $("#createalbum-dialog").dialog('close');
                    }
                }.bind(this));
            }
        }.bind(this));
    }

    // Удаление альбома
    this.removeAlbum = function(AlbumId) {
        if (confirm(ls.lang.get('picalbums_confirm_delete_album'))) {
            var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxremovealbum/';
            var postData = { album_target_id: AlbumId};

            $(".photo-albums .album-info .album-loader[id='album-loader_" + AlbumId + "']").css('display', 'block');

            this.ajax(posturl, postData, function(result) {
                // Если не было получено результат
                if (!result) {
                    $.notifier.error('Error', 'Please try again later');
                    return;
                }
                // Результат получен, но с ошибкой
                if (result.bStateError) {
                    $.notifier.error(null, result.sMsg);
                } else {
                    // Выводим пришедшее сообщение
                    if (result.sMsg)
                        $.notifier.notice(null, result.sMsg);

                    $('#album_' + AlbumId).fadeOut("slow", function() {
                        $(this).remove();
                    });
                }
                $(".photo-albums .album-info .album-loader[id='album-loader_" + AlbumId + "']").css('display', 'none');
            }.bind(this));

        }
    }

    this.editAlbum = function(formObj, albumId, redirect) {
        var formObj = $('#' + formObj);

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxeditalbum/';
        var postData = { album_target_id: albumId,
                         album_visivility: formObj.find('[name="album_visibility"]').val(),
                         album_description: formObj.find('[name="album_description_text"]').val(),
                         album_title: formObj.find('[name="album_title_text"]').val(),
                         album_category_id: formObj.find('[name="category_id"]').val(),
                         album_need_moder: formObj.find('[name="album_need_moder"]').val(),
                         album_tags: formObj.find('[name="album_tag_text"]').val()};

        this.ajax(posturl, postData, function(result) {
            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                setTimeout(function() {
                    window.location.href = redirect + result.albumulr;
                }, 400);
            }
        }.bind(this));

    }

    this.editPictures = function(albumId, redirect) {
        var arrObj = {};
        $('.picture_description_text').each(function(indx, element) {
            arrObj[$(element).attr('id')] = $(element).val();
        });
        $('.album_cover').each(function(indx, element) {
            if ($(element).is(':checked'))
                arrObj[$(element).attr('id')] = 'checked';
        });

        $('.picture_delete').each(function(indx, element) {
            if ($(element).is(':checked'))
                arrObj[$(element).attr('id')] = 'checked';
        });

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxeditpictures/';
        var postData = { pictures_array: arrObj,
            album_id: albumId
        };

        this.ajax(posturl, postData, function(result) {
            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                setTimeout(function() {
                    window.location.href = redirect;
                }, 400);
            }
        }.bind(this));

    }

    // Загрузка комментариев
    this.showComments = function(pictureId) {
        $('.picture_comment').slideUp('slow', function() {
            $(this).remove();
        });

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxgetcommentforpicture/';
        var postData = { picture_target_id: pictureId };

        this.ajax(posturl, postData, function(result) {
            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                if (result.aResult) {
                    $('.piccomments').append(result.aResult);
                }
            }
        }.bind(this));
    }

    this.showCommentsWithClear = function(pictureId) {
        if ($('.piccomments .picture_comment').length > 0) {
            $('.piccomments .picture_comment').slideUp("slow", function() {
                $(this).remove();
            });
        } else {
            this.showComments(pictureId);
        }
    }

    // Удаление комментария
    this.removeComment = function(commentId) {
        if (confirm(ls.lang.get('picalbums_confirm_delete_comment'))) {
            var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxremovecomment/';
            var postData = { comment_target_id: commentId};

            this.ajax(posturl, postData, function(result) {

                // Если не было получено результат
                if (!result) {
                    $.notifier.error('Error', 'Please try again later');
                    return;
                }
                // Результат получен, но с ошибкой
                if (result.bStateError) {
                    $.notifier.error(null, result.sMsg);
                } else {
                    // Выводим пришедшее сообщение
                    if (result.sMsg)
                        $.notifier.notice(null, result.sMsg);

                    $('#comment_record_' + commentId).fadeOut("slow", function() {
                        $(this).remove();
                    });
                }
            }.bind(this));
        }
    }

    // Добавление комментария
    this.AppendComment = function(formObj, pictureId) {
        // Получаем обьект формы
        formObj = $('#' + formObj);

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxappendcomment/';
        var postData = { comment_text: formObj.find('[name="comment_text"]').val(),
            picture_target_id: pictureId};

        this.ajax(posturl, postData, function(result) {
            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                if (result.aResult) {
                    $('.piccomments').append(result.aResult);
                }
                this.toggleFromAppendCommentUP();
                $('#form_comment_text').val('');
            }
        }.bind(this));
    }

    this.heartComment = function(PictureId) {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxheartpicture/';
        var postData = { picture_target_id: PictureId};

        this.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                if (result.heartStatus == false)
                    $('#heart_like').removeClass('like-active').addClass('like');
                else
                    $('#heart_like').removeClass('like').addClass('like-active');

                $('#picture-heart-preview').empty().html(result.sHeartText);

                if (result.iHeartCount) {
                    $('#heartcnt').empty().text(result.iHeartCount);
                }
                else {
                    $('#heartcnt').empty().text('0');
                }

                $('#heart_like').poshytip('update');
            }
        }.bind(this));
    }

    this.deleteFromBlacklist = function(userId) {

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxremovefromblacklist/';
        var postData = { user_id: userId};

        this.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                $('#blacklict_user_' + userId).remove();
            }
        }.bind(this));
    }

    this.addToBlackList = function(blackuserinput) {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxappendtoblacklist/';
        user_login_value = $('#' + blackuserinput).val();
        var postData = { user_login: user_login_value };

        this.ajax(posturl, postData, function(result) {
            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                $('#blacklict_useslist').append('<div id="blacklict_user_' + result.userId + '"><a class="user"  href="' + result.userProfilePath + '">' + user_login_value + '</a> - <a href="" onclick="picalbums.deleteFromBlacklist(' + result.userId + '); return false">' + ls.lang.get('picalbums_delete_text') + '</a></div>');
            }
        }.bind(this));
    }

    this.showDialogWithHeartedUsers = function(PictureId) {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxallheartusers/';
        var postData = { picture_target_id: PictureId};
        this.ajax(posturl, postData, function(result) {
            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                if (result.textHearted) {
                    $('#heart-dialog-content').empty().html(result.textHearted);
                    $("#heart-dialog").dialog({
                        position: ["center","center"],
                        dialogClass: 'heart-dialog-class',
                        draggable: true,
                        resizable: true,
                        height: 200,
                        width: 400,
                        hide: 'slide'
                    });
                }
            }
        }.bind(this));
    }

    this.showDialogWithExif = function() {
        $("#exif-dialog").dialog({
            position: ["center","center"],
            dialogClass: 'exif-dialog-class',
            draggable: true,
            resizable: true,
            height: 400,
            width: 400,
            hide: 'slide'
        });
    }

    this.showDialogForCopyPicture = function() {
        $("#currentuserpictures-dialog").dialog({
            position: ["center","center"],
            dialogClass: 'currentuserpictures-dialog-class',
            draggable: true,
            resizable: true,
            height: 164,
            width: 340,
            hide: 'slide'
        });
    }

    this.showDialogForCreateAlbum = function() {
        $("#createalbum-dialog").dialog({
            position: ["center","center"],
            dialogClass: 'createalbum-dialog-class',
            draggable: true,
            resizable: true,
            height: 490,
            width: 790,
            hide: 'slide'
        });
    }

    this.copyPicture = function() {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxcopypicture/';
        var postData = { copy_to_album_id : $('#current_user_albums').val(), copy_picture_id : $('.showedpicture').attr('id')};
        this.ajax(posturl, postData, function(result) {
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                $("#currentuserpictures-dialog").dialog('close');
            }
        }.bind(this));
    }

    this.markConfirm = function(PictureId, ConfirmStatus) {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxmarkconfirm/';
        var href = window.location.href;
        var talkid = '';
        if (href.indexOf('talk/read/') > 0) {
            href = href.substring(href.indexOf('talk/read/') + 10);
            href = href.substring(0, href.indexOf('/'));
            talkid = href;
        }

        var postData = { picture_target_id: PictureId, confirm_status : ConfirmStatus, talkid : talkid};
        this.ajax(posturl, postData, function(result) {
            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                $('.reload-note-link').click();
                $('#nonmark_confirm').fadeOut("slow", function() {
                    $(this).empty();
                });
            }

            if (talkid != '') {
                setTimeout(function() {
                    window.location.href = window.location.href.substring(0, window.location.href.indexOf('read/'));
                }, 400);
            }
        }.bind(this));
    }

    this.friendAlbumSlideUp = function(album_id) {
        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxsavefriendpagehistory/';
        var postData = { album_target_id: album_id };

        this.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                $('#friends-last-foto_' + album_id).slideUp('normal', function() {
                    $(this).remove();
                });
                cnt = $('#span-friend-page-album-cnt').text();
                cnt = parseInt(cnt);
                cnt = cnt - 1;
                $('#span-friend-page-album-cnt').empty().append(cnt);
            }
        }.bind(this));
    }

    this.removeCategory = function(categoryId) {
        if (confirm(ls.lang.get('picalbums_ready_delete_category'))) {

            var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxremovecategory/';
            var postData = { category_target_id: categoryId};

            this.ajax(posturl, postData, function(result) {
                // Если не было получено результат
                if (!result) {
                    $.notifier.error('Error', 'Please try again later');
                    return;
                }
                // Результат получен, но с ошибкой
                if (result.bStateError) {
                    $.notifier.error(null, result.sMsg);
                } else {
                    // Выводим пришедшее сообщение
                    if (result.sMsg)
                        $.notifier.notice(null, result.sMsg);

                    $('#category_setting' + categoryId).fadeOut("slow", function() {
                        $(this).remove();
                    });
                }
            }.bind(this));

        }
    }

    this.editCategory = function(formObj, categoryId, redirect) {
        var formObj = $('#' + formObj);

        var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxeditcategory/';
        var postData = { category_target_id: categoryId,
            category_title: formObj.find('[name="category_title_text"]').val()
        };

        this.ajax(posturl, postData, function(result) {

            // Если не было получено результат
            if (!result) {
                $.notifier.error('Error', 'Please try again later');
                return;
            }
            // Результат получен, но с ошибкой
            if (result.bStateError) {
                $.notifier.error(null, result.sMsg);
            } else {
                // Выводим пришедшее сообщение
                if (result.sMsg)
                    $.notifier.notice(null, result.sMsg);

                setTimeout(function() {
                    window.location.href = redirect
                }, 400);
            }
        }.bind(this));

    }

    // Модерирование альбома
    this.moderateAlbum = function(AlbumId) {
        if (confirm(ls.lang.get('picalbums_confirm_moderate_album'))) {
            var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxmoderatealbum/';
            var postData = { album_target_id: AlbumId};

            this.ajax(posturl, postData, function(result) {
                // Если не было получено результат
                if (!result) {
                    $.notifier.error('Error', 'Please try again later');
                    return;
                }
                // Результат получен, но с ошибкой
                if (result.bStateError) {
                    $.notifier.error(null, result.sMsg);
                } else {
                    // Выводим пришедшее сообщение
                    if (result.sMsg)
                        $.notifier.notice(null, result.sMsg);

                    setTimeout(function() {
                        window.location.reload();
                    }, 400);
                }
            }.bind(this));

        }
    }

    this.moderatePicture = function(PictureId) {
        if (confirm(ls.lang.get('picalbums_confirm_moderate_image'))) {
            var posturl = aRouter[picalbumsConfig["picalbums_router_name"]] + 'ajaxmoderatepicture/';
            var postData = { picture_target_id: PictureId};

            this.ajax(posturl, postData, function(result) {
                // Если не было получено результат
                if (!result) {
                    $.notifier.error('Error', 'Please try again later');
                    return;
                }
                // Результат получен, но с ошибкой
                if (result.bStateError) {
                    $.notifier.error(null, result.sMsg);
                } else {
                    // Выводим пришедшее сообщение
                    if (result.sMsg)
                        $.notifier.notice(null, result.sMsg);

                    setTimeout(function() {
                        window.location.reload();
                    }, 400);
                }
            }.bind(this));

        }
    }

    this.toggleFromAppendComment = function() {
        $('.picalbums_comment_reply').slideToggle(0);
    }

    this.toggleFromAppendCommentUP = function() {
        $('.picalbums_comment_reply').slideUp(0);
    }

    this.toggleFormAppendPicture = function() {
        $('.picture_form_add').slideToggle(0);
    }

    this.toggleFormAppendPictureUP = function() {
        $('.picture_form_add').slideUp(0);
    }

    return this;
}).call({});


