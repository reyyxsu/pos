<?php 
@ob_start();
session_start();
if(!empty($_SESSION['admin'])){ }else{
    echo '<script>window.location="login.php";</script>';
    exit;
}
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=data-laporan-".date('Y-m-d').".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); 

require 'config.php';
include $view;
$lihat = new view($config);

$bulan_tes = array(
    '01'=>"Januari", '02'=>"Februari", '03'=>"Maret", '04'=>"April",
    '05'=>"Mei", '06'=>"Juni", '07'=>"Juli", '08'=>"Agustus",
    '09'=>"September", '10'=>"Oktober", '11'=>"November", '12'=>"Desember"
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Excel</title>
</head>
<body>
    <h3 style="text-align:center;"> 
        <?php 
        if(!empty($_GET['cari'])){
            echo "Data Laporan Penjualan ".$bulan_tes[$_GET['bln']]." ".$_GET['thn'];
        } elseif(!empty($_GET['hari'])){
            echo "Data Laporan Penjualan ".$_GET['tgl'];
        } else {
            echo "Data Laporan Penjualan ".$bulan_tes[date('m')]." ".date('Y');
        }
        ?>
    </h3>

    <table border="1" width="100%" cellpadding="3" cellspacing="0">
    <thead>
    <tr style="background-color:#d9edf7;">
        <th>No</th>
        <th>ID Barang</th>
        <th>Nama Barang</th>
        <th>Nama Kasir</th>
        <th>Jumlah</th>
        <th>Modal Awal</th>
        <th>Total</th>
        <th>Diskon</th>
        <th>Total Akhir</th>
        <th>Tanggal Input</th>
        <th>Periode</th>
    </tr>
</thead>
<tbody>
    <?php 
    $no=1;
    $jumlah = $total = $modal = $total_akhir = 0;

    if(!empty($_GET['cari'])){
        $periode = $_GET['bln'].'-'.$_GET['thn'];
        $hasil = $lihat->periode_jual($periode);
    } elseif(!empty($_GET['hari'])){
        $hari = $_GET['tgl'];
        $hasil = $lihat->hari_jual($hari);
    } else {
        $hasil = $lihat->jual();
    }

    foreach($hasil as $isi){
        $jumlah += $isi['jumlah'];
        $total += $isi['total'];
        $modal += $isi['harga_beli'] * $isi['jumlah'];
        
        $diskon_persen = $isi['diskon'];
        $diskon = ($isi['total'] * $diskon_persen / 100);
        $akhir = $isi['total'] - $diskon;
        $total_akhir += $akhir;

        $periode = date('m-Y', strtotime($isi['tanggal_input']));
    ?>
    <tr>
        <td><?= $no; ?></td>
        <td><?= $isi['id_barang']; ?></td>
        <td><?= $isi['nama_barang']; ?></td>
        <td><?= $isi['nm_member']; ?></td>
        <td><?= $isi['jumlah']; ?></td>
        <td>Rp.<?= number_format($isi['harga_beli'] * $isi['jumlah']); ?>,-</td>
        <td>Rp.<?= number_format($isi['total']); ?>,-</td>
        <td><?= $diskon_persen; ?>%</td>
        <td>Rp.<?= number_format($akhir); ?>,-</td>
        <td><?= $isi['tanggal_input']; ?></td>
        <td><?= $periode; ?></td>
    </tr>
    <?php $no++; } ?>

    <!-- TOTAL -->
    <tr style="font-weight:bold; background-color:#f2f2f2;">
        <td colspan="4">Total Terjual</td>
        <td><?= $jumlah; ?></td>
        <td>Rp.<?= number_format($modal); ?>,-</td>
        <td>Rp.<?= number_format($total); ?>,-</td>
        <td colspan="1">Total Pemasukan Sebelum / Sesudah Diskon</td>
        <td>Rp.<?= number_format($total_akhir); ?>,-</td>
        <td style="background-color:#0bb365;color:#fff;">Keuntungan</td>
        <td style="background-color:#0bb365;color:#fff;">Rp.<?= number_format($total_akhir - $modal); ?>,-</td>
    </tr>
</tbody>
    </table>
</body>
</html>
