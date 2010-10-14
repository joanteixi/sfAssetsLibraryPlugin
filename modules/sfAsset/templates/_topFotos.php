<?php //en cas que fotos sigui un array, inserta el slideshow. Sinó, mostra només una foto ?>
<?php use_helper('sfAsset');?>
<?php if (count($fotos) == 0) : ?>
    <img src="/images/toniramon.jpg" alt="Fisiovet" />
<?php elseif (count($fotos) == 1) : ?>
    <?php echo asset_image_tag($fotos[0],'large');?>
<?php else : ?>

    <?php use_javascript('nivo/jquery.nivo.slider.pack.js');?>
    <?php use_stylesheet('/js/nivo/nivo-slider.css');?>
    <?php use_stylesheet('/js/nivo/custom-nivo-slider.css');?>
<div id="slider">
        <?php foreach ($fotos as $foto) : ?>
            <?php echo asset_image_tag($foto,'large');?>
        <?php endforeach ?>
</div>
<?php endif ?>


