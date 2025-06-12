const flashData = $('.flash-data').data('flashdata');

if(flashData){
    swal({
        title: 'Data Mobil',
        Text: 'Berhasil' + flashData,
        type: 'success'
    })
}