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
		Kelas: {{ $data['class_room'] }}<br>
		Tahun Ajaran: {{ $data['academic_year'] }}<br>
		Mata Pelajaran: {{ $data['subject'] }}
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

	</div>

</body>
</html>