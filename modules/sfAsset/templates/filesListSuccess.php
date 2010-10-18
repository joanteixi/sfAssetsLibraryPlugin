<?php use_helper('sfAsset'); ?>
<?php //create an input hidden tag to control from javascript code if is popup ?>
<input type="hidden" id="is_popup" value="<?php echo $sf_user->hasAttribute('popup', 'sf_admin/sf_asset/navigation') ? '1' : '0' ?>" />
<?php if (count($files)) : ?>
<?php
  foreach ($files as $asset) {
    include_partial('asset', array('sf_asset' => $asset));
  } ?>
<?php else : ?>
    <h2>No hi ha cap fitxer en aquesta carpeta</h2>
<?php endif ?>

    <div style="display:none">

      <div id="rename_file" >
        <form id="rename_form" method="POST" action="">

          <h2>Canvia el nom del fitxer</h2>
          <p>Escriu el nou nom del fitxer (sense punts ni espais)</p>
          <!-- input form. you can press enter too -->
          <input id="nom_fitxer"/>
          <button type="submit">Renombrar</button>
        </form>
        <br />
      </div>
    </div>

    <div style="display:none">

      <div id="move_file" >
        <form id="move_form" method="POST" action="">
          <h2>Selecciona la carpeta destí</h2>
          <!-- carrega per ajax l'estructura de carpetes al visualitzar aquest form. Així no cal carregar-ho cada cop -->
          <select id="move_file_select_tag" name="carpeta"></select>
          <button type="submit">Moure</button>
        </form>
        <br />
      </div>
    </div>
    <a id="click_to_rename_file" href="#rename_file"></a>
    <a id="click_to_move_file" href="#move_file"></a>

    <script type='text/javascript'>
      $(document).ready(function(){
        $(".assetImage").contextMenu('context_file_menu', {
          menuStyle: {
            width: '180px'
          },
          itemStyle : {
            padding: '0 3px 0 20px'
          },
          onShowMenu: function(e, menu) {
            if ($('#is_popup').val() == 0)
              {
                $('#select_full, #select_small, #select_large,#separator_images',menu).remove()
              }
            return menu
          },

          bindings: {
            'edit_file' : function(t) {
              $(t).find('a').trigger('click');
            },

            'move_file' : function (t) {
              //obre finestra per seleccionar a on volem moure el file. L'ideal seria arrossegar cap a directoris... :-(
              $('body').data('file_clicked',$(t).attr('id'));
              //carrega estructura carpetes en el form
              $("#move_file_select_tag").load('<?php echo url_for('sfAsset/getFolderList') ?>');
              $('#click_to_move_file').trigger('click');
            },
            'select_small' : function (t) {
              src = $(t).find('a.small_image').attr('rel');
              setImageFieldCkeditor(src, "<?php echo $sf_user->getAttribute('CKEditorFuncNum', null, 'sf_admin/sf_asset/navigation') ?>");

            },
            'delete_file' : function (t) {
              c = confirm('Estàs segur que vols esborrar el fitxer?');
              if (c) {
                loading_file('show');
                $.post("<?php echo url_for('sfAsset/deleteAsset') ?>" , { 'id' : t.id }, function(data){
                  if(!data) {
                    alert('No es pot esborrar el fitxer.');
                  } else {
                    $(t).remove();
                  }
                  loading_file('hide');
                }
              )
              }
            },
            'select_large' : function (t) {
              src = $(t).find('a.large_image').attr('rel');
              setImageFieldCkeditor(src, "<?php echo $sf_user->getAttribute('CKEditorFuncNum', null, 'sf_admin/sf_asset/navigation') ?>");
            },
            'select_full': function(t) {

              //si és popup del tipus 6 ha de retornar una url per validar que es pot baixar el document
<?php if ($sf_user->getAttribute('popup', null, 'sf_admin/sf_asset/navigation') == 6) : ?>
                id = $(t).find('a.download').attr('rel');
                src = "/sfAssetFront/download?id="+id;
                setImageFieldCkeditor(src, "<?php echo $sf_user->getAttribute('CKEditorFuncNum', null, 'sf_admin/sf_asset/navigation') ?>");


<?php else : ?>

                  src = $(t).find('a.full_image').attr('rel');
                  setImageFieldCkeditor(src, "<?php echo $sf_user->getAttribute('CKEditorFuncNum', null, 'sf_admin/sf_asset/navigation') ?>");
<?php endif ?>
                },

                'rename_file': function(t)
                {
                  $('body').data('file_clicked',$(t).attr('id'));
                  $('#click_to_rename_file').trigger('click');
                }
              }
            });


<?php if (!$sf_user->hasAttribute('popup', 'sf_admin/sf_asset/navigation')): ?>
              //              $("#context_file_menu").disableContextMenuItems('#select_small,#select_large,#select_full');
<?php endif ?>
              $(".asset").click(function(){
                haystack = $(this).attr('href')+"";
                needle = '#';
                href = haystack.slice( haystack.indexOf( needle ) +1 );
                $('#file_window').load(href);
                return false;
              })

              $('#click_to_rename_file').fancybox({
                'scrolling'		: 'no',
                'titleShow'		: false,
                'onComplete'            : function() { $('#nom_fitxer').focus() },
                'onClosed'		: function() {
                }

              });

              $('#click_to_move_file').fancybox({
                'scrolling'		: 'no',
                'titleShow'		: false,
                'width'                 : 500,
                'height'                : 100,
                'autoDimensions'        : false,
                'onClosed'		: function() {
                }

              });

              $('#move_form').bind("submit", function() {
                $.fancybox.showActivity();
                $.post("<?php echo url_for('sfAsset/moveAsset') ?>", {
                  'new_folder' : $('#move_file_select_tag').val(),
                  'id' : $('body').data('file_clicked')
                }, function (data){
                  if (data == 'success')
                  {
                    $.fancybox.close();
                    //remove from DOM asset
                    id = $('body').data('file_clicked');
                    $('#'+id).hide('slow');
                  } else {
                    $.fancybox(data);
                  }
                  //cal fer desapareixer ara el fitxer.
                  //
                  //$('#'+($('body').data('file_clicked'))).find('.filename').html(data);
                });
                return false;
              })

              // acció al renombrar fitxer.
              $("#rename_form").bind("submit", function() {
                $.fancybox.showActivity();
                $.post("<?php echo url_for('sfAsset/renameAsset') ?>" ,
      {
        'new_name' : $('#nom_fitxer').val(),
        'id' :  $('body').data('file_clicked')
      }, function(data){
        $.fancybox.close();
        $('#'+($('body').data('file_clicked'))).find('.filename').html(data);
      });

      return false;
    });



  }); //fi document ready


</script>
