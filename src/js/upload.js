function uploadBuku() {
  var data = new FormData();
  var cover = document.getElementById('upload-cover').files[0];
  var buku = document.getElementById('upload-buku').files[0];
  data.append('cover', cover);
  data.append('buku', buku);
  data.append('Judul', $('#Judul').val());
  data.append('Penulis', $('#Penulis').val());
  data.append('Penerbit', $('#Penerbit').val());
  data.append('Halaman', $('#Halaman').val());
  data.append('Kategori', $('#Kategori').val());
  data.append('Deskripsi', $('#Deskripsi').val());
  data.append('csrftoken', $('#csrftoken').val());
  $.ajax({
    url: host+'/books/upload',
    method: 'POST',
    contentType: false,
    processData: false,
    data: data,
    beforeSend: function(){
      $('#upload-loading').show();
    }
  })
  .done(function(res) {
    formUploadReset();
    res = JSON.parse(res)
    console.log(res);
    $('#feedback-msg').html(res.msg);
  })
  .fail(function(err, status, xhr) {
    formUploadReset();
    err = JSON.parse(err);
    console.log(err);
    $('#feedback-msg').html(err.msg);
  });
}

function formUploadReset() {
  $('.book-upload').val('');
  $('#cover-preview').attr('src', host+'/assets/img/uploadplaceholder.png');
  $('#upload-loading').hide();
  $('#Halaman').val('0');
  $('#Kategori').formSelect();
}
