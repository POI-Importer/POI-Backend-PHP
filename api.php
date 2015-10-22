<?php
include('conf.php');

session_start();
if(!isset($_SESSION['secret'])) {
    header('Location: login.php');
    exit();
}

function printError($msg){
    $json=['error'=>$msg];
    echo json_encode($json);
    exit();
}

function doQuery($con,$stmt){
    $stmt->execute();
    $res = $stmt->query($sql);
    $row=$res->fetchAll(PDO::FETCH_ASSOC);
}

if(!isset($_POST['action'])){
        printError("No action specified");
}

$con = new PDO('mysql:host='.$server.';dbname='.$table.';charset=utf8', $username, $password);

if($_POST['action']=='get_comments'){
    if(!isset($_POST['feature'])){
        printError("No feature specified");
    }
    $stmt = $dbh->prepare("SELECT * FROM content WHERE feature_id=':fid'");
    $stmt->bindParam(':fid', $_POST['feature']);
    doQuery($con,$stmt);
}

if($_POST['action']=='get_status'){
    if(!isset($_POST['features'])){
        printError("No feature specified");
    }
    $features=explode(",",filter_var($_POST['features'],FILTER_SANITIZE_ENCODED));
    $stmt = $dbh->prepare("SELECT * FROM status WHERE feature_id IN (:fid)");
    $stmt->bindParam(':fid', "'".implode("','",$features)."'");
    doQuery($con,$stmt);
}

if($_POST['action']=='comment'){
    if(!isset($_POST['feature']) || !isset($_POST['comment']) || !isset($_POST['status'])){
        printError("No feature specified or no comment or status");
    }
    $stmt = $dbh->prepare("INSERT INTO content VALUES (':fid', ':time', ':username',':comment')");
    $stmt->bindParam(':fid', $_POST['feature']);
    $stmt->bindParam(':time', time());
    $stmt->bindParam(':username', $_SESSION['osm_user']);
    $stmt->bindParam(':comment', $_POST['comment']);  
    doQuery($con,$stmt);

    $stmt = $dbh->prepare("INSERT INTO status VALUES (':fid', ':status') ON DUPLICATE KEY UPDATE status = ':status'");
    $stmt->bindParam(':fid', $_POST['feature']);
    $stmt->bindParam(':status', $_POST['status']);  
    doQuery($con,$stmt);
}