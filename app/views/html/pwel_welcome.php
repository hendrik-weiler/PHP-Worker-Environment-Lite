<html>
<head>
<title>PHP Worker Environment Lite</title>
<style type="text/css">
.welcometext {
    margin: 0 14% auto;
}
.subtext {
    margin:0 26% auto;
    font-size:1.2em;
}
.lang {
    text-align: center;
    font-size: 12px;
}
.lang ul {
    display: inline;
    padding: 2px;
    margin: 2px;
}
.lang ul li {
    display: inline;
    padding: 5px;
}
.lang ul li a {
    color: orange;
    text-decoration: none;
}
.examples {
    margin: 0 35% auto;
    text-align: center;
    background: #DDD;
    border: 2px dotted black;
    padding: 7px;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
}
</style>
</head>
<body>
    <div class="lang"><?php print $lang; ?></div>
<div class="welcometext"><h1><?php print $tr->translate("welcome"); ?></h1></div>
<div class="subtext"><?php print $tr->translate("subtext"); ?></div>
<div class="examples">
<?php
    print $tr->translate("examples");
    print "<ul>";
    if(is_array($examples)) {
        
        foreach($examples as $ex) {
            print '<li><a href="'.$ex["link"].'">'.$ex["name"].'</a></li>';
        }
    }
    else {
        print $tr->translate("noexamples");
    }
    print "</ul>";
?>
</div>
</body>
</html>