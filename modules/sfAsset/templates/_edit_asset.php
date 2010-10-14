<?php use_helper('I18N', 'Date','JavascriptBase', 'sfAsset');?>
    <div class="foto_details">
      <h3>Detalls<span id="toogle_open" class="toogle_open">&nbsp;</span></h3>
      <div id="description_details">
      <p><strong>Mides:</strong> <?php echo __('%mida% px', array('%mida%' => $sf_asset->getSize('large')), 'sfAsset') ?> / <?php echo __('%weight%', array('%weight%' => $sf_asset->getFilesize('large')), 'sfAsset') ?></p>
        <p><strong>Fitxer:</strong> <?php echo auto_wrap_text($sf_asset->getFilename())?></p>
        <p><?php echo __('Created on %date%', array('%date%' => format_date($sf_asset->getCreatedAt('U'))), 'sfAsset') ?></p>

        <?php include_partial('edit_descripcio', array('sf_asset' => $sf_asset));?>
    </div>
    </div>
<ul class="top_actions">
    <li><a href="#crop" id="crop_file">Retallar</a></li>
    <li><a href="#resize" id="resize_file">Escalar</a></li>
    <li><a href="#restore" id="restore_file">Recuperar original</a></li>
    <li><a href="#end_edit" id="end_edit">Tornar</a></li>
</ul>

<div id="crop_tools" style="display:none">
    Width: <input size="4" type="text" id="width" name="mides[width]" />
    Height: <input size="4" type="text" id="height" name="mides[height]"/>
    X: <input size="4" type="text" id="x" name="mides[x]"/>
    Y: <input size="4" type="text" id="y" name="mides[y]"/>
    <input type="hidden" id="type" name="mides[type]" value="large"/>
    <button id="crop_save">Save</button>
</div>
<div id="resize_tools" style="display:none">
    Width: <input size="4" type="text" id="resize_width" name="resize[width]" />
    Height: <input size="4" type="text" id="resize_height" name="resize[height]"/>
    <button id="resize_save">Save</button>
</div>
<div class="dades_foto">
    <div class="original_foto" id="container_foto">
        <?php include_partial('show_asset',array('sf_asset' => $sf_asset));?>
    </div>

</div>

<script type="text/javascript">
   
// open close button draggable window
$('#toogle_open').toggle(function() {
  $(this).toggleClass('opened');
  $('#description_details').hide();
}, function() {
  $(this).toggleClass('opened');
   $('#description_details').show();
});
    resize = false;
    crop = false;

    //CROP
    $('#crop_file').click(function(){
        //if resize enabled, destroy:
        if (resize)
        {
            $("#original").resizable('destroy');
            $('#original').removeClass('resize');
            $('#resize_tools').hide();

            resize = false;
        }
        crop = true;
        $('#crop_tools').show();
        jcrop_api = $.Jcrop('#original',{
            onChange: updateSizes
        });
        return false;
    });

    function updateSizes(coords)
    {
        $("#width").val(coords.w);
        $("#height").val(coords.h);
        $("#x").val(coords.x);
        $("#y").val(coords.y);
    }

    $('#crop_save').click(function(){
        //per ajax fer un crop de la imatge
        $.post("<?php echo url_for('sfAsset/cropImage')?>",{ 'id' : '<?php echo $sf_asset->getId()?>', 'mides' : {
                'width' : $('#width').val(),
                'height' : $('#height').val(),
                'x' : $('#x').val(),
                'y' : $('#y').val(),
                'type' : 'large'
            }
        }, function(data) {
            //callback function on success
            $('#container_foto').html(data);
            jcrop_api.destroy();
            crop = false;
            $('#crop_tools').hide();

        });
        return false;


    })

    //*** RESTORE
    $('#restore_file').click(function(){
        $.post("<?php echo url_for('sfAsset/scaleAssetAjax')?>",{ 'id' : '<?php echo $sf_asset->getId()?>', 'w' : '800','original_type' : 'full'},function(data){
            $('#container_foto').html(data);
        });
        return false;
    });

    //**** RESIZE
    $('#resize_file').click(function(){
        //disable crop if enabled
        if (typeof jcrop_api != 'undefined'){
            jcrop_api.destroy();
            $('#crop_tools').hide();
            crop = false;
        }
        resize = true;
        $('#resize_tools').show();
        $('#resize_width').val($("#original").width());
        $('#resize_height').val($("#original").height());
        $("#original").addClass('resize')
        r = $("#original").resizable({
            handles: 'all',
            aspectRatio: true,
            grid: 1,
            resize: function(event, ui){
                $('#resize_width').val(ui.size.width);
                $('#resize_height').val(ui.size.height);
            }
        });
        return false;
    });
    $('#resize_width').blur(function(){
        ratio =   parseFloat($('#original').css('width')) / parseFloat($('#original').css('height'));
        value = parseFloat(this.value);
        height = value/ratio;
        $('#resize_height').val(height);
        $('.ui-wrapper').css('width',value);
        $('.ui-wrapper').css('height',  height);
        $('#original').css('width',value);
        $('#original').css('height',  height);
    })
    $('#resize_height').blur(function(){
        ratio =   parseFloat($('#original').css('width')) / parseFloat($('#original').css('height'));
        value = parseFloat(this.value);
        width = ratio*value;
        $('#resize_width').val(width);
        $('.ui-wrapper').css('height',value);
        $('.ui-wrapper').css('width',width);
        $('#original').css('height',value);
        $('#original').css('width',width);
    })
    $('#resize_save').click(function(){
        ui = $("#original").resizable("widget");
        w = $("#original").width();h = $("#original").height();
        $.post("<?php echo url_for('sfAsset/ScaleAssetAjax')?>", {  'id' : '<?php echo $sf_asset->getId()?>', 'w' : w, 'h' : h,'original_type' : 'large' }, function (data) {
            $('#container_foto').html(data);
            $('#resize_tools').hide();
            resize  = false;
        })
        return false;
    });
//TORNAR BUTTON
    $('#end_edit').click(function(){
        //recull el param de la URL després de # i fa click en el directori que correspon,
        $('#file_explorer a.jstree-clicked').trigger('click');
        return false;
    })

    $(document).ready(function() {
        $('.editable').editable('<?php echo url_for('sfAsset/saveDescription')?>',{
              style   : 'display: inline',
              width   : '400px'

        });
    });

    function getURLParam(){
        var strHref = window.location.href;
        if ( strHref.indexOf("#") > -1 ){
            var strQueryString = strHref.substr(strHref.indexOf("#") +1 ).toLowerCase();
        }
        return strQueryString;
    }
</script>