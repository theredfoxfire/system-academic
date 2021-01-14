<!DOCTYPE html>
<html>
<head>
	<title>Membuat Laporan PDF Dengan DOMPDF Laravel</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<style>
	@page {
		margin:0px
	}
</style>
	<div class="container">
    <br/>
    Nama Siswa: {{ $data['name'] }}<br/>
    Nomor Induk: {{ $data['serial'] }}<br/>
    Kelas: {{ $data['class'] }}<br/>
    No Absen: {{ $data['attendance'] }}<br/>
    <table class='table table-bordered'>
      <col>
      <colgroup span="2"></colgroup>
      <colgroup span="2"></colgroup>
      <thead>
        <tr>
          <th rowspan="2">No</th>
          <th rowspan="2">Mata Pelajaran</th>
          <th rowspan="2">KKM</th>
          <th colspan="2" scope="colgroup">Nilai</th>
          <th rowspan="2">Deskripsi</th>
        </tr>
        <tr>
          <th scope="col">Angka</th>
          <th scope="col">Huruf</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($data['score'] as $key => $item)
          <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $item['subject'] }}</td>
            <td>75</td>
            <td>{{ round($item['final_score'], 0) }}</td>
            <td>{{ $item['in_words'] }}</td>
            <td>{{ $item['description'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

	</div>

</body>
</html>