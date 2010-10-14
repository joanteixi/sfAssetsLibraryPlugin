function setImageField(src)
{
  opener.sfAssetsLibrary.fileBrowserReturn(src);
  window.close();
}

function escollirMida()
{
   $(this).append("<input type='text' value='mida' />");
}

function getUrlParam(paramName)
{
    var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
    var match = window.location.search.match(reParam) ;

    return (match && match.length > 1) ? match[1] : '' ;
}

function setImageFieldCkeditor(src, funcNum)
{
   window.opener.CKEDITOR.tools.callFunction(funcNum, src);
    window.close();
}

function setImageFieldImageTag(src, id)
{
    opener.setImage(src, id);
    window.close();
}

