@if(isset($datarecords))
	<div style="float: left;">
		Showing {{ ($datarecords->currentpage()-1)*$datarecords->perpage()+1 }} to {{ $datarecords->currentpage()*$datarecords->perpage() }}
	of  {{ $datarecords->total() }} entries
	</div>
	<div style="float: right;margin-top: -20px;">
		{{ $datarecords->appends(Request::except('page'))->links() }}
	</div>
@endif