<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kendali</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .sub-header {
            margin-bottom: 20px;
        }
        .sub-header p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
        .footer {
            text-align: right;
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://bpsjambi.id/inventori/img/logo.png" alt = "<?= FCPATH ?>" width="100" height="50" />
        KARTU PERSEDIAAN BARANG PAKAI HABIS (5218111)
    </div>
    <div class="sub-header">
        <p><strong>Nama Barang:</strong> <?= $nama_barang; ?></p>
        <p><strong>Kode Barang:</strong> <?= $kd_barang; ?></p>
        <p><strong>Satuan:</strong> <?= $satuan; ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No.Bon/Faktur</th>
                <th>Tgl</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Sisa Barang</th>
                <!--<th>Uraian Pemasukan/Pengeluaran</th>-->
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= $row['faktur']; ?></td>
                        <td><?= $row['tanggal']; ?></td>
                        <td><?= $row['masuk']; ?></td>
                        <td><?= $row['keluar']; ?></td>
                        <td><?= $row['current_stok']; ?></td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No data available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="footer">
        Halaman: 1
    </div>
</body>
</html>
