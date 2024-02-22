@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-3 mb-5">
        <div class="card-header">
            <h5>Welcome To Dashboard</h5>
        </div>
        <div class="card-body">
            <p>Dashboard Coming Soon!</p>
        </div>
    </div>
</div>
@endsection
@push('footerScripts')
<script src="{{url('js/APIDataTable.js')}}"></script>
{{-- <script src="{{url('js/chart-analytics.js')}}"></script> --}}
<script src="{{url('js/jqvmap.js')}}"></script>
<script src="{{url('js/example-chart.js')}}"></script>
<script src="{{url('js/gd-default.js')}}"></script>
<script src="{{url('js/chart-ecommerce.js')}}"></script>
<script>
    $(document).ready(function() {
        // Check if there is a stored notification
        var toastrNotification = sessionStorage.getItem('toastrNotification');

        if (toastrNotification) {
            // Parse the JSON data
            var notificationData = JSON.parse(toastrNotification);

            // Display Toastr notification
            toastr[notificationData.type](notificationData.message);

            // Clear the stored notification
            sessionStorage.removeItem('toastrNotification');
        }
    });
</script>
@endpush