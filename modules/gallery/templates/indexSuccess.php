<?php
slot('menu_dreta');
    include_component('gallery','llistatAlbums');
end_slot();

use_javascript('/sfAssetsLibraryPlugin/js/galleria/src/galleria.js');
use_stylesheet('/sfAssetsLibraryPlugin/js/galleria/src/themes/classic/galleria.css');


?>
<script type="text/javascript">
  Galleria.loadTheme('/sfAssetsLibraryPlugin/js/galleria/src/themes/classic/galleria.classic.js');
</script>
<div id="album_view">
<div id="centre" class="<?php echo $content->getColor()?>_clar">
    <?php echo  $content->getText();?>
</div>

</div>