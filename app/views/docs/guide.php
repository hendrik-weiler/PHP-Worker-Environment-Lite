<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Documentation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/tuto.css" />
    <link type="text/css" rel="stylesheet" href="css/sh_rand01.min.css">
    <script type="text/javascript" src="js/sh_main.min.js"></script>
    <script type="text/javascript" src="lang/sh_html.min.js"></script>
    <script type="text/javascript" src="lang/sh_php.min.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/tuto.js"></script>
  </head>
  <body onLoad="sh_highlightDocument();">
      <div class="title">Php Worker Enviromnent Lite - Documentation</div>
      <div class="list">
          <div class="smalltitle">Available Docs</div>
          <ul>
              <?php
                $pc = new PWEL_CONTROLLER();
                $pr = new PWEL_ROUTING();
                $db = new eDB(".");
                $select = new eDB_Select("docsdb", array(
                    "type" => "about"
                ));
                foreach($select->result as $row) {
                    print '<li><a href="'.$pc->validateLink($row["url"]).'">'.$row["name"].'</a></li>';
                    if($param == str_replace("/","",$row["url"]))
                        $name = $row["name"];
                }
              ?>
          </ul>
          <div class="smalltitle">Browse Classes</div>
          <ul>
              <?php
                $pc = new PWEL_CONTROLLER();
                $pr = new PWEL_ROUTING();
                $db = new eDB(".");
                $select = new eDB_Select("docsdb", array(
                    "type" => "class"
                ));
                foreach($select->result as $row) {
                    print '<li><a href="'.$pc->validateLink($row["url"]).'">'.$row["name"].'</a></li>';
                    if($param == str_replace("/","",$row["url"]))
                        $name = $row["name"];
                }
              ?>
          </ul>
          <div class="smalltitle">Browse Components</div>
          <ul>
              <?php
                $pc = new PWEL_CONTROLLER();
                $pr = new PWEL_ROUTING();
                $db = new eDB(".");
                $select = new eDB_Select("docsdb", array(
                    "type" => "component"
                ));
                foreach($select->result as $row) {
                    print '<li><a href="'.$pc->validateLink($row["url"]).'">'.$row["name"].'</a></li>';
                    if($param == str_replace("/","",$row["url"]))
                        $name = $row["name"];
                }
              ?>
          </ul>
      </div>
      <div class="content">
          <div class="smalltitle"><?php print $name; ?></div>
          <div class="padding">
              <?php
                if($pc->displayExists($param.".html"))
                    $pc->display($param.".html");
                else
                    $pr->displayError();
              ?>
          </div>
      </div>
</body>
</html>
