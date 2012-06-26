<script type="text/javascript">
var DIR_PICALBUM_PLUGIN = '{$sTemplateWebPathPicalbumsPlugin}';


var picalbumsConfig = { "picalbums_router_name" : '{$oConfig->GetValue('plugin.picalbums.albums_router_name')}',
                        "ajax_upload_progress_disable" : {if $oConfig->GetValue('plugin.picalbums.ajax_upload_progress_disable')}true{else}false{/if},
                        "ajax_upload_max_connections" : {$oConfig->GetValue('plugin.picalbums.ajax_upload_max_connections')},
                        "ajax_max_size_upload_file" : {$oConfig->GetValue('plugin.picalbums.ajax_max_size_upload_file')},
                        "text_form_max_characters" : {$oConfig->GetValue('plugin.picalbums.text_form_max_characters')},
                        "pjax_for_picture_listing" : {if $oConfig->GetValue('plugin.picalbums.pjax_for_picture_listing')}true{else}false{/if},};
</script>
