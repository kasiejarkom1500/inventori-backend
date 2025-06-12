<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Stok Barang</title>
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
        @media print {
            body {
                font-family: Arial, sans-serif;
            }
            h1 {
                text-align: center;
            }
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
            .total-row td {
                font-weight: bold;
            }
        }
    </style>
</head>
<body>
    <h1>Laporan Stok Barang</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Permintaan</th>
                <th>Kd Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Satuan</th>
                <th>Stok</th>

                <th>Jumlah Permintaan</th>
                <th>Ruangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_permintaan = 0;
            if (!empty($permintaan)) {
                foreach ($permintaan as $cs):
                    $total_permintaan += $cs['jumlah'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($no++, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($cs['tanggal'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($cs['kd_barang'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($cs['nama_barang'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($cs['kategori'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($cs['satuan'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($cs['stok'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($cs['jumlah'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($cs['ruangan'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php 
                endforeach;
            } else {
                echo "<tr><td colspan='8'>No data available</td></tr>";
            }
            ?>
            <tr class="total-row">
                <td colspan="7" style="text-align:left; font-size:14pt"><b>Total Barang Keluar</b></td>
                <td style="text-align:left; font-size:14pt"><b><?= htmlspecialchars($total_permintaan, ENT_QUOTES, 'UTF-8') ?></b></td>
            </tr>
        </tbody>
    </table>
    
    <script>
        window.print();
    </script>
</body>
</html>
