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
	<h1 class="text-center custom-header">SMA 1 ZIMBABWE</h1>
	<div class="row">
		<div class="col-md-6">
			<h6>Name: {{ $data['name'] }}</h6>
		</div>
		<div class="col-md-6">
			<h6 class="text-right">Kelas: {{ ucfirst($data['class']) }}</h6>
		</div>
	</div>
	<table class='table table-bordered'>
		<col>
		<colgroup span="2"></colgroup>
		<colgroup span="2"></colgroup>
		<thead>
		<tr>
			<th rowspan="2">No</th>
			<th rowspan="2" style="width: 10%">Serial</th>
			<th rowspan="2" style="width: 25%">Nama</th>
			{{-- <th rowspan="2">L/P</th> --}}
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
				$totalNA = 0;
				$totalCount = 0;
		@endphp
		@foreach ($data['subjects'] as $key => $subject)
		<tr>
				<td>{{ ++$key }}</td>
				<td>{{ $subject['serial'] }}</td>
				<td>{{ $subject['name'] }}</td>
				{{-- <td>L</td> --}}
				{{-- Tugas Harian --}}
				@foreach ($subject['exams']['TH'] as $TH)
					<td>{{ $TH['point'] }}</td>
					@php
							$totalNA += $TH['point'];
							$totalCount += 1;
					@endphp
				@endforeach
				@for($i = 0 ;$i < (4-count($subject['exams']['TH'])); $i++)
					<td></td>
				@endfor
				<td>{{ $subject['exams']['averageTH'] }}</td>

				{{-- Ulangan Harian --}}
				@foreach ($subject['exams']['UH'] as $UH)
					<td>{{ $UH['point'] }}</td>
					@php
							$totalNA += $UH['point'];
							$totalCount += 1;
					@endphp
				@endforeach
				@for($i = 0 ;$i < (4-count($subject['exams']['UH'])); $i++)
					<td></td>
				@endfor
				<td>{{ $subject['exams']['averageUH'] }}</td>
				<td>{{ $subject['exams']['NA'] }}</td>
		</tr>
		@endforeach
		<tr>
			<td colspan="13">Rata Rata Nilai Kelas</td>
			<td>{{ round(($totalNA/$totalCount), 0) }}</td>
		</tr>
	</tbody>
	</table>
</body>
</html>