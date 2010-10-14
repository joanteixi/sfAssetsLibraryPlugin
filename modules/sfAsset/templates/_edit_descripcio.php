  <h4>Descripció<span class="ajuda" > Clica un cop en la descripció per canviar-la en cada idioma</span></h4>
    <ul class="descripcio">
        <?php foreach (sfConfig::get('app_idiomes_web',array('Català' => 'ca_ES')) as $key=>$value) : ?>
        <li><strong>Descripció [<?php echo $key?>]: </strong><span class="editable" id ="<?php echo $sf_asset->getId().'_'.$value?>"> <?php echo $sf_asset->getDescription($value)?> </span></li>
        <?php endforeach ?>

    </ul>
  <p><strong>És pública?:</strong><?php $a = new sfWidgetFormChoice(array('default' => false ,'expanded' => true, 'choices' => array('1' => 'Públic', '0' => 'Privat')), array('class' => 'es_public')); echo $a->render('is_public',  $sf_asset->getIsPublic(), array('class' => 'is_public_list'))?></p>


  <script type="text/javascript">
    $(document).ready(function() {
       //acció al modificar si és públic
    $('input[name=is_public]').click(function(){
      $.post('<?php echo url_for('sfAsset/toogleIsPublic')?>',{'id' : '<?php echo $sf_asset->getId()?>', 'value' : this.value})
    })

    // linia descripció editable

        $('.editable').editable('<?php echo url_for('sfAsset/saveDescription')?>',{
              style   : 'display: inline',
              width   : '200px',
              placeholder: 'Clica per editar la descripció'

        });

      });

</script>