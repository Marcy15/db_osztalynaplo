<?php
include_once "config.php";
include_once "db.php";

function generateStudents() {
    
    foreach(CLASSES as $class) {
        $osztalyletszam = rand(MIN_CLASS_COUNT,MAX_CLASS_COUNT);
        echo "<br>";
        for ($i = 0; $i < $osztalyletszam; $i++) {
            
            $nem = rand(0,1);
            $lastname = NAMES["lastnames"][rand(0, count(NAMES["lastnames"]) - 1)];
            $firstname = "";
            if($nem == 0) {
                $firstname = NAMES["firstnames"]["men"][rand(0, count(NAMES["firstnames"]["men"]) - 1)];
            } else {
                $firstname = NAMES["firstnames"]["women"][rand(0, count(NAMES["firstnames"]["women"]) - 1)];
            }

            echo "<br>".$nem." ".$lastname." ".$firstname;
            $osztalyId = execSql("SELECT id FROM classes WHERE name = '".$class."';");
            var_dump($osztalyId[0]["id"]);
            //echo " osztályid: ".$osztalyId;
        }
    }
}