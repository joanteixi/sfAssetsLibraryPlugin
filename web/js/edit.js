/**
 * Funció que crida per AJAX a una funció PHP que escala la imatge
 * @return Stringn path a la imatge escalada
 */
function escalarImatge(url, id, type, w)
{
    $.post(url, {
        'id' : id,
        'type' : type,
        'w' : w
    }, function(data) {
        //on success redibuixa la imatge
        $('#escalar').parent().parent().find('img').attr('src',data);
    })
}


