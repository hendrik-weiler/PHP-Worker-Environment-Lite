<html>
<head>
<title>PHP Worker Environment Lite</title>
<style type="text/css">
.subtext {
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
    margin: 0 20% auto;
    text-align: center;
    background: #DDD;
    border: 2px dotted black;
    padding: 7px;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    -o-border-radius: 10px;
    border-radius: 10px;
}
.centering {
    text-align: center;
}
ul {
    padding: 0;
    margin: 0;
}
a {
    color: blue;
    text-decoration: none;
}
a:visited {
    color: blue;
    text-decoration: none;
}
</style>
</head>
<body>
    <div class="lang"><?php $pc = new PWEL_CONTROLLER(); print $lang." | "; print ' <a href="'.$pc->validateLink("../docs").'">'.$tr->translate("documentation").'</a>'; ?></div>
<div class="centering">
    <div><h1><?php print $tr->translate("welcome"); ?></h1></div>
    <div class="subtext"><?php print $tr->translate("subtext"); ?></div>
</div>
</body>
</html>