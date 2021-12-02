	<script src="{{ asset('public/js/jquery.min.js') }}"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="{{ asset('public/js/bootstrap.min.js') }}"></script>
	<!-- Metis Menu Plugin JavaScript -->
	<script src="{{ asset('public/js/metisMenu.min.js') }}"></script>
	<!-- DataTables JavaScript -->
	<script src="{{ asset('public/js/dataTables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('public/js/dataTables/dataTables.bootstrap.min.js') }}"></script>
	<!-- Custom Theme JavaScript -->
	<script src="{{ asset('public/js/startmin.js') }}"></script>
	<!-- Page-Level Demo Scripts - Tables - Use for reference -->
	<script>
	    $(document).ready(function() {
	        $('#dataTables-example').DataTable({
                responsive: true
	        });
	    });
	</script>
	<script type="text/javascript">
		$(window).load(function() {
		setTimeout(function(){ $('.loader').fadeOut('fast'); }, 50);
		})
	</script>

	<script>
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();   
		});
	</script>
	<script type="text/javascript"> 
      $(document).ready( function() {
        $('.flash-message').delay(2000).fadeOut();
      });
    </script>