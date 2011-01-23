<?php
    class start extends PWEL_CONTROLLER {
        function startup() {
            $lang["de"] = array(
                "name" => "Deutsch",
                "link" => $this->validateLink("/de/")
            );
            $lang["eng"] = array(
                "name" => "English",
                "link" => $this->validateLink("/eng/")
            );
            $this->lang = "Language: <ul>";
            foreach($lang as $language) {
                $this->lang .= "<li>".'<a href="'.$language["link"].'">'.$language["name"]."</a></li>";
            }
            $this->lang .= "</ul>";
        }

        function index() {
            $this->tr = new translator("langfiles/");
            $this->display("pwel_welcome");
        }

        function addtr() {
            $form = new Form();
           print $form->open($this->validateLink("/".PWEL_COMPONENT_ROUTE::$variables["lang"]."/start/inserttr"), "post")
                 ->label("Language")->tfield("lang", $value)
                 ->label("Keyword")->tfield("keyword", $value)
                 ->label("Translation")->tfield("translation", $value)
                 ->button("Add")
                 ->close();
           print '<a href="'.$this->validateLink("/").'">Back</a>';
        }

        function inserttr() {
            new eDB("./langfiles/");
            new eDB_Insert("translation_".PWEL_COMPONENT_ROUTE::$variables["lang"],
                    array(
                        "id" => "*", "keyword" => $_POST["keyword"], "translation" => $_POST["translation"]
                    ));
            $url = new PWEL_URL();
            $url->redirect("/".PWEL_COMPONENT_ROUTE::$variables["lang"]."/start/addtr");
        }

        function autosearch() {
            print "\$this->display(\"autosearchtest\"); <br />He found the file in html/somesubfolder/<p>";
            $this->display("autosearchtest");
        }

    }
?>