@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Notifications</h3>
            <p>You have total <span id="count">{{ $notificationCount ?? 0 }}</span> notifications.</p>
        </div><!-- .nk-block-head-content -->
        <!-- <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li class="nk-block-tools-opt"><a href="#" class="btn btn-primary" id="mark_all_as_read_1"><em class="icon ni ni-check-round"></em><span>Mark all as read</span></a></li>
                    </ul>
                </div>
            </div>
        </div> -->
        <!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block table-compact">
    <div class="nk-notification nk-tb-list is-separate mt-3">
        <table id="notifications_table" class="notifications_table nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Title</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Description</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Actions</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <!-- <div class="row g-3">
        <div class="col-lg-7 offset-lg-5">
            <div class="form-group mt-2">
                <button type="button" class="btn btn-primary" id="load-more"><em class="icon ni ni-reload"></em><span>Load More</span></button>
            </div>
        </div>
    </div> -->
</div><!-- .nk-block -->
@endsection

@push('footerScripts')
<script src="{{url('js/all-notifications.js')}}"></script>
<script src="{{ url('js/tableFlow.js') }}"></script>
<script type="text/javascript">
    var root_url = "<?php echo Request::root(); ?>";
    $(document).ready(function() {
        getNotificationList();
    });

    function getNotificationList() {
        $('.notifications_table').DataTable().destroy();

        // Initialize DataTable
        var dataTable = new CustomDataTable({
            tableElem: '.notifications_table',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: root_url + "/get-notification-list",

                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'notification_title',
                        name: 'notification_title',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'notification_description',
                        name: 'notification_description',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [1, 'desc']
                ],
            },
        });
    }
</script>

@endpush