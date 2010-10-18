<?php echo javascript_include_tag('/sfAssetsLibraryPlugin/swfupload/swfupload.js'); ?>
<?php echo javascript_include_tag('/sfAssetsLibraryPlugin/swfupload/swfupload.queue.js'); ?>
<?php echo javascript_include_tag('/sfAssetsLibraryPlugin/swfupload/fileprogress.js'); ?>
<?php echo javascript_include_tag('/sfAssetsLibraryPlugin/swfupload/handlers.js'); ?>
<?php echo stylesheet_tag('/sfAssetsLibraryPlugin/swfupload/css/default.css'); ?>

<!-- Load Queue widget CSS and jQuery -->
<?php use_helper('I18N'); ?>
<script type="text/javascript">
  $(document).ready(function() {

 var swfu;

    var settings = {
      flash_url : "/sfAssetsLibraryPlugin/swfupload/swfupload.swf",
      upload_url: "<?php echo url_for('sfAsset/uploadSwf')?>",
      post_params: {
         "swfaction" : "<?php echo session_id(); ?>",
         "parent_folder_id" : "<?php echo $sf_params->get('parent_folder_id') ?>"
         },
      file_size_limit : "100 MB",
      file_types : "*.jpg; *.pdf",
      file_types_description : "JPG Images, PDF docs",
      file_upload_limit : 100,
      file_queue_limit : 0,
      custom_settings : {
        progressTarget : "fsUploadProgress",
        cancelButtonId : "btnCancel"
      },
      debug: false,

      // Button settings
      button_image_url: "/sfAssetsLibraryPlugin/swfupload/images/TestImageNoText_65x29.png",
      button_width: "65",
      button_height: "29",
      button_placeholder_id: "spanButtonPlaceHolder",
      button_text: '<span class="theFont">Upload</span>',
      button_text_style: ".theFont { font-size: 16; }",
      button_text_left_padding: 12,
      button_text_top_padding: 3,
				
      // The event handler functions are defined in handlers.js
      file_queued_handler : fileQueued,
      file_queue_error_handler : fileQueueError,
      file_dialog_complete_handler : fileDialogComplete,
      upload_start_handler : uploadStart,
      upload_progress_handler : uploadProgress,
      upload_error_handler : uploadError,
      upload_success_handler : uploadSuccess,
      upload_complete_handler : uploadComplete,
      queue_complete_handler : queueComplete	// Queue plugin event
    };

    swfu = new SWFUpload(settings);

    //Afegir clicks per editar les imatges
    $('.upload_thumbnail').live('click',function() {
      $('#file_window').load("<?php echo url_for('sfAsset/edit')?>/id/"+this.id);
    })
})

</script>


<h1><?php echo __('Upload file', null, 'sfAsset') ?></h1>

	<div id="thumbnails"></div>
        <div class="fieldset flash" id="fsUploadProgress">
			<span class="legend">Upload Queue</span>
			</div>
		<div id="divStatus">0 Files Uploaded</div>
			<div>
				<span id="spanButtonPlaceHolder"></span>
				<input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
			</div>

