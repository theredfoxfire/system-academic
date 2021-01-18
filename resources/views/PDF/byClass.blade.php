<!DOCTYPE html>
<html>
<head>
	<title>Nilai {{ $data['subject'] }} {{ ucfirst($data['class_room']) }} Tahun {{ $data['academic_year'] }}</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<style>
	.custom-header {
		border-bottom: 2px solid #bbbb
	}
</style>
<body>
	<h1 class="text-center custom-header">SMA 1 ZIMBABWE</h1>
	<h6>Mata Pelajaran: {{ $data['subject'] }}</h6>
	<div class="row">
		<div class="col-md-6">
			<h6>Kelas: {{ ucfirst($data['class_room']) }}</h6>
		</div>
		<div class="col-md-6">
			<h6 class="text-right">Tahun Ajaran: {{ $data['academic_year'] }}</h6>
		</div>
	</div>
	<table class='table table-bordered'>
		<col>
		<colgroup span="2"></colgroup>
		<colgroup span="2"></colgroup>
		<thead>
		<tr>
			<th rowspan="2">No</th>
			<th rowspan="2" style="width: 10%">No Induk</th>
			<th rowspan="2" style="width: 25%">Nama</th>
			<th rowspan="2">L/P</th>
			<th colspan="4" scope="colgroup">Nilai Tugas</th>
			<th rowspan="2">RNT</th>
			<th colspan="4" scope="colgroup">Nilai UH</th>
			<th rowspan="2">RNUH</th>
			<th rowspan="2">NA</th>
		</tr>
		<tr>
			<th scope="col">1</th>
			<th scope="col">2</th>
			<th scope="col">3</th>
			<th scope="col">4</th>
			<th scope="col">1</th>
			<th scope="col">2</th>
			<th scope="col">3</th>
			<th scope="col">4</th>
		</tr>
		</thead>
	<tbody>
		@php
				$averageNA = 0;
		@endphp
		@foreach ($data['students'] as $key => $student)
		<tr>
				<td>{{ ++$key }}</td>
				<td>{{ $student['id'] }}</td>
				<td>{{ $student['name'] }}</td>
				<td>L</td>
				{{-- Ulangan Harian --}}
				@foreach ($student['TH'] as $th)
						<td>{{ $th['point'] }}</td>
				@endforeach
				@for($i = 0 ;$i < (4-count($student['TH'])); $i++)
					<td></td>
				@endfor
				<td>{{ $student['th_average'] }}</td>

				{{-- Ujian Harian --}}
				@foreach ($student['UH'] as $uh)
						<td>{{ $uh['point'] }}</td>
				@endforeach
				@for($i = 0 ;$i < (4-count($student['UH'])); $i++)
					<td></td>
				@endfor
				<td>{{ $student['uh_average'] }}</td>

				{{-- Nilai Akhir --}}
				<td>{{ $student['NA'] }}</td>
				@php
					$averageNA +=$student['NA'];	
				@endphp
		</tr>
		@endforeach
		<tr>
			<td colspan="14">Rata Rata Nilai Kelas</td>
			<td>{{ round($averageNA/count($data['students']), 0) }}</td>
		</tr>
	</tbody>
	</table>
</body>
</html>