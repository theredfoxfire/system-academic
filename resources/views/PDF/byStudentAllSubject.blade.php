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
		<thead>
		<tr>
			<th >No</th>
			<th style="width: 25%">Nama</th>
				@foreach ($data['examType'] as $type)
						<th>{{ trimSubjectName($type['name']) }}</th>
				@endforeach
		</tr>
		</thead>
	<tbody>
		@foreach ($data['subjects'] as $key => $subject)
		<tr>
				<td>{{ ++$key }}</td>
				<td>{{ $subject['name'] }}</td>
				@foreach ($data['examType'] as $type)
						<td style="padding: 0px;margin: 0px;">
							
								@if(count($subject['exams']) > 0)
									<table>
										<tr>
									@foreach ($subject['exams'][$type['id'].$subject['serial']] as $key => $point)
										<td>{{ $point['point'] }}</td>
									@endforeach
										</tr>
									</table>
								@else
									-
								@endif
								
						</td>
				@endforeach
		</tr>
		@endforeach
	</tbody>
	</table>
</body>
</html>