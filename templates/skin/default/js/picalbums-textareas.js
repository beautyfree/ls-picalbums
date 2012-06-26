$(document).ready(function () {
    $(".picalbums_comment-counter").append(ls.lang.get('picalbums_text_characters_start') + "<strong>" + (picalbumsConfig["text_form_max_characters"] - $('#album_description_text').val().length) + "</strong>" + ls.lang.get('picalbums_text_characters_end'));

    $('#album_description_text').bind('textchange', function () {
        if ($(this).val().length > picalbumsConfig["text_form_max_characters"]) $(this).val($(this).val().substr(0, picalbumsConfig["text_form_max_characters"]));
        var remaining = picalbumsConfig["text_form_max_characters"] - $(this).val().length;
        $(".picalbums_comment-counter").html(ls.lang.get('picalbums_text_characters_start') + "<strong>" + remaining + "</strong>" + ls.lang.get('picalbums_text_characters_end'));
        if (remaining <= 10) $(".picalbums_comment-counter").css("color", "red");
        else $(".picalbums_comment-counter").css("color", "black");
    });

    $('#album_description_text').keyup(function (e) {
        if (e.ctrlKey && e.keyCode == 13) $('#submit_album').click();
    });

    ls.autocomplete.add($("#album_tag_text"), aRouter[picalbumsConfig["picalbums_router_name"]]+'ajaxtagautocompleter/', true);
});