<?php
$servername = 'localhost'; // Change accordingly
$username = 'root'; // Change accordingly
$password = ''; // Change accordingly
$db = 'xampp_starter'; // Change accordingly

// TODO: ADD CHECK IF THE USER ALREADY DID A REVIEW
session_start();
// Create connection
$conn = new mysqli($servername, $username, $password, $db);
// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// echo '<p> Noice </p>'

$ta_email = $_POST['ta_email'];
$student_email = $_SESSION['email'];
$course_number = $_POST['course'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];
$year = $_POST['year'];
$term = $_POST['term'];

$conn = new mysqli($servername, $username, $password, $db);
$sql = $conn->prepare('SELECT * FROM User WHERE email = ?');
$sql->bind_param('s', $_SESSION['email']);
$sql->execute();
$result = $sql->get_result();
$user = $result->fetch_assoc();

$sql = $conn->prepare("SELECT UserType.userType FROM UserType INNER JOIN User_UserType 
            ON UserType.idx=User_UserType.userTypeId WHERE User_UserType.userId = ?");
$sql->bind_param('s', $_SESSION['email']);
$sql->execute();
$result = $sql->get_result();
$userTypes = $result->fetch_all();

$username = $user[0] . ' ' . $user[1];

$userArray = [];

foreach ($userTypes as $userType) {
    array_push($userArray, $userType[0]);
}

if (
    in_array('sysop', $userArray) ||
    in_array('admin', $userArray) ||
    in_array('ta', $userArray) ||
    in_array('professor', $userArray)
) {
    echo 'true';
} else {
    echo 'false';
}
