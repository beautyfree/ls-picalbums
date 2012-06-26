$.fn.jQueryNotes = function (settings) {
    settings = $.extend({
        minWidth: 50,
        minHeight: 50,
        allowAdd: true,
        allowEdit: true,
        allowDelete: true,
        allowLink: true,
        allowAuthor: true,
        hideNotes: false,
        loadNotes: true,
        helper: "",
        maxNotes: null,
        operator: "jquery-notes_php/notes.php"
    }, settings);
    var ID = {};
    var _getProperties = function (pointer) {
            var image = $("#jquery-notes_" + pointer + " img");
            ID.timeout;
            ID.firstLoad = true;
            ID.add = false;
            ID.edit = false;
            ID.set = false;
            ID.move = false;
            ID.image = image.attr("src");
            ID.width = image.width();
            ID.height = image.height()
        };
    var _initPlugin = function (matchedObj) {
            pointer = 1;
            ID = {};
            _setContainer(matchedObj, pointer);
            _getProperties(pointer);
            _createHtml(pointer);
            _getNotes(pointer)
        };
    var _setContainer = function (matchedObj, pointer) {
            var cssClass = $(matchedObj).attr("class");
            var style = $(matchedObj).attr("style");
            $(matchedObj).wrap('<div id="jquery-notes_' + pointer + '" class="jquery-notes-container clearfix" />').removeAttr("class", "style").css({
                padding: 0,
                margin: 0,
                border: "none",
                outline: "none",
                background: "none"
            });
            $("#jquery-notes_" + pointer).addClass(cssClass).addClass(settings.helper).attr({
                style: style
            })
        };
    var _createHtml = function (pointer) {
            $("#jquery-notes_" + pointer + " img").wrap('<div class="notes clearfix" />');
            $("#jquery-notes_" + pointer + " .notes").append('<div class="layer"></div><div class="loading"><div class="message"></div></div>');
            $("#jquery-notes_" + pointer + " .notes .loading").css({
                marginTop: ID.height / 2 - 13 + "px"
            });
            $("#jquery-notes_" + pointer + " .notes").mousedown(function (event) {
                _addNote(pointer, event)
            }).mouseup(function (event) {
                _setNote(pointer, event)
            });
            $(".makenote").empty().append('<a href="javascript:void(0);" class="add-note-link" title="add" >' + ls.lang.get('picalbums_make_note') + "</a>");
            $(".makenote").append('<a href="javascript:void(0);" class="reload-note-link" style="display: none;" title="add" >!</a>');
            $(".add-note-link").click(function () {
                _startAdd(pointer)
            });
            $(".reload-note-link").click(function () {
                _reload(pointer)
            })
        };
    var _countNotes = function (pointer) {
            return $("#jquery-notes_" + pointer + " .notes").children(".note").length
        };
    var _startLoading = function (pointer, message) {
            $("#jquery-notes_" + pointer + " .notes .layer").fadeIn("middle");
            $("#jquery-notes_" + pointer + " .notes .loading").fadeIn("middle");
            $("#jquery-notes_" + pointer + " .notes .loading .message").text(message)
        };
    var _stopLoading = function (pointer) {
            ID.timeout = undefined;
            ID.timeout != undefined ? clearTimeout(ID.timeout) : "";
            ID.timeout = setTimeout(function () {
                ID.timeout = undefined;
                $("#jquery-notes_" + pointer + " .notes .layer").fadeOut("middle");
                $("#jquery-notes_" + pointer + " .notes .loading").fadeOut("middle");
                $("#jquery-notes_" + pointer + " .notes .loading .message").text("")
            }, 1E3)
        };
    var _getNotes = function (pointer) {
            if (settings.loadNotes) $.ajax({
                url: settings.operator,
                global: false,
                timeout: 15E3,
                dataType: "json",
                type: "POST",
                data: "get=true&image=" + ID.image + "&security_ls_key=" + LIVESTREET_SECURITY_KEY + "&pictureid=" + settings.pictureid,
                success: function (data) {
                    if (data.result) {
                        firstLoad = false;
                        $.each(data.result, function () {
                            _printNote(pointer, this)
                        });
                        var counter = _countNotes(pointer);
                        $("#jquery-notes_" + pointer + " .controller .counter").attr("title", function () {
                            return counter == 1 ? counter + " note" : counter + " notes"
                        });
                        counter >= settings.maxNotes && settings.maxNotes != null ? $(".add-note-link").hide() : $(".add-note-link").show();
                        settings.hideNotes ? _hideNotes(pointer) : ""
                    }
                    _stopLoading(pointer)
                }
            });
            else _stopLoading(pointer)
        };
    var _printNote = function (pointer, note) {
            var html = '<div id="n_' + pointer + "-" + note.ID + '" class="note"><div id="n_border_' + pointer + "-" + note.ID + '" class="border"><div class="bg">';
            html += note.LINK != "" && settings.allowLink ? '<a href="' + note.LINK + '"></a>' : "";
            html += '</div></div></div><div style="text-align: left" id="t_' + pointer + "-" + note.ID + '" class="text"><span class="txt">';
            html += note.NOTE != "" ? note.NOTE : note.LINK;
            html += "</span>";
            if (note.NOTE) html += note.AUTHOR != "" && settings.allowAuthor ? '<span style="text-align: right" class="author" usermark="' + note.AUTHORMARK + '" isconfirm="' + note.ISCONFIRM + '" canedit="' + note.CANEDIT + '" ' + '" candelete="' + note.CANDELETE + '" > - ' + note.AUTHOR + "</span>" : "";
            else html += note.AUTHOR != "" && settings.allowAuthor ? '<span style="text-align: right" class="author" usermark="' + note.AUTHORMARK + '" isconfirm="' + note.ISCONFIRM + '" canedit="' + note.CANEDIT + '" ' + '" candelete="' + note.CANDELETE + '" >' + note.AUTHOR + "</span>" : "";
            html += "</div>";
            $("#jquery-notes_" + pointer + " .notes").append(html);
            if (note.ISCONFIRM == 0 || note.ISCONFIRM == "0") $("#n_border_" + pointer + "-" + note.ID).attr("style", "border: 1px solid green;");
            if (settings.allowDelete || settings.allowEdit) $("#jquery-notes_" + pointer + " .notes #n_" + pointer + "-" + note.ID).mousedown(function (e) {
                $(this).mouseup(function () {
                    $(this).unbind("mouseup");
                    e.button == 2 ? _openSettings(pointer, note.ID) : null
                })
            })[0].oncontextmenu = function () {
                return false
            };
            var position = {
                "left": _percentToPixel(pointer, note.LEFT, "left"),
                "top": _percentToPixel(pointer, note.TOP, "top"),
                "width": _percentToPixel(pointer, note.WIDTH, "width"),
                "height": _percentToPixel(pointer, note.HEIGHT, "height")
            };
            $("#jquery-notes_" + pointer + " .notes #n_" + pointer + "-" + note.ID).css({
                left: position.left + "px",
                top: position.top + "px",
                width: position.width + "px",
                height: position.height + "px"
            }).hover(function () {
                _focusOnNote(pointer, note.ID)
            }, function () {
                _focusOffNote(pointer, note.ID)
            });
            $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + note.ID).css({
                left: position.left + "px",
                top: parseFloat(position.top) + parseFloat(position.height) + "px"
            });
            $("#jquery-notes_" + pointer + " .notes #n_" + pointer + "-" + note.ID + " a").click(function (evt) {
                document.location = note.LINK
            })
        };
    var _focusOnNote = function (pointer, id) {
            if (!ID.add && !ID.edit) {
                $("#jquery-notes_" + pointer + " .notes .note").addClass("offFocus");
                if (id != undefined) {
                    $("#jquery-notes_" + pointer + " .notes #n_" + pointer + "-" + id).removeClass("offFocus").addClass("onFocus");
                    $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + id).show().index(9990)
                }
            }
        };
    var _focusOffNote = function (pointer, id) {
            if (!ID.add && !ID.edit) {
                $("#jquery-notes_" + pointer + " .notes .note").removeClass("offFocus");
                if (id != undefined) {
                    $("#jquery-notes_" + pointer + " .notes #n_" + pointer + "-" + id).removeClass("onFocus");
                    $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + id).hide().index("auto")
                }
            }
        };
    var _addNote = function (pointer, event) {
            if (ID.add && !ID.set) {

                ID.set = true;
                ID.move = true;
                var position = {};
                position.left = event.pageX - $("#loaded_image").offset().left;
                position.top = event.pageY - $("#loaded_image").offset().top;
                $("#jquery-notes_" + pointer + " .notes").append('<div class="note select"><div class="border"><div class="bg"></div></div></div>');
                position.maxLeft = ID.width - settings.minWidth;
                position.maxTop = ID.height - settings.minHeight;
                if (position.left > position.maxLeft) position.left = position.maxLeft;
                if (position.top > position.maxTop) position.top = position.maxTop;

                $("#jquery-notes_" + pointer + " .notes .select").css({
                    width: settings.minWidth,
                    height: settings.minHeight,
                    left: position.left,
                    top: position.top,
                    cursor: "move"
                }).draggable({
                    containment: $("#loaded_image"),
                    cursor: "move"
                }).resizable({
                    containment: "parent",
                    minWidth: settings.minWidth,
                    minHeight: settings.minHeight,
                    aspectRatio: false,
                    handles: "ne, se, sw, nw"
                })
            }
        };
    var _setNote = function (pointer, event) {
            if (ID.add && ID.set && ID.move) {
                ID.move = false;
                var html = '<div class="text-box">';
                html += settings.allowAuthor ? '<input id="noteauthor" type="text" name="author" value="" /><br />' : "";
                html += '<textarea name="note"></textarea><br />';
                html += '<a href="javascript:void(0);" class="save-note" title="save note"></a><a href="javascript:void(0);" class="cancel-note" title="cancel"></a></div>';
                $("#jquery-notes_" + pointer + " .notes .select").append(html);
                $("#noteauthor").autocomplete({
                    open: function () {
                        $(".text-box").parent().css("display", "block")
                    },
                    source: function (request, response) {
                        $.ajax({
                            url: aRouter[picalbumsConfig["picalbums_router_name"]] + "ajaxuserautocomplete/",
                            dataType: "json",
                            type: "POST",
                            data: {
                                security_ls_key: LIVESTREET_SECURITY_KEY,
                                name_startsWith: request.term
                            },
                            success: function (data) {
                                response(data.aResult)
                            }
                        })
                    },
                    minLength: 2
                });
                _inputFocus(pointer);
                $("#jquery-notes_" + pointer + " .notes .select .text-box .save-note").click(function () {
                    _saveNote(pointer, null, "add")
                });
                $("#jquery-notes_" + pointer + " .notes .select .text-box .cancel-note").click(function () {
                    $(".cancel-note-link").removeClass("cancel-note-link").text(ls.lang.get('picalbums_make_note'));
                    _abort(pointer);
                    if (picalbumsConfig["pjax_for_picture_listing"] == false) {
                        $("#picture_link_navigation").unbind("click");
                        $("#picture_link_navigation").click(function (evt) {
                            document.location = $(this).attr("href")
                        })
                    } else $(".navigation_next_picture_link_pjax").pjax("#picture_listing_pjax", {
                        timeout: 5E3
                    });
                    $("#picture_link_navigation").css("cursor", "pointer");
                    return false
                })
            }
        };
    var _inputFocus = function (pointer) {
            $("#jquery-notes_" + pointer + ' .notes .select input[name="link"]').focusout(function () {
                $(this).val() == "" ? $(this).val("http://") : null
            })
        };
    var _saveNote = function (pointer, id, operation) {
            var note = $("#jquery-notes_" + pointer + " .notes .select .text-box textarea").val();
            var link = $("#jquery-notes_" + pointer + ' .notes .select .text-box input[name="link"]').val();
            var author = $("#jquery-notes_" + pointer + ' .notes .select .text-box input[name="author"]').val();
            link = link == undefined ? "" : link;
            author = author == undefined ? "" : author;
            var position = _getNotePosition(pointer);
            $.ajax({
                url: settings.operator,
                global: false,
                timeout: 15E3,
                dataType: "json",
                type: "POST",
                beforeSend: function () {
                    id == undefined ? _startLoading(pointer, ls.lang.get('picalbums_saving_note')) : _startLoading(pointer, ls.lang.get('picalbums_editing_note'))
                },
                data: operation + "=true&image=" + ID.image + "&id=" + id + "&position=" + position.left + "," + position.top + "," + position.width + "," + position.height + "&note=" + note + "&link=" + link + "&author=" + author + "&security_ls_key=" + LIVESTREET_SECURITY_KEY + "&pictureid=" + settings.pictureid,
                success: function (data) {
                    if (data.result == "true") {
                        $(".cancel-note-link").removeClass("cancel-note-link").text(ls.lang.get('picalbums_make_note'));
                        _reload(pointer);
                        if (picalbumsConfig["pjax_for_picture_listing"] == false) {
                            $("#picture_link_navigation").unbind("click");
                            $("#picture_link_navigation").click(function (evt) {
                                document.location = $(this).attr("href")
                            })
                        } else $(".navigation_next_picture_link_pjax").pjax("#picture_listing_pjax", {
                            timeout: 5E3
                        });
                        $("#picture_link_navigation").css("cursor", "pointer")
                    } else {
                        _stopLoading(pointer);
                        $.notifier.error(null, data.result)
                    }
                }
            })
        };
    var _deleteNote = function (pointer, id) {
            if (settings.allowDelete) $.ajax({
                url: settings.operator,
                global: false,
                timeout: 15E3,
                dataType: "json",
                type: "POST",
                beforeSend: function () {
                    _startLoading(pointer, ls.lang.get('picalbums_deleting_note'))
                },
                data: "delete=true&image=" + ID.image + "&id=" + id + "&security_ls_key=" + LIVESTREET_SECURITY_KEY + "&pictureid=" + settings.pictureid,
                success: function (data) {
                    _stopLoading(pointer);
                    data.result == "true" ? _reload(pointer) : $.notifier.error(null, data.result)
                }
            })
        };
    var _startAdd = function (pointer) {
            if (settings.allowAdd && (_countNotes(pointer) < settings.maxNotes || settings.maxNotes == null)) if (ID.add) {
                $(".cancel-note-link").removeClass("cancel-note-link").text(ls.lang.get('picalbums_make_note'));
                _abort(pointer);
                if (picalbumsConfig["pjax_for_picture_listing"] == false) {
                    $("#picture_link_navigation").unbind("click");
                    $("#picture_link_navigation").click(function (evt) {
                        document.location = $(this).attr("href")
                    })
                } else $(".navigation_next_picture_link_pjax").pjax("#picture_listing_pjax", {
                    timeout: 5E3
                });
                $("#picture_link_navigation").css("cursor", "pointer")
            } else if (!ID.add && !ID.edit) {
                _abort(pointer);
                _focusOnNote(pointer);
                ID.add = true;
                $("#picture_link_navigation").unbind("click");
                $("#picture_link_navigation").click(function (evt) {
                    evt.preventDefault()
                });
                $("#picture_link_navigation").css("cursor", "auto");
                $(".navigation_next_picture_link_pjax").die("click");
                $(".add-note-link").addClass("cancel-note-link").text(ls.lang.get('picalbums_do_make_note'));
                $.notifier.notice(null, ls.lang.get('picalbums_click_into_picture_for_make_note'))
            }
        };
    var _openSettings = function (pointer, id) {
            if ((settings.allowEdit || settings.allowDelete) && !ID.edit && !ID.add) {
                ID.edit = true;
                $("#jquery-notes_" + pointer + " .notes #n_" + pointer + "-" + id).addClass("select");
                var note = $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + id + " .txt").text();
                var author = $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + id + " .author").attr("usermark");
                var canedit = $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + id + " .author").attr("canedit");
                var candelete = $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + id + " .author").attr("candelete");
                $("#jquery-notes_" + pointer + " .notes #t_" + pointer + "-" + id).remove();
                $("#jquery-notes_" + pointer + " .notes #n_" + pointer + "-" + id + " .border .bg a").remove();
                var html = "";
                html += '<div class="text-box">';
                if (settings.allowEdit) {
                    html += settings.allowAuthor ? '<input id="noteauthor" type="text" name="author" value="' + author + '" /><br />' : "";
                    html += '<textarea name="note">' + note + "</textarea><br />";
                    if (canedit == 1 || canedit == "1") html += '<a href="javascript:void(0);" class="edit-note" title="edit" />'
                }
                html += '<a href="javascript:void(0);" class="cancel-note" title="cancel" />';
                if (candelete == 1 || candelete == "1") html += settings.allowDelete ? '<a href="javascript:void(0);" class="delete-note" title="delete" />' : "";
                html += "</div>";
                $("#jquery-notes_" + pointer + " .notes .select").append(html);
                $("#noteauthor").autocomplete({
                    open: function () {
                        $(".text-box").parent().css("display", "block")
                    },
                    source: function (request, response) {
                        $.ajax({
                            url: aRouter[picalbumsConfig["picalbums_router_name"]] + "ajaxuserautocomplete/",
                            dataType: "json",
                            type: "POST",
                            data: {
                                security_ls_key: LIVESTREET_SECURITY_KEY,
                                name_startsWith: request.term
                            },
                            success: function (data) {
                                response(data.aResult)
                            }
                        })
                    },
                    minLength: 2
                });
                _inputFocus(pointer);
                $("#jquery-notes_" + pointer + " .notes .select").draggable({
                    containment: "parent",
                    cursor: "move"
                }).resizable({
                    containment: "parent",
                    minWidth: settings.minWidth,
                    minHeight: settings.minHeight,
                    aspectRatio: false,
                    handles: "ne, se, sw, nw"
                });
                $("#jquery-notes_" + pointer + " .notes .select .text-box .edit-note").click(function () {
                    _saveNote(pointer, id, "edit")
                });
                $("#jquery-notes_" + pointer + " .notes .select .text-box .cancel-note").click(function () {
                    _reload(pointer);
                    if (picalbumsConfig["pjax_for_picture_listing"] == false) {
                        $("#picture_link_navigation").unbind("click");
                        $("#picture_link_navigation").click(function (evt) {
                            document.location = $(this).attr("href")
                        })
                    } else $(".navigation_next_picture_link_pjax").pjax("#picture_listing_pjax", {
                        timeout: 5E3
                    });
                    $("#picture_link_navigation").css("cursor", "pointer");
                    return false
                });
                $("#jquery-notes_" + pointer + " .notes .select .text-box .delete-note").click(function () {
                    _deleteNote(pointer, id);
                    if (picalbumsConfig["pjax_for_picture_listing"] == false) {
                        $("#picture_link_navigation").unbind("click");
                        $("#picture_link_navigation").click(function (evt) {
                            document.location = $(this).attr("href")
                        })
                    } else $(".navigation_next_picture_link_pjax").pjax("#picture_listing_pjax", {
                        timeout: 5E3
                    });
                    $("#picture_link_navigation").css("cursor", "pointer");
                    return false
                });
                $("#picture_link_navigation").unbind("click");
                $("#picture_link_navigation").click(function (evt) {
                    evt.preventDefault()
                });
                $("#picture_link_navigation").css("cursor", "auto")
            }
        };
    var _hideNotes = function (pointer) {
            if (!ID.add && !ID.edit) if ($("#jquery-notes_" + pointer + " .controller .hide-notes").hasClass("show-notes")) {
                $("#jquery-notes_" + pointer + " .notes .note").css({
                    visibility: "visible"
                });
                $("#jquery-notes_" + pointer + " .controller .hide-notes").removeClass("show-notes").attr({
                    title: "hide notes"
                })
            } else {
                $("#jquery-notes_" + pointer + " .notes .note").css({
                    visibility: "hidden"
                });
                $("#jquery-notes_" + pointer + " .controller .hide-notes").addClass("show-notes").attr({
                    title: "show notes"
                })
            }
        };
    var _reload = function (pointer) {
            $("#jquery-notes_" + pointer + " .notes .note").remove();
            $("#jquery-notes_" + pointer + " .notes .text").remove();
            _getProperties(pointer);
            _getNotes(pointer)
        };
    var _abort = function (pointer) {
            $("#jquery-notes_" + pointer + " .notes .select").remove();
            _getProperties(pointer);
            _focusOffNote(pointer)
        };
    var _getNotePosition = function (pointer) {
            return {
                "left": _pixelToPercent(pointer, $("#jquery-notes_" + pointer + " .notes .select").css("left"), "left"),
                "top": _pixelToPercent(pointer, $("#jquery-notes_" + pointer + " .notes .select").css("top"), "top"),
                "width": _pixelToPercent(pointer, $("#jquery-notes_" + pointer + " .notes .select").css("width"), "width"),
                "height": _pixelToPercent(pointer, $("#jquery-notes_" + pointer + " .notes .select").css("height"), "height")
            }
        };
    var _pixelToPercent = function (pointer, pixel, type) {
            pixel = parseInt(pixel.toString().replace("px", ""));
            switch (type) {
            case "left":
            case "width":
                var percent = 100 / ID.width * pixel;
                break;
            case "top":
            case "height":
                var percent = 100 / ID.height * pixel;
                break
            }
            return percent
        };
    var _percentToPixel = function (pointer, percent, type) {
            percent = parseFloat(percent.toString().replace("%", ""));
            switch (type) {
            case "left":
            case "width":
                var pixel = percent / 100 * ID.width;
                break;
            case "top":
            case "height":
                var pixel = percent / 100 * ID.height;
                break
            }
            return pixel
        };
    $(this).each(function () {
        _initPlugin(this)
    })
};