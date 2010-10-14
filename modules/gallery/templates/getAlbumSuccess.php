    <div id="main_image"></div>
<ul class="gallery">
    <?php $i=0;foreach ($files as $file) : $ext = $file->getType(); ?>

    <li class='file <?php echo $i==0 ? "active" : ""?> ' >
            <?php $name = $file->getDescription() != '' ? $file->getDescription() : $file->getFileName(); ?>
            <?php //echo image_tag($name .'<span class="final_linia">[creat el '.$file->getCreatedAt('d-m-y').']</span>','iogFileExplorer/download?doc='.$file->getRelativePath(),array('rel' => $file->getRelativePath()));?>
            <?php echo link_to(
                    image_tag($file->getUrl('small'),array('title' => $name)),
                    '/'.$file->getRelativePath('large')
                            ); $i = 1;?>

    </li>
    <?php endforeach ?>
</ul>


<script type="text/javascript">

	$(document).ready(function(){


		$('ul.gallery').galleria({
			
		});
	});
</script>