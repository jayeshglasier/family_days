<html>
<head>
	<style>
		@page { size 8.5in 11in; margin: 2cm }
		div.page { page-break-after: always }
	</style>
</head>
<body>
	<div class="page">
		<h3 style="text-align: center;"> <?php echo (isset($companyname) && $companyname != '' ? $companyname : 'Family Days - Preset Chores Record') ?></h3>
		<hr>
		<table style="width:100%;" border="0.4">
			<tbody>
				<tr style="border: 0.4px solid;text-align:center;font-size: 15px;" class="page">
					<th width="10%">Sr. No</th>
					<th width="75%">Preset Chores</th>
					<th with="15%">Created Date</th>
				</tr>
				<?php $i=0 ; ?>@foreach($datarecords as $data)<?php $i++; ?>
				<tr style="font-size: 14px;text-align:center;">
					<td>{{ $i }}</td>
					<td>{{ $data->pre_title }}</td>
	                <td>{{ date('M-d-Y',strtotime($data->pre_createat)) }}</td>
				</tr>@endforeach
			</tbody>
		</table>
	</div>
</body>
</html>