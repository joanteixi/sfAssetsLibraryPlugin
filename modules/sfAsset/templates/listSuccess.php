<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_stylesheet('backend.css', 'last') ?>
<?php use_stylesheet('/sfAssetsLibraryPlugin/css/media.css') ?>
<?php use_javascript('/sfAssetsLibraryPlugin/js/jsTree/jquery.jstree.js')  //filetree functions            ?>

<!-- context Menu files -->
<?php use_javascript('/sfAssetsLibraryPlugin/js/jquery.contextMenu/contextBasicMenu.js'); //   context menú right button             ?>
<?php use_stylesheet('/sfAssetsLibraryPlugin/js/jquery.contextMenu/jquery.contextMenu.css'); // context menú right button            ?>

<?php use_javascript('/sfAssetsLibraryPlugin/js/edit.js'); ?>
<?php use_javascript('/sfAssetsLibraryPlugin/js/jeditable.js'); // edit in context             ?>

<!-- fancybox: dialog open window  -->
<?php use_javascript('/sfAssetsLibraryPlugin/js/jquery.fancybox/jquery.fancybox-1.3.1.js'); ?>
<?php use_stylesheet('/sfAssetsLibraryPlugin/js/jquery.fancybox/jquery.fancybox-1.3.1.css'); ?>

<!-- tool for crop images -->
<?php use_javascript('/sfAssetsLibraryPlugin/js/jquery.Jcrop.min.js'); ?>
<?php use_stylesheet('/sfAssetsLibraryPlugin/css/jquery.Jcrop.css'); ?>

<!-- resize ui jquery -> used for resize images -->
<?php use_javascript('/sfAssetsLibraryPlugin/js/jquery.ui/jquery-ui-1.8.resize.min.js'); ?>
<?php use_stylesheet('/sfAssetsLibraryPlugin/js/jquery.ui/ui-lightness/jquery-ui-1.8.2.rezisable.css'); ?>

<!-- add popup css style when popup window -->
<?php if($sf_user->hasAttribute('popup', 'sf_admin/sf_asset/navigation')) use_stylesheet ("/sfAssetsLibraryPlugin/css/popup.css");?>

<?php use_helper('I18N') ?>
<div id="mediateca"
<h1><?php echo __('Asset Library', null, 'sfAsset') ?></h1>

<?php if (!$popup) : ?>
<?php include_partial('list_header', array('folder' => $folder)) ?>
<?php endif; ?>

<input type="hidden" id="is_popup" value="<?php echo $sf_user->hasAttribute('popup', 'sf_admin/sf_asset/navigation') ? $sf_user->getAttribute('popup', null,'sf_admin/sf_asset/navigation') : '0' ?>" />


  <div id="sf_asset_left">
    <div id="loading_folder" style="display:none"><img src="/images/icons/ajax-loader.gif" alt="Carregant..." /></div>
    <h2><?php echo (__("Llistat de carpetes")) ?> </h2>

    <div id="file_explorer"></div>
  </div>

  <div id="sf_asset_container">
    <div class="title">
      <h2>Fitxers</h2>
      <div id="loading_file" style="display:none"><img src="/images/icons/ajax-loader.gif" alt="Processant..." /></div>
    </div>
    <div id="file_window"></div>
  <?php include_partial('sfAsset/messages') ?>
</div>

<?php if (!$popup) : ?>
<?php include_partial('sfAsset/list_footer', array('folder' => $folder)) ?>
<?php endif; ?>

<?php include_partial('context_menu'); ?>


    <div class="modal" id="upload">
      <div class="contentWrap"></div>
    </div>
</div>
    <script type="text/javascript">
      $(document).ready( function() {

    //  afegir la function upload al plugin crrm del tree.
        $.jstree.plugin("crrm", { _fn :
            {
            upload: function (obj) {
             // console.log(obj);
              $('#file_window').load("<?php echo url_for('sfAsset/uploadFile') ?>",'parent_folder_id='+obj.attr('id'))

            }
          }
        }
      );

      //instancia jstree
        $('#file_explorer').jstree({
          "plugins" : [ "themes", "json_data", "ui", "contextmenu","crrm"],
          "json_data" : {
            "ajax" : {
              "url" : "<?php echo url_for('sfAsset/dirList') ?>",
              "data" : function (n) {
                return { id : n.attr ? n.attr("id") : 0 };
              }
            }
          },
          "contextmenu" : {
            items : { // Could be a function that should return an object like this one
              "create" : {
                "separator_before"	: false,
                "separator_after"	: true,
                "label"			: "Nova carpeta",
                "action"		: function (obj) { this.create(obj); }
              },
              "rename" : {
                "separator_before"	: false,
                "separator_after"	: false,
                "label"			: "Renombrar",
                "action"		: function (obj) { this.rename(obj); }
              },
              "remove" : {
                "separator_before"	: false,
                "icon"			: false,
                "separator_after"	: false,
                "label"			: "Esborrar",
                "action"		: function (obj) {
                  if(confirm("Segur que vols esborrar aquesta carpeta i tot el que conté?")) {
                    this.remove(obj);
                  }
                }
              },
              'ccp' : null,
              "upload" : {
                "label" : "Pujar un fitxer aquí",
                "action" : function (obj) { this.upload(obj); }
              }
            }
          }
        });

        /** CAPTURING EVENTS OF TREE **/
        /* CREATE NODE */
        $('#file_explorer').bind('create.jstree',function(e,data) {
          //crear la carpeta nova
          $.post("<?php echo url_for('sfAsset/createFolder') ?>" , {'name' : data.rslt.name,'parent_folder' : $(data.rslt.parent).attr('id')},
          function(json){
            if (json.error) {
              //esborra el node i mostra msg
              $('#file_explorer').jstree('remove',data.rslt.obj);
              alert(json.error_msg);
            } else {
              //posa el id corresponent al nou node
              data.rslt.obj.attr('id',json.id);
              data.rslt.obj.find('a').attr('class','directory');
            }
          })
        })

        /* RENAME NODE */
        $('#file_explorer').bind('rename.jstree',function(e,data) {
          $.post("<?php echo url_for('sfAsset/renameFolder') ?>" , {
            'value' : data.rslt.new_name,
            'id' : data.rslt.obj.attr('id')
          }, function(json){
            //en cas d'error mostra el msg i torna a posar el nom original
            if(json.error) {
              alert(json.error_msg);
              data.rslt.obj.find('a').html('<ins class="jstree-icon">&nbsp;</ins>'+data.rslt.old_name);
            }
          });
        });

        /* DELETE NODE */
        $('#file_explorer').bind('remove.jstree', function(e,data){
          //esborra el node en el servidor
          $.post("<?php echo url_for('sfAsset/deleteFolder') ?>", { 'id' : data.rslt.obj.attr('id')},
          function(json) {
            if(json.error){
              alert(json.error_msg);
              $.jstree.rollback(data.rlbk);
              //cal tornar a posar aquí el node. Hauria de ser millor fer primer el POST i després la acció en el jsTree

            }
          });
        })

      });

      /**
       * show files when click on folder
       **/
      function showFiles(dir_id)
      {
        $('#file_window').load("<?php echo url_for('sfAsset/filesList') ?>", { 'id' : dir_id});
  }
      
  /**
   * Crea una imatge amb un loader en el id_selector especificat
   **/
  function crearLoader(id_selector)
  {
    html = '<img src="/sfAssetsLibraryPlugin/images/spinner.gif" alt="Carregant..." />';
    $('#'+id_selector).html(html);
    $('#'+id_selector).show('fast');
  }

  $("#file_explorer a.directory").live('click',function(){
    //guarda la info del directori clicat.
    //  $('body').data('context',this);
    crearLoader('file_window');
    showFiles($(this).parent().attr('id'));
    return false;
  });
  /**
   * Mostra el gif ajax a la finestra de les imatges.
   **/

  function loading_file(flag)
  {
    if (flag == 'show')
    {
      $('#loading_file').show();
    } else {
      $('#loading_file').hide();
    }
  }


</script>