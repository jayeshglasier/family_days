<html>
<head>
	<style>
		@page { size 8.5in 11in; margin: 2cm }
		div.page { page-break-after: always }
	</style>
</head>
<body>
	<div class="page">
		<h3 style="text-align: center;"> <?php echo (isset($companyname) && $companyname != '' ? $companyname : 'ChoreUp - All Members Record') ?></h3>
		<hr>
		<table style="width:100%;" border="0.4">
			<tbody>
				<tr style="border: 0.4px solid;text-align:center;font-size: 15px;" class="page">
					<th width="10%">Sr. No</th>
					<th width="20%">Family Name</th>
					<th with="20%">Username</th>
					<th with="20%">Fullname</th>
					<th with="20%">Email Id</th>
					<th with="10%">Phone</th>
				</tr>
				<?php $i=0 ; ?>@foreach($datarecords as $data)<?php $i++; ?>
				<tr style="font-size: 12px;text-align:center;">
					<td>{{ $i }}</td>
					<td>{{ $data->use_family_name }}</td>
	                <td>{{ $data->use_username }}</td>
	                <td>{{ $data->use_full_name }}</td>
	                <td>{{ $data->email }}</td>
	                <td>{{ $data->use_phone_no }}</td>
				</tr>@endforeach
			</tbody>
		</table>
	</div>
</body>
</html>