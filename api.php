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

if(!isset($_POST['action'])){
        printError("No action specified");
}

$con = new PDO('mysql:host='.$server.';dbname='.$table.';charset=utf8', $username, $password);

if($_POST['action']=='get_comments'){
    if(!isset($_POST['feature'])){
        printError("No feature specified");
    }
    $stmt = $con->prepare("SELECT * FROM content WHERE feature_id=:fid");
    $bind = ['fid' => $_POST['feature']];
    $res = $stmt->execute($bind);
    $row=$stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
}

if($_POST['action']=='get_status'){
    if(!isset($_POST['features'])){
        printError("No feature specified");
    }
    $features=explode(",",filter_var($_POST['features'],FILTER_SANITIZE_ENCODED));
    $question=array_fill(0,count($features),'?');
    $stmt = $con->prepare("SELECT * FROM status WHERE feature_id IN (".implode(', ',$question).")");
    $res = $stmt->execute($features);
    $row=$stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
}

if($_POST['action']=='comment'){
    if(!isset($_POST['feature']) || !isset($_POST['comment']) || !isset($_POST['status'])){
        printError("No feature specified or no comment or status");
    }
    $stmt = $con->prepare("INSERT INTO content VALUES (:fid, :time, :username, :comment)");
    $bind = ['fid' => $_POST['feature'],
    'time' => time(),
    'username' => $_SESSION['osm_user'],
    'comment' => $_POST['comment']
    ];
    $res = $stmt->execute($bind);

    $stmt = $con->prepare("INSERT INTO status VALUES (:fid, :status) ON DUPLICATE KEY UPDATE status = :status");
    $bind = ['fid' => $_POST['feature'],
    'status' => $_POST['status']
    ];
    $res = $stmt->execute($bind);
}
