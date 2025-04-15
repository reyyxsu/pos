<?php 
@ob_start();
session_start();
if(!empty($_SESSION['admin'])){ }else{
	echo '<script>window.location="login.php";</script>';
	exit;
}
require 'koneksi.php';
include $view;
$lihat = new view($config);

// AMANKAN DATA
$toko = $lihat->toko() ?: ['nama_toko' => '-', 'alamat_toko' => '-'];
$hsl = $lihat->penjualan() ?: [];

?>
<html>
<head>
	<title>print</title>
	<link rel="stylesheet" href="assets/css/bootstrap.css">
</head>
<body>
<script>window.print();</script>
<div class="container">
	<div class="row">
		<div class="col-sm-4"></div>
		<div class="col-sm-4">
			<center>
				<p><?php echo $toko['nama_toko']; ?></p>
				<p><?php echo $toko['alamat_toko']; ?></p>
				<p>Tanggal : <?php echo date("j F Y, G:i"); ?></p>
				<p>Kasir : <?php echo htmlentities($_GET['nm_member'] ?? '-'); ?></p>
			</center>
			<table class="table table-bordered" style="width:100%;">
				<tr>
					<td>No.</td>
					<td>Barang</td>
					<td>Jumlah</td>
					<td>Total</td>
				</tr>
				<?php 
				$no = 1;
				$total_sebelum_diskon = 0;
				$total_setelah_diskon = 0;
				foreach ($hsl as $isi) {
					$diskon = isset($isi['diskon']) ? $isi['diskon'] : 0;
					$harga_sebelum = $isi['total'];
					$harga_setelah = $harga_sebelum - ($diskon / 100 * $harga_sebelum);
					$total_sebelum_diskon += $harga_sebelum;
					$total_setelah_diskon += $harga_setelah;
				?>
				<tr>
					<td><?php echo $no++; ?></td>
					<td><?php echo $isi['nama_barang']; ?></td>
					<td><?php echo $isi['jumlah']; ?></td>
					<td><?php echo number_format($isi['total']); ?></td>
				</tr>
				<?php } ?>
			</table>

			<div class="pull-right">
			<b>Total Sebelum Diskon:</b> Rp.<?php echo number_format($total_sebelum_diskon); ?><br/>
			<b>Diskon:</b> <?php echo ($_GET['diskon'] ?? 0); ?>%<br/>
			<b>Total Setelah Diskon:</b> Rp.<?php echo number_format($_GET['total_akhir'] ?? $total_setelah_diskon); ?><br/>
			<b>Bayar:</b> Rp.<?php echo number_format($_GET['bayar'] ?? 0); ?><br/>
			<b>Kembali:</b> Rp.<?php echo number_format($_GET['kembali'] ?? 0); ?>
			</div>
			<div class="clearfix"></div>
			<center>
				<p>Terima Kasih Telah berbelanja di toko kami !</p>
			</center>
		</div>
		<div class="col-sm-4"></div>
	</div>
</div>
</body>
</html>
