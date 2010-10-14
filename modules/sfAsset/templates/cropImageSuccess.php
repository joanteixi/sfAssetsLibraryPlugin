<?php
use_javascript('/sfAssetsLibraryPlugin/js/jquery.Jcrop.min.js');
use_stylesheet('/sfAssetsLibraryPlugin/css/jquery.Jcrop.css');
use_helper('sfAsset');
?>
<h2>Modifica la miniatura</h2>
<p>Selecciona en la foto grossa la part de la foto que vols com a miniatura. Quan ho tinguis, clica sobre el botó "grabar"</p>
<form action="<?php echo url_for('sfAsset/cropImage')?>" method="POST">
    <input type="hidden" value="<?php echo $sf_params->get('id')?>" name="id" />
    <table class="image_edit">
        <tr>
            <td><?php echo asset_image_tag($asset,'full',array('id' => 'cropbox'));?></td>
            <td>
                <?php //habilita la preview en cas de thumbnail (small image)
                if($sf_params->get('type') == 'small') : ?>
                <div class="container_preview">
                    <?php echo asset_image_tag($asset,'full',array('id' => 'preview'));?>
                </div>
                <?php endif ?>
                
               

            </td>
        </tr>
<tr><td><input type="submit" value="Grabar" /></td></tr>
    </table>
     <input type="hidden" id="width" name="mides[width]" />
                <input type="hidden" id="height" name="mides[height]"/>
                <input type="hidden" id="x" name="mides[x]"/>
                <input type="hidden" id="y" name="mides[y]"/>
                <input type="hidden" name="mides[type]" value="<?php echo $sf_params->get('type','large')?>" />
</form>

<script type="text/javascript">
    $(document).ready(function(){

        var max_width = 800;
        if($('#cropbox').width() >  max_width )
            {
                var ratio = max_width / $('#cropbox').width();
                $('#cropbox').width($('#cropbox').width()*ratio);
            }
      $(function() {
            $('#cropbox').Jcrop({
                onChange: updateSizes
                <?php if($sf_params->get('type') == 'small') : ?>
                onChange: showPreview,
                onSelect: showPreview,
                aspectRatio: 1
                <?php endif ?>
            });

        });

        function updateSizes(coords)
        {
            $("#width").val(coords.w);
            $("#height").val(coords.h);
            $("#x").val(coords.x);
            $("#y").val(coords.y);
        }
        function showPreview(coords)
        {
            var rx = 100 / coords.w;
            var ry = 100 / coords.h;
            $('#preview').css({
                width: Math.round(rx * $('#cropbox').width()) + 'px',
                height: Math.round(ry * $('#cropbox').height()) + 'px',
                marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                marginTop: '-' + Math.round(ry * coords.y) + 'px'
            });
        };
    

    });

</script>