<?php
include "db.php";
include "generateStudents.php";

if(!dbExists("school")) {
    createDatabase();
} else {
    echo "adatbázis már létezik";
}

function createDatabase() {

    createDbs("school");
    
    execSql("CREATE TABLE classes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(10) NOT NULL
    );");

    $values = [];
    foreach (CLASSES as $class) {
        $values[] = "('$class')";
    }

    $sql = "INSERT INTO classes (name) VALUES " . implode(',', $values);

    $cmd = execSql($sql);

    execSql("CREATE TABLE subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    );");

    $values = [];
    foreach (SUBJECTS as $subject) {
        $values[] = "('$subject')";
    }

    $sql = "INSERT INTO subjects (name) VALUES " . implode(',', $values);

    $cmd = execSql($sql);

    execSql("CREATE TABLE students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        gender INT NOT NULL,
        class_id INT,
        FOREIGN KEY (class_id) REFERENCES classes(id)
    );");

    execSql("CREATE TABLE grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        subject_id INT,
        grade INT NOT NULL,
        FOREIGN KEY (student_id) REFERENCES students(id),
        FOREIGN KEY (subject_id) REFERENCES subjects(id)
    );
    ");

    
    echo "Adatbázis létrehozva";
}

generateStudents();