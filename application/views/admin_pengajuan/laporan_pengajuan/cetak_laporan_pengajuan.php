<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Pengajuan Barang</title>
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
            text-align: center;
        }
        h1 {
            text-align: center;
            font-size: large;

        }
        h2{
            text-align: right;
        }
        h3{
            padding: auto;
        }
        h4{
            padding-left: 15px;
        }
        p {
            font-size:medium;
            text-align: justify;
            padding-left: 15px;
            text-indent: 0;
            padding-right: 10px;
        }
        .invisible-table, .invisible-table td {
            border: none;
            text-align:left
        }
        .page-break {
            page-break-before: always;
        }
        .logo {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
        }
    </style>
</head>
<body>
    <h1>KERANGKA ACUAN KERJA</h1>
    <h2 style="text-align:center"><i>KEPERLUAN SEHARI-HARI PERKANTORAN</i></h2>
    <table class="invisible-table">
        <tr>
            <td>Kementerian Negara/Lembaga</td>
            <td>:BADAN PUSAT STATISTIK</td>
        </tr>
        <tr>
            <td>Satuan Kerja</td>
            <td>:BPS PROVINSI JAMBI</td>
        </tr>
        <tr>
            <td>Pembebanan</td>
            <td>:DIPA NOMOR:SP-DIDA 054.01.2.428145/2024</td>
        </tr>
        <tr>
            <td>Program</td>
            <td>:(WA) PROGRAM DUKUNGAN MANAJEMEN</td>
        </tr>
        <tr>
            <td>Kegiatan</td>
            <td>:(2886) DUKUNGAN MANAJEMEN DAN PELAKSANAAN<br>TUGAS TEKNIS LAINNYA BPS PROVINSI (2886)</td>
        </tr>
        <tr>
            <td>Penanggung Jawab</td>
            <td>:TIM RUMAH TANGGA, KEARSIPAN DAN <br> PENGELOLAAN BMN</td>
        </tr>
    </table>
    <img src="<?= base_url('img/logobps.png') ?>" alt="Logo BPS" class="logo">
    <div class="page-break"></div>

    <h1><b>KERANGKA ACUAN KERJA (TERM OF REFERENCE)</b><br>PER KELUARAN KEGIATAN</h1>
    <h3><b>A. Dasar Hukum</b></h3>
    <p style="margin-top: -15px; padding-left:22px;">1. Undang-Undang Nomor 16 Tahun 1997 tentang Statistik (Lembaran Negara Republik Indonesia Tahun 1997 Nomor 39, Tambahan Lembaran Negara Republik Indonesia Nomor 3683);<br>
    2. Undang-Undang Nomor 17 Tahun 2003 tentang Keuangan Negara; 3. Undang-Undang Nomor 1 Tahun 2004 tentang Perbendaharaan Negara;<br>
    3. Undang-Undang Nomor 1 Tahun 2004 tentang Pemeriksaan Pengelolaan dan Tanggung Jawab Keuangan Negara <br>
    4. Undang-Undang Nomor 15 Tahun 2004 tentang Pemeriksaan Pengelolaan dan Tanggung Jawab Keuangan Negara;<br>
    5. Peraturan Presiden Republik Indonesia Nomor 16 Tahun 2018 tentang Pengadaan Barang dan Jasa Pemerintah, sebagaimana telah diubah dengan Peraturan Presiden Republik Indonesia Nomor 12 Tahun 2021 tentang Perubahan atas Peraturan Presiden Republik Indonesia Nomor 16 Tahun 2018 tentang Pengadaan Barang dan Jasa Pemerintah; <br>
    6. Peraturan Menteri Keuangan Nomor 210/PMK.05/2022 Tahun 2022 tentang Tata Cara Pembayaran dalam rangka Pelaksanaan Anggaran Pendapatan dan Belanja Negara; <br>
    7. Peraturan Menteri Keuangan Republik Indonesia Nomor 49 Tahun 2023 tentang Standar Biaya Masukan Tahun Anggaran 2024; <br>
    8. Peraturan Menteri Keuangan Republik Indonesia Nomor 62 Tahun 2023 tentang Perencanaan Anggaran, Pelaksanaan Anggaran, serta Akuntansi dan Pelaporan Keuang; <br>
    9. Keputusan Direktur Jenderal Perbendaharaan Nomor KEP-331/PB/2021 tentang Kodefikasi Segmen Akun pada BaganAkun Standar.<br></p>

    <h3><b>B. Latar Belakang</b></h3>
    <p style="margin-top: -15px; padding-left:22px;">Belanja Keperluan Perkantoran digunakan untuk membiayai keperluan sehari-hari perkantoran yang secara langsung menunjang kegiatan operasional Kementerian/Lembaga. Belanja keperluan perkantoran termasuk kedalam jenis belanja barang dan jasa, 
    yakni pengeluaran untuk menampung pembelian barang dan jasa yang habis pakai untuk memperduksi barang dan jasa yang dipasarkan maupun yang tidak dipasarkan serta pengadaan barang yang dimaksudkan untuk diserahkan atau dijual kepada masyarakat 
    dan belanja perjalanan. <br></p>
    <p style="margin-top: -15px; padding-left:22px;">Dalam rangka meningkatkan kinerja dan pelayanan ASN di lingkup Badan Pusat Statistik Provinsi Jambi, dibutuhkan sarana dan prasarana yang memadai demi kelancaran proses pelayanan internal dan eksternal. Maka dari itu, BPS Provinsi Jambi 
    melaksanakan pengadaan barang yang habis pakai antara lain, pembelian alat-alat tulis, perlengkapan perkantoran, barang cetak, ARK (Alat Rumah tangga Kantor), alat-alat kebersihan, dan biaya minum/makan kecil untuk rapat, biaya penerimaan tamu, 
    dll yang mana satuan biayanya berkaitan dengan jumlah pegawai. Saat ini, ketersediaan barang yang habis pakai tersebut sudah mulai menipis (hampir habis) sehingga perlu dilakukan pengadaan dengan pembelian secara langsung ke penyedia. </p>

    <h3>C. Maksud dan Tujuan</h3>
        <h4>a. Maksud</h4>
        <p style="margin-top: -20px; padding-left:31px;">Melaksanakan pengadaan barang yang habis pakai</p>
        <h4>b. Tujuan</h4>
        <p style="margin-top: -20px; padding-left:31px;">Tersedianya kebutuhan barang yang habis pakai secara memadai dan lengkap</p>

    <h3>D. Manfaat</h3>
    <p style="margin-top: -15px; padding-left:22px;">Manfaat yang dihasilkan dari pelaksanaan kegiatan ini adalah:<br>a. Bagi Pegawai, memperoleh barang yang habis pakai sesuai kebutuhan sehingga pekerjaan dan proses kegiatan di BPS Provinsi Jambi dapat berjalan secara optimal;<br>
    b. Bagi Satker BPS Provinsi Jambi, meningkatnya kinerja dan pelayanan ASN BPS Provinsi Jambi yang berpengaruh terhadap nama baik kantor.</p>

    <h3><b>E. Metode Pengadaan Barang/Jasa dan Ruang Lingkup</b></h3>
    <p style="margin-top: -15px; padding-left:22px;">Pembelian barang yang habis pakai berupa pembelian alat-alat tulis, perlengkapan perkantoran, barang cetak, ARK (Alat Rumah tangga Kantor), alat-alat kebersihan, dan biaya minum/makan kecil untuk rapat, biaya penerimaan tamu, dil yang
    dibutuhkan pegawai dilakukan dengan cara pembelian langsung ke pihak ketiga seperti Toko, Warung, Supermarket, dsb.</p>

    <h3>F. Tempat dan Waktu Pelaksanaan</h3>
    <p style="margin-top: -15px; padding-left:22px;">Pengadaan yang berkaitan dengan keperluan sehari perkantoran tersebut dilaksanakan diluar BPS Provinsi Jambi dalam hal ini pihak ketiga atau penyedia seperti Toko, Warung, Supermarket, dsb sepanjang tahun 2024 mulai dari Januari sampai 
        dengan Desember sesuai dengan kebutuhan.</p>

    <h3>G. Biaya</h3>
    <p style="margin-top: -15px; padding-left:22px;">Pembiayaan akan dibebankan dalam DIPA Satuan Kerja BPS Provinsi Jambi Nomor SP-DIPA
       054.01.2.428145/2024 yang meliputi:<br> 1. Belanja Keperluan Sehari-Hari Perkantoran</p>
    
    <p><b>Rencana Anggaran Biaya (RAB)</b><br><b><i>(Terlampir)</i></b></p>
    <p><b>Spesifikasi Teknis yang Diperlukan untuk Pengadaan</b><br><b><i>(Terlampir)</i></b></p>

    <p style=text-align:right>Jambi, <?= date('d F Y') ?></p>
    <table class="invisible-table">
        <tr>
            <td style=text-align:center>Menyetujui</td>
            <td style=text-align:center>Yang Mengajukan</td>
        </tr>
        <tr>
            <td style=text-align:center>PPK BPS Provinsi Jambi</td>
            <td style=text-align:center>Tim Ruta, Kearsipan dan Pengelolaan BMN</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="text-align:center; height: 25px;">Gafur, S.ST, M.Si.</td>
            <td style="text-align:center; height: 25px;">Sutino, S.E.</td>
        </tr>
    </table>
    <br>
    

    <div class="page-break"></div>

    <h1 style=text-align:right><b>Lampiran 1</b></h1>
    <h2 style="text-align:center"><b>RENCANA ANGGARAN BIAYA</b></h2>
    <table class="invisible-table">
        <tr>
            <td>Program</td>
            <td>:(WA) Program Dukungan Manajemen</td>
        </tr>
        <tr>
            <td>Kegiatan</td>
            <td>:(2886) Dukungan Manajemen dan Pelaksanaan<br>Tugas Teknis Lainnya BPS Provinsi</td>
        </tr>
        <tr>
            <td>Klasifikasi Rincian Output (KRO)</td>
            <td>:(EBA) Layanan Dukungan Manajemen<br>Internal</td>
        </tr>
        <tr>
            <td>Rincian Output (RO)</td>
            <td>:(994) Layanan Perkantoran</td>
        </tr>
        <tr>
            <td>Komponen</td>
            <td>:(002) Operasional dan Pemeliharaan Kantor</td>
        </tr>
        <tr>
            <td>Sub Komponen</td>
            <td>:(A) Tanpa Sub Komponen</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>AKUN</th>
                <th>RINCIAN</th>
                <th>VOLUME</th>
                <th>SATUAN</th>
                <th>HARGA SATUAN</th>
				<th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
                <tr>
                    <td>521811</td>
                    <td><b>Belanja Barang <br> Persediaan barang <br> konsumsi</b><br></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Alat Tulis Kantor</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <?php 
                        $total_harga = 0;
                        foreach ($pengajuan as $cs) {
                            $total_harga += $cs['total_harga'];
                        }
                        echo number_format($total_harga, 2);
                    ?></td>
                </tr>
        </tbody>
    </table> <br><br>
    <h1 style=text-align:left>Lampiran 2</h1>
    <h1>Spesifikasi Teknis</h1>
    <?php 
    $total_harga = 0;
    foreach ($pengajuan as $cs) {
        $total_harga += $cs['total_harga'];
    }
    ?>
    <h2>(Rp.<?= number_format($total_harga, 2) ?>)</h2>
    <h3>Daftar Barang dan Perlengkapan yang diperlukan:</h3>
    <table>
        <thead>
            <tr>
                <th style=text-align:center>No</th>
                <th style=text-align:center>Nama Barang</th>
                <th style=text-align:center>Satuan</th>
                <th style=text-align:center>Volume</th>
                <th style=text-align:center>Harga Satuan <br>(Rp)</th>
				<th style=text-align:center>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_pengajuan = 0;
			$total_harga = 0;
            foreach ($pengajuan as $cs):
                $total_pengajuan += $cs['jumlah'];
				$total_harga += $cs['total_harga'];
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $cs['nama_barang']; ?></td>
                    <td><?= $cs['satuan']; ?></td>
                    <td><?= $cs['jumlah']; ?></td>
                    <td><?= $cs['harga_pengajuan']; ?></td>
					<td><?= $cs['total_harga']; ?></td>
                </tr>
            <?php endforeach; ?>
			<tr>
</tr>
        </tbody>
    </table>
    
    <script>
        window.print();
    </script>
</body>
</html>
