<script type="text/javascript">
    function picturePrepare() {
        $('#form_comment_text').markItUp(getMarkitupCommentSettings());

        $('.original-imagegal').prettyPhoto({
               social_tools:'',
               show_title: false,
               slideshow:false,
               deeplinking: false,
               overlay_gallery: false,
        });

        $('#heart_like').poshytip({
             className: 'tip-yellow',
             alignTo: 'cursor',
             alignX: 'inner-left',
             alignY: 'top',
             offsetY: 5,
             content: function(updateCallback) {
                 return $('#picture-heart-preview').html();
             }
        });

        $('#login_form').clone().appendTo('body').jqm({ trigger: '#picalbums_login_form_show' });
    }
</script>

{if !$isPjax}
	<script type="text/javascript" src="{cfg name='path.root.engine_lib'}/external/prettyPhoto/js/prettyPhoto.js"></script>
	
	<script type="text/javascript">
        {if $aArrayPreload}
            (function($) {
                var cache = [];
                $.preLoadImages = function() {
                    var args_len = arguments.length;
                    for (var i = args_len; i--;) {
                      var cacheImage = document.createElement('img');
                      cacheImage.src = arguments[i];
                      cache.push(cacheImage);
                    }
                }
            })(jQuery);
            $.preLoadImages(
                {foreach from=$aArrayPreload item=oAllPicture name=allpics}
                    '{$oAllPicture->getPicPath()}'{if not $smarty.foreach.allpics.last},{/if}
                {/foreach});
        {/if}

		$(document).ready(function() {
			$('.navigation_next_pjax').pjax('#picture_listing_pjax',{ timeout : 5000, });
			$('.navigation_next_picture_link_pjax').pjax('#picture_listing_pjax',{ timeout : 5000, });
			$('.navigation_prev_pjax').pjax('#picture_listing_pjax', { timeout : 5000, });

            picturePrepare();

            $('#loaded_image').imagesLoaded(function() {
                $('.jquery-note_picture').jQueryNotes({
                    operator: aRouter[picalbumsConfig["picalbums_router_name"]]+'ajaxnote',
                    pictureid: {$oPicture->getId()},
                    picalbums_note_array_json: '{$sNoteArrayJson}'
                });
            });
		});
	</script>
{else}
	<script type="text/javascript">
		$('#loaded_image').imagesLoaded(function() {
		      picturePrepare();

              $('.jquery-note_picture').jQueryNotes({
                  operator: aRouter[picalbumsConfig["picalbums_router_name"]]+'ajaxnote',
                  pictureid: {$oPicture->getId()},
                  picalbums_note_array_json: '{$sNoteArrayJson}' });
		});
	</script>
{/if}
