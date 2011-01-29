$(function(){
    $(".sh_html").css({
        "background":"url(pic/html.png) top right no-repeat #ffcc99",
        "border" : "2px dotted #ff9933"
    });

    $(".sh_php").css({
        "background":"url(pic/php.png) top right no-repeat #ccccff",
        "border" : "2px dotted blue"
    });

    $(".list li").click(function() {
           var href = $(this).children("a").attr("href");
           window.location.href = href;
    }).hover(function() {
        $(this).css({
            "backgroundColor" : "black"
        });
    },function() {
        $(this).css({
            "backgroundColor" : "transparent"
        });
    });
});