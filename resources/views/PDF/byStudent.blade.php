<!DOCTYPE html>
<html>
<head>
	<title>Nilai {{ $data['name'] }} {{ ucfirst($data['class']) }}</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<style>
	.custom-header {
		border-bottom: 2px solid #bbbb
	}
</style>
<body>
  <h1 class="text-center custom-header">{{ $schoolInfo['name'] }}</h1>
  <div class="row">
    <div class="col-md-6">
      <h6>Nama Siswa: {{ $data['name'] }}</h6>
    </div>
    <div class="col-md-6">
      <h6 class="text-right">Nomor Induk: {{ $data['serial'] }}</h6>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <h6>Kelas: {{ ucfirst($data['class']) }}</h6>
    </div>
    <div class="col-md-6">
      <h6 class="text-right">No Absen: {{ $data['attendance'] }}</h6>
    </div>
  </div>
  <h6>Wali Kelas: {{ $guardian }}
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
          <td>{{ $item['passingPoint'] }}</td>
          <td>{{ round($item['final_score'], 0) }}</td>
          <td>{{ $item['in_words'] }}</td>
          <td>{{ $item['description'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

</body>
</html>