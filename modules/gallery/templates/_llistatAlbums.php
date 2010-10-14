<div class="albums_list">
<h2>Llistat d'àlbums</h2>
<ul>
    <?php foreach ($dirs as $dir) : ?>
    <li id="dir_<?php echo $dir->getId()?>"  class="album"><a   href="#" rel="/<?php echo $dir->getRelativePath()?>/"><?php echo $dir->getName()?></a></li>

    <?php endforeach ?>
</ul>
</div>
<script type="text/javascript">
    $('.album').click(function(){
      $('#album_view').html('<img src="/images/icons/ajax-loader.gif" alt="Carregant..." />')
      $('#album_view').show('fast');
        $.get("<?php echo url_for('gallery/getAlbum');?>" ,{'id' : $(this).attr('id')}, function(data){
            $('#album_view').html(data);
        });
        return false;
    });
</script>