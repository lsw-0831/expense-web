<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;charset=utf-8");

//sqlite数据库文件，自动生成
$db = new PDO('sqlite:expense.db');
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//表不存在就自动创建
$db->exec("CREATE TABLE IF NOT EXISTS expense(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    expense_date TEXT NOT NULL,
    money REAL NOT NULL,
    category TEXT NOT NULL,
    remark TEXT
)");

$action = $_REQUEST['action'];

if($action === 'list'){
    $month = $_REQUEST['month']??'';
    if(!empty($month)){
        $start = $month."-01";
        $end = date("Y-m-t",strtotime($start));
        $stmt = $db->prepare("SELECT * FROM expense WHERE expense_date >= ? AND expense_date <= ? ORDER BY expense_date DESC");
        $stmt->execute([$start,$end]);
    }else{
        $stmt = $db->query("SELECT * FROM expense ORDER BY expense_date DESC");
    }
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action === 'add'){
    $date = $_POST['expense_date'];
    $money = $_POST['money'];
    $cat = $_POST['category'];
    $remark = $_POST['remark'];
    $stmt = $db->prepare("INSERT INTO expense(expense_date,money,category,remark) values(?,?,?,?)");
    $stmt->execute([$date,$money,$cat,$remark]);
    echo json_encode(["ok"=>true]);
}
elseif($action === 'delete'){
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM expense WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(["ok"=>true]);
}
