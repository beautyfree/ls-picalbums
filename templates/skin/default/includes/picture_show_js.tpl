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

        timeout = { timeout : 5000, };
		$(document).ready(function() {
			$('.navigation_next_pjax').pjax('#picture_listing_pjax',timeout);
			$('.navigation_next_picture_link_pjax').pjax('#picture_listing_pjax',timeout);
			$('.navigation_prev_pjax').pjax('#picture_listing_pjax', timeout);

			$('#form_comment_text').markItUp(getMarkitupCommentSettings());
			share42('sharediv', '{$sTemplateWebPathPicalbumsPlugin}/images/',
                                '{$sAlbumPath}{$oAlbum->getURL()}/{$oPicture->getURL()}/',
                                '{$oPicture->getDescription()|escape:'html'}');

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

            {if $bNoteActivate}
                $('#loaded_image').imagesLoaded(function() {
                    $('.jquery-note_picture').jQueryNotes({
                        operator: aRouter[picalbumsConfig["picalbums_router_name"]]+'ajaxnote',
                        pictureid: {$oPicture->getId()} });
                });
            {/if}
		});
	</script>
{else}
	<script type="text/javascript">
		$('#loaded_image').imagesLoaded(function() {
		      $('#form_comment_text').markItUp(getMarkitupCommentSettings());
			  share42('sharediv', '{$sTemplateWebPathPicalbumsPlugin}/images/',
                                  '{$sAlbumPath}{$oAlbum->getURL()}/{$oPicture->getURL()}/',
                                   '{$oPicture->getDescription()|escape:'html'}');

			  $('.original-imagegal').prettyPhoto({
					   social_tools:'',
					   show_title: false,
					   slideshow:false,
					   deeplinking: false,
					   overlay_gallery: false,
			  });
			  
			  {if $bNoteActivate}
					$('.jquery-note_picture').jQueryNotes({	
						operator: aRouter[picalbumsConfig["picalbums_router_name"]]+'ajaxnote',
						pictureid: {$oPicture->getId()} });
			  {/if}
		});
	</script>
{/if}
