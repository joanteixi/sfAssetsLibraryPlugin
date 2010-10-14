<ul class="jqueryFileTree" style="display: none;">

    <?php foreach ($dirs as $dir) : ?>

    <li  class="directory collapsed" >
      <!--  <img id="dir_<?php echo $dir->getId()?>" rel="/<?php echo $dir->getRelativePath()?>/" width="18" height="16" class="clickable tree <?php echo $dir->hasChildren() ? 'tree_plus' : ''?> folder_actions" src="/sfAssetsLibraryPlugin/images/spacer.gif" />-->
        <!-- <img width="18" height="16" class="tree tree_folder" rel="/<?php echo $dir->getRelativePath()?>/" src="/sfAssetsLibraryPlugin/images/spacer.gif" />-->
        <a id="id_<?php echo $dir->getId()?>" href="#<?php echo $dir->getRelativePath()?>" class="folder_editable clickable context" rel="/<?php echo $dir->getRelativePath()?>/"><?php echo $dir->getName()?></a>
    </li>

    <?php endforeach ?>
</ul>

<script type="text/javascript">
    $(document).ready(function(){


        $(".directory").contextMenu({
            menu: 'context_folder_menu'
        },
        function(action, el,pos)
        {
            $('body').data('context',el);
            switch(action){
                case 'crear':
                    //afegir un nòdul amb input text al final de arbre carpetes
                    $('#click_to_create_folder').trigger('click');
                    break;

                case 'delete_folder':
                    var c = confirm('Vols esborrar la carpeta? Només serà possible si està buida.');
                    if (c) {
                        //esborra per ajax
                        loading_folder('show');
                        id =  $($('body').data('context')).find('a').attr('id').substr(3);
                        $.post("<?php echo url_for('sfAsset/deleteFolder')?>" , { 'id' : id }, function(data){
                            //recarrega l'arbre de directoris
                            if (data == 'success')
                            {
                                $($('body').data('context')).hide();
                            } else {
                                alert(data);
                            }
                            loading_folder('hide');
                        });
                    }
                    break;

                case 'pujar_fitxer':
                    //obre diàlog per pujar fotos
                    $('#file_window').load("<?php echo url_for('sfAsset/uploadFile')?>",'parent_folder=' + $($('body').data('context')).find('a').attr('rel'));

                    break;

                case 'rename':
                  //obre dialog per canviar el nom
                   $('#click_to_rename_folder').trigger('click');
                    break;

                     //convertir en caixa de text amb jeditable
//                     $old_value =  $($('body').data('context')).find('a').html();
//                     $($('body').data('context')).find('a').editable('<?php echo url_for('sfAsset/renameFolder')?>', {
//                          event : 'dblclick',
//                          style   : 'display: inline'
//});
//                     $($('body').data('context')).find('a').trigger('dblclick');
//                     //jeditable retorna el nou valor del text en mateix lloc.
//                     // cal ara canviar les referències en els atributs rel i href
//                       $($('body').data('context')).find('a').attr('rel').replace($old_value,$($('body').data('context')).find('a').html());

                }
            }

    )
    }

)
</script>