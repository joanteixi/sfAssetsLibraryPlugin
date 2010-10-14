<?php use_javascript("/sfAssetsLibraryPlugin/plupload/js/plupload.full.min.js"); //upload via flash ?>
<?php use_javascript("/sfAssetsLibraryPlugin/plupload/js/jquery.plupload.queue.min.js");  //upload via flash  ?>
<?php use_stylesheet('/sfAssetsLibraryPlugin/plupload/css/plupload.queue.css');  //upload via flash ?>
<?php use_javascript("/sfAssetsLibraryPlugin/plupload/js/ca.js"); ?>
<!-- Load Queue widget CSS and jQuery -->
<?php use_helper('I18N','jQuery');?>

<script type="text/javascript">
    // Convert divs to queue widgets when the DOM is ready
    $(function() {
        $("#uploader").pluploadQueue({
            // General settings
            runtimes : 'flash,html5',
            url : '<?php echo url_for('sfAsset/plupload')?>',
            max_file_size : '10mb',
            chunk_size : '5mb',
            unique_names : false,

            // Specify what files to browse for
            filters : [
                {title : "Tots", extensions : "*"}
            ],

            // Flash settings
            flash_swf_url : '/sfAssetsLibraryPlugin/plupload/js/plupload.flash.swf'
        });

        // Client side form validation
    });

    function isUploadComplete()
    {
        var uploader = $('#uploader').pluploadQueue();

        // Validate number of uploaded files
        if (uploader.total.uploaded == 0) {
            // Files in queue upload them first
            if (uploader.files.length > 0) {
                // When all files are uploaded submit form
                uploader.bind('UploadProgress', function() {
                    if (uploader.total.uploaded == uploader.files.length)
                        alert('Encara no s\'han pujat els fitxers. Clica de nou quan estiguin pujats');
                        return false;
                });

                uploader.start();
                return false;
            } else
                alert('Has de pujar un fitxer com a mínim.');
            return false;
        }
        return true;
    }
</script>
<?php echo jq_form_remote_tag(array(
'url' => 'sfAsset/uploadFile',
'condition' => "isUploadComplete()",
'complete' => "$($('body').data('context')).find('a:first').trigger('click')"
),array('id' => 'upload_form'))
?>
<h1><?php echo __('Upload file', null, 'sfAsset') ?></h1>
<p class="gran">Selecciona primer la carpeta on vols pujar els fitxers</p>
<div class="fila-gran">
    <label for="parent_folder"><?php echo __('Place under:', null, 'sfAsset') ?></label>
    <?php echo select_tag('parent_folder', options_for_select(sfAssetFolderPeer::getAllPaths(), trim($sf_params->get('parent_folder_id'),'/'))) ?>
</div>
<div id="uploader">
    <p>El teu navegador no té instal·lat Flash, Silverlight, Gears, BrowserPlus o HTML5.</p>
</div>
<input type='hidden' name="parent_folder_id" value="<?php echo $sf_params->get('parent_folder_id')?>" >
<input type="submit" value="Grabar dades" />
</form>