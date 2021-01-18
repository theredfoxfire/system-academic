<!DOCTYPE html>
<html>
<head>
	<title>Nilai Siswa {{ ucfirst($data[0]['class']) }}</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<style>
	.custom-header {
		border-bottom: 2px solid #bbbb
	}
  .wrapper-page {
    page-break-after: always;
  }

  .wrapper-page:last-child {
      page-break-after: avoid;
  }
</style>
<body>
  @foreach ($data as $student)
    <div class="wrapper-page">
      <h1 class="text-center custom-header">SMA 1 ZIMBABWE</h1>
      <div class="row">
        <div class="col-md-6">
          <h6>Nama Siswa: {{ $student['name'] }}</h6>
        </div>
        <div class="col-md-6">
          <h6 class="text-right">Nomor Induk: {{ $student['serial'] }}</h6>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <h6>Kelas: {{ ucfirst($student['class']) }}</h6>
        </div>
        <div class="col-md-6">
          <h6 class="text-right">No Absen: {{ $student['attendance'] }}</h6>
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
          @foreach ($student['score'] as $key => $item)
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
  @endforeach
</body>
</html>