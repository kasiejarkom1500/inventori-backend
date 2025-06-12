<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Permintaan Barang</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Laporan Barang Keluar</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Permintaan</th>
                <th>Kd Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Satuan</th>
                <th>Jumlah Permintaan</th>
                <th>Ruangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_permintaan = 0;
            foreach ($permintaan as $cs):
                $total_permintaan += $cs['jumlah'];
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $cs['tanggal']; ?></td>
                    <td><?= $cs['kd_barang']; ?></td>
                    <td><?= $cs['nama_barang']; ?></td>
                    <td><?= $cs['kategori']; ?></td>
                    <td><?= $cs['satuan']; ?></td>
                    <td><?= $cs['jumlah']; ?></td>
                    <td><?= $cs['ruangan']; ?></td>
                </tr>
            <?php endforeach; ?>
			<tr>
<td colspan = '6'><div style='text-align:left; font-size:14pt'><b>Total Barang Keluar</b></div></td>
<td style='text-align:left; font-size:14pt'><b><?= $total_permintaan ?></b></td>
</tr>
        </tbody>
    </table>
    
    <script>
        window.print();
    </script>
</body>
</html>
