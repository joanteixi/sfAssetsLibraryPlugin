<?php use_helper('I18N') ?>


<?php include_partial('sfAsset/create_folder_header') ?>

<!-- Load Queue widget CSS and jQuery -->
<?php use_stylesheet('/plupload/css/plupload.queue.css');?>
<?php use_javascript('jquery/jquery-1.3.2.min.js');?>
<!-- Thirdparty intialization scripts, needed for the Google Gears and BrowserPlus runtimes -->
<?php use_javascript('/plupload/js/gears_init.js')?>

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<?php use_javascript("/plupload/js/plupload.full.min.js");?>
<?php use_javascript("/plupload/js/jquery.plupload.queue.min.js");?>
<?php use_javascript("/plupload/js/ca.js");?>


<script type="text/javascript">
    // Convert divs to queue widgets when the DOM is ready
    $(function() {
        $("#uploader").pluploadQueue({
            // General settings
            runtimes : 'flash,gears,silverlight,browserplus,html5',
            url : '<?php echo url_for('sfAsset/plupload')?>',
            max_file_size : '20mb',
            chunk_size : '5mb',
            unique_names : false,

            // Specify what files to browse for
            filters : [
                {title : "Tots", extensions : "*"}
            ],

            // Flash settings
            flash_swf_url : '/plupload/js/plupload.flash.swf',

            // Silverlight settings
            silverlight_xap_url : '/plupload/js/plupload.silverlight.xap'
        });

        // Client side form validation
        $('form').submit(function(e) {
            var uploader = $('#uploader').pluploadQueue();

            // Validate number of uploaded files
            if (uploader.total.uploaded == 0) {
                // Files in queue upload them first
                if (uploader.files.length > 0) {
                    // When all files are uploaded submit form
                    uploader.bind('UploadProgress', function() {
                        if (uploader.total.uploaded == uploader.files.length)
                            $('form').submit();
                    });

                    uploader.start();
                } else
                    alert('Has de pujar un fitxer com a mínim.');

                e.preventDefault();
            }
        });
    });
</script>
<?php echo form_tag('sfAsset/massUpload', 'method=post multipart=true') ?>
<h1><?php echo __('Mass upload files', null, 'sfAsset') ?></h1>
<p class="gran">Selecciona primer la carpeta on vols pujar els fitxers</p>
<div class="fila-gran">
    <label for="parent_folder"><?php echo __('Place under:', null, 'sfAsset') ?></label>
    <?php echo select_tag('parent_folder', options_for_select(sfAssetFolderPeer::getAllPaths(), $sf_params->get('parent_folder'))) ?>
</div>
<div id="uploader">
    <p>El teu naveador no té instal·lat Flash, Silverlight, Gears, BrowserPlus o HTML5.</p>
</div>
<input type="submit" value="Grabar dades" />
</form>
<?php include_partial('sfAsset/create_folder_footer') ?>