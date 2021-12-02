 <div class="col-lg-6">
    @if(session('success'))
    </br>
    <div class="flash-message" style="padding-top: 5px;">
        <div class="alert alert-info" style="text-align: center;">
            <span class="success-message"><big>{{ session('success') }}</big></span>
        </div>
    </div>
    @endif @if (session('error'))
    </br>
    <div class="flash-message" style="padding-top: 5px;">
        <div class="alert alert-danger" style="text-align: center;">
            <span class="error-message"><big>{{ session('error') }}</big></span>
        </div>
    </div>
    @endif
</div>