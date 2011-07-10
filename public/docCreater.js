$(function() {
    $('.clickTextarea').live('click', function() {
          if(!$(this).find('textarea').is('textarea')) {
            var $val = $(this).parent().find('span').html();
            $(this).html('<textarea cols="80" rows="7" name="text">'+ $val.replace(/<br>/g,'\n') +'</textarea><br /><input style="width:70px; height:25px;" type="button" class="save" value="Save" />');
          }
    });

    $('.save').live('click', function() {
       var $text =  $(this).parent().find('textarea').val();
       $(this).parent().parent().html('<span class="clickTextarea">' + $text.replace(/\n/g,"<br>") + '</span>');
    });

    $('.copy').live('click',function() {
        $text = $('body').html();
        var $search = '<input style="margin:8px; height:25px; width:150px;" value="To HTML" type="button" class="copy" />';
        $('body').html('<textarea cols="100" rows="60" maxlength="10000">' + $text.replace($search, '') + '</textarea>');
    });
});