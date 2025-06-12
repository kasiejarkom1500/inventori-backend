</div>


<footer class="sticky-footer bg-white">
	<div class="container my-auto">
		<div class="copyright text-center my-auto">
			<span>&copy; Inventory-Adra </span>
		</div>
	</div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
	<i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->


<!-- Bootstrap core JavaScript-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="<?= base_url(); ?>assets/vendor/jquery/jquery.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url(); ?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url(); ?>assets/js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="<?= base_url(); ?>assets/vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="<?= base_url(); ?>assets/js/demo/chart-area-demo.js"></script>
<script src="<?= base_url(); ?>assets/js/demo/chart-pie-demo.js"></script>
<!-- data tabel -->
<script src="<?= base_url(); ?>assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="<?= base_url(); ?>assets/js/demo/datatables-demo.js"></script>

<script src="<?= base_url(); ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?= base_url(); ?>assets/js/myscript.js"></script>
<script src="<?= base_url(); ?>assets/vendor/select2/js/select2.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>




<script>
	$(document).ready(function() {
		// Initialize Select2
		$('#id_barang').select2({
			placeholder: 'Pilih barang'
		}).on('select2:select', function(e) {
			var selectedData = e.params.data;
			var id_barang = selectedData.id;
			var kategori = $('#id_barang option:selected').data('kategori');
			var satuan = $('#id_barang option:selected').data('satuan');
			var stok = $('#id_barang option:selected').data('stok');
			var harga = $('#id_barang option:selected').data('harga');

			var id_harga_pengajuan = $('#id_barang option:selected').data('id_harga_pengajuan');
			$('#kategori').val(kategori)
			$('#satuan').val(satuan)
			$('#stok').val(stok)
			$('#harga').val(harga)
			// Lakukan sesuatu dengan data yang dipilih

		});
	});
</script>

<script>
	function applyHargaPengajuan() {
		var harga_pengajuan_header = $('#harga_pengajuan_header').val();
		$('tbody tr').each(function() {
			var id_barang = $(this).find('.id_barang').text();
			var harga_satuan = parseFloat($(this).find('td:eq(6)').text());
			var harga_pengajuan = harga_pengajuan_header * harga_satuan + harga_satuan;
			$('#harga_pengajuan_' + id_barang).text(harga_pengajuan);

			// Send AJAX request to update harga_pengajuan in the database
			$.ajax({
				url: '<?= base_url('barang/update_harga_pengajuan'); ?>',
				method: 'POST',
				data: {
					id_barang: id_barang,
					harga_pengajuan: harga_pengajuan
				},
				success: function(response) {
					console.log('Harga pengajuan updated for id_barang ' + id_barang);
				},
				error: function() {
					console.log('Failed to update harga_pengajuan for id_barang ' + id_barang);
				}
			});
		});
	}
</script>

<script>
	$(document).ready(function() {
		$('#addToTable').click(function(event) {
			event.preventDefault(); // Mencegah form submit secara default

			var id_barang = $('#id_barang').val();
			var id_barang_text = $('#id_barang option:selected').text();
			var kategori = $('#kategori').val();
			var satuan = $('#satuan').val();
			var stok = $('#stok').val();
			var jumlah = $('input[name="jumlah"]').val();
			// var ruangan = $('#id_ruangan').val();

			// Validasi stok
			if (parseInt(jumlah) > parseInt(stok)) {
				alert('Jumlah yang dimasukkan melebihi stok yang tersedia!');
				return;
			}

			// Validasi barang yang sama
			var isDuplicate = false;
			$('#dataTable tbody tr').each(function() {
				var existingBarang = $(this).find('td:eq(0)').text();
				if (existingBarang === id_barang_text) {
					isDuplicate = true;
					return false; // Berhenti dari loop each
				}
			});

			if (isDuplicate) {
				alert('Barang yang sama sudah dipilih!');
				return;
			}

			// Hapus pesan "No data available in table" jika ada
			$('#noData').remove();

			var newRow = '<tr data-id-barang="' + id_barang + '">' +
				'<td>' + id_barang_text + '</td>' +
				'<td>' + kategori + '</td>' +
				'<td>' + satuan + '</td>' +
				'<td>' + stok + '</td>' +
				'<td>' + jumlah + '</td>' +
				'<td><button type="button" class="btn btn-danger btn-delete">Hapus</button></td>' +
				'</tr>';

			$('#dataTable tbody').append(newRow);

			// Kosongkan field input setelah menambahkan data ke tabel
			$('#jumlah').val('');
		});

		// Event handler untuk menghapus baris
		$('#dataTable').on('click', '.btn-delete', function() {
			$(this).closest('tr').remove();
			// Tampilkan kembali pesan "No data available in table" jika tidak ada data
			if ($('#dataTable tbody tr').length === 0) {
				var noDataRow = '<tr id="noData">' +
					'<td colspan="6">No data available in table</td>' +
					'</tr>';
				$('#dataTable tbody').append(noDataRow);
			}
		});

		$('#submitAll').click(function() {
			var data = [];
			var id_user = $('#id_user').val();
			var ruangan = $('#id_ruangan_permintaan').val();
			var totalSemua = 0; // Inisialisasi total semua harga
			// var id_ruangan = $('#id_ruangan').val();
			$('#dataTable tbody tr').each(function() {
				var row = $(this);
				var harga = parseFloat(row.find('td:eq(3)').text());
				var jumlah = parseFloat(row.find('td:eq(4)').text());
				var total_harga = harga * jumlah;
				total_harga += total_harga;

				var rowData = {
					id_ruangan: ruangan,
					id_user: id_user,
					id_barang: row.data('id-barang'),
					jumlah: jumlah
				};
				data.push(rowData);
			});

			$.ajax({
				url: '<?php echo site_url("permintaan_barang/simpan"); ?>',
				method: 'POST',
				data: {
					data: data
				},
				success: function(response) {
					console.log(response);
					alert('Data berhasil disimpan!');
					$('#dataTable tbody').empty(); // Hapus data dari tabel setelah disimpan

					// Tampilkan kembali pesan "No data available in table" jika tidak ada data
					var noDataRow = '<tr id="noData">' +
						'<td colspan="6">No data available in table</td>' +
						'</tr>';
					$('#dataTable tbody').append(noDataRow);
				}
			});
		});
	});
</script>

<script>
	$(document).ready(function() {
		$('.btn-save').click(function() {
			var id_permintaan = $(this).data('id');
			var status = $('input[name="status_' + id_permintaan + '"]:checked').val();
			var jumlah = $('input[name="jumlah_' + id_permintaan + '"]').val();


			if (!status) {
				alert('Pilih status terlebih dahulu!');
				return;
			}

			$.ajax({
				url: '<?= base_url('permintaan_barang/update_status'); ?>',
				method: 'POST',
				data: {
					id_permintaan: id_permintaan,
					status: status,
					jumlah:jumlah
				},
				success: function(response) {
					alert('Status berhasil diperbarui!');
					location.reload();
				},
				error: function(xhr, status, error) {
					alert('Terjadi kesalahan saat memperbarui status: ' + error);
				}
			});
		});
	});
</script>

<script>
	$(document).ready(function() {
		$('#addToTable1').click(function(event) {
			event.preventDefault(); // Mencegah form submit secara default

			var id_barang = $('#id_barang').val();
			var id_barang_text = $('#id_barang option:selected').text();
			var kategori = $('#kategori').val();
			var satuan = $('#satuan').val();
			var stok = $('#stok').val();
			var id_harga_pengajuan = $('#harga').val();
			var jumlah = $('input[name="jumlah"]').val();

			var isDuplicate = false;
			$('#dataTable1 tbody tr').each(function() {
				var existingBarang = $(this).find('td:eq(0)').text();
				if (existingBarang === id_barang_text) {
					isDuplicate = true;
					return false; // Berhenti dari loop each
				}
			});

			if (isDuplicate) {
				alert('Barang yang sama sudah dipilih!');
				return;
			}

			// Hitung total harga
			var total_harga = parseFloat(id_harga_pengajuan) * parseFloat(jumlah);

			// Hapus pesan "No data available in table" jika ada
			$('#noData').remove();

			var newRow = '<tr data-id-barang="' + id_barang + '">' +
				'<td>' + id_barang_text + '</td>' +
				'<td>' + kategori + '</td>' +
				'<td>' + satuan + '</td>' +
				'<td>' + id_harga_pengajuan + '</td>' +
				'<td>' + jumlah + '</td>' +
				'<td>' + total_harga + '</td>' +
				'<td><button type="button" class="btn btn-danger btn-delete">Hapus</button></td>' +
				'</tr>';

			$('#dataTable1 tbody').append(newRow);

			// Kosongkan field input setelah menambahkan data ke tabel
			$('#jumlah').val('');
		});

		// Event handler untuk menghapus baris
		$('#dataTable1').on('click', '.btn-delete', function() {
			$(this).closest('tr').remove();
			// Tampilkan kembali pesan "No data available in table" jika tidak ada data
			if ($('#dataTable1 tbody tr').length === 0) {
				var noDataRow = '<tr id="noData">' +
					'<td colspan="7">No data available in table</td>' +
					'</tr>';
				$('#dataTable1 tbody').append(noDataRow);
			}
		});

		$('#submitAll1').click(function() {
			var data = [];
			var id_user = $('#id_user').val();
			var ruangan = $('#id_ruangan').val();
			var totalSemua = 0;

			$('#dataTable1 tbody tr').each(function() {
				var row = $(this);
				var harga = parseFloat(row.find('td:eq(3)').text());
				var jumlah = parseFloat(row.find('td:eq(4)').text());
				var total_harga = harga * jumlah;
				totalSemua += total_harga;

				var rowData = {
					id_ruangan: ruangan,
					id_user: id_user,
					id_barang: row.data('id-barang'),
					jumlah: jumlah,
					total_harga: total_harga
				};

				$.ajax({
					url: '<?php echo site_url("pengajuan_barang/simpan"); ?>',
					method: 'POST',
					data: {
						data: rowData
					}, // Mengirim total semua harga ke server
					success: function(response) {
						console.log(response)
						alert('Data berhasil disimpan!');
						$('#dataTable1 tbody').empty(); // Hapus data dari tabel setelah disimpan

						// Tampilkan kembali pesan "No data available in table" jika tidak ada data
						var noDataRow = '<tr id="noData">' +
							'<td colspan="7">No data available in table</td>' +
							'</tr>';
						$('#dataTable1 tbody').append(noDataRow);
					}
				});
			});



		});
	});


	$(document).ready(function() {
		$('#dataTable').DataTable();
	});
</script>


<script>
	$(document).ready(function() {
		$('.btn-save1').click(function() {
			var id_pengajuan = $(this).data('id');
			var status = $('input[name="status_' + id_pengajuan + '"]:checked').val();

			if (!status) {
				alert('Pilih status terlebih dahulu!');
				return;
			}

			$.ajax({
				url: '<?= base_url('pengajuan_barang/update_status'); ?>',
				method: 'POST',
				data: {
					id_pengajuan: id_pengajuan,
					status: status
				},
				success: function(response) {
					alert('Status berhasil diperbarui!');
					location.reload();
				},
				error: function(xhr, status, error) {
					alert('Terjadi kesalahan saat memperbarui status: ' + error);
				}
			});
		});
	});
</script>


</body>

</html>
