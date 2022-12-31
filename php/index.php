<?php

$file_db = "convert.db";

try {
    $pdo = new PDO("sqlite:$file_db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $sql_create = "CREATE TABLE IF NOT EXISTS `convert`(
        `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
        `nama` text NOT NULL,
        `noPengirim` integer NOT NULL, 
        `jumlah` integer NOT NULL,
        `noTujuan` integer NOT NULL,
        `eWallet` text NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP)";
    $pdo->exec($sql_create);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

header('Content-Type: application/json');

/** 
 * Method REST:
 * 
 * - GET: untuk mendapatkan data dari server
 * - POST: untuk menginputkan data baru
 * - PUT: untuk mengupdate data yang sudah ada
 * - DELETE: untuk menghapus data
 * 
*/

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # untuk mengakses data dari server
    $query = 'select * from convert order by created_at desc';
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # untuk menambahkan data baru dari server
    $nama = $_POST['nama'];
    $noPengirim = $_POST['noPengirim'];
    $jumlah = $_POST['jumlah'];
    $noTujuan = $_POST['noTujuan'];
    $eWallet = $_POST['eWallet'];
    $query = "insert into convert (nama, noPengirim, jumlah, noTujuan, eWallet) values (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $res = $stmt->execute([$nama, $noPengirim, $jumlah, $noTujuan, $eWallet]);
    if ($res){
        $data = ['nama'=>$nama, 'noPengirim'=>$noPengirim, 'jumlah'=>$jumlah, 'noTujuan'=>$noTujuan, 'eWallet'=>$eWallet];
        echo json_encode($data);
    } else {
        echo json_encode(['error'=>$stmt->errorCode()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    # untuk menghapus data dari server
    $id = $_GET['id'];
    $query = "delete from curhat where id = ?";
    $stmt = $pdo->prepare($query);
    $res = $stmt->execute([$id]);
    if ($res){
        $data = ['id'=>$id];
        echo json_encode($data);
    } else {
        echo json_encode(['error'=>$stmt->errorCode()]);
    }
}