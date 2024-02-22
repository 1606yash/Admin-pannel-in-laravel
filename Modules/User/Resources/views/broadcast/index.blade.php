@extends('layouts.app')

@section('content')
@php
    $userPermission = \Session::get('userPermission');
@endphp
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Broadcast</h3>
                 @php 
                    if(count($broadcasts) > 1) 
                        $count = 'broadcasts';
                    else
                        $count = 'broadcast';                       
                @endphp
                <p>You have total {{ count($broadcasts) }} {{ $count }}.</p>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            @if(isset($userPermission['broadcast']) && ($userPermission['broadcast']['edit_all'] || $userPermission['broadcast']['edit_own']))
                            <li>
                                <a href="#" data-target="addRole" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Broadcast</span></a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block table-compact">
        <div class="nk-tb-list is-separate mb-3">
            <table class="broadcast-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <!-- <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="uid">
                            <label class="custom-control-label" for="uid"></label>
                        </div>
                    </th> -->
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Message</span></th>
                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Receivers</span></th>
                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Type</span></th>
                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Category</span></th>
                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Date/Time</span></th>
                    <th class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right" nowrap="true">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            </table>
            
        </div><!-- .nk-tb-list -->
       
        <div id="modal" class="nk-add-product toggle-slide toggle-slide-right" data-content="addRole" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar>
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title">Add Broadcast</h5>
                    <div class="nk-block-des">
                        <p>Add broadcast message for the user.</p>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <div class="nk-block">
                <form role="form" id="addBroadcastForm" method="post" action="{{ url('broadcast/add') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            
                            <div class="mb-2"><x-inputs.radio for="allBuyers" label="All Buyers" value="1" size="md" name="br_type" /></div>

                            <div class="mb-2"><x-inputs.radio for="specificBuyer" label="Specific Buyer" value="2" size="md" name="br_type"/></div>
                            
                            <div class="mb-2"><x-inputs.radio for="buyerCategory" label=" Buyer Category" value="3" size="md" name="br_type"/></div>
                        </div>
                        
                        <div class="col-12" id="specBuyer" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Buyer<span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <select id='callbacks' multiple='multiple' data-parsley-errors-container=".parsley-container-buyer" name="buyer[]" placeholder="Select Buyer">
                                        @forelse($buyers as $key  => $buyer)
                                        <option value="{{ $buyer->id }}">{{ $buyer->name.' '.$buyer->last_name }}</option>
                                        @empty
                                            <option></option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="parsley-container-buyer"></div>
                            </div>
                        </div>

                        <div class="col-12" id="retailCat" style="display: none;">
                            <x-inputs.select  size="sm" for="buyerCat" label="Buyer Category" icon="label" placeholder="Buyer Category" name="buyerCat" data-placeholder="Select Buyer Category">
                                   <option></option>
                                    @forelse($buyerCategories as $key  => $buyerCat)
                                    <option value="{{ $buyerCat->id }}">{{ $buyerCat->retailer_catagory }}</option>
                                    @empty
                                        <option></option>
                                    @endforelse
                                </x-inputs.select>

                        </div>

                        <div class="col-12">
                             <x-inputs.textarea minlength="3" maxlength="500" for="Message" size="sm" label="Message" required="true" name="message" required="true"/>
                        </div>
                        <div class="col-12 text-right">
                            <a href="javascript:resetValues();" class="btn btn-outline-light">Cancel</a>
                            <button class="btn btn-primary" type="submit" name="submit"><span>Submit</span></button>
                        </div>
                    </div>
                </form>
            </div><!-- .nk-block -->
        </div>
    </div><!-- .nk-block -->

@endsection
@push('footerScripts')
<script src="{{url('js/jquery.multi-select.js')}}"></script>
<script src="{{url('js/jquery.quicksearch.js')}}"></script>
<script type="text/javascript">
// Multi Select
$('#callbacks').multiSelect({
  selectableHeader: "<input type='text' class='form-control mb-1 search-input' autocomplete='off' placeholder='Search Buyer'>",
  selectionHeader: "<input type='text' class='form-control mb-1 search-input' autocomplete='off' placeholder='Search Buyer'>",
  afterInit: function(ms){
    var that = this,
        $selectableSearch = that.$selectableUl.prev(),
        $selectionSearch = that.$selectionUl.prev(),
        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
    .on('keydown', function(e){
      if (e.which === 40){
        that.$selectableUl.focus();
        return false;
      }
    });

    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
    .on('keydown', function(e){
      if (e.which == 40){
        that.$selectionUl.focus();
        return false;
      }
    });
  },
  afterSelect: function(){
    this.qs1.cache();
    this.qs2.cache();
  },
  afterDeselect: function(){
    this.qs1.cache();
    this.qs2.cache();
  }
});

function resetValues(){

    $('body').removeClass('toggle-shown');
    $('#modal').removeClass('content-active');
    $('#addBroadcastForm')[0].reset();
    $('#addBroadcastForm').parsley().reset();
    NioApp.Toggle.collapseDrawer('addRole')
}

 var root_url = "<?php echo Request::root(); ?>";
    $('.radio-btn').click(function(){
        var type = $(this).val();
        console.log('val-' + type);
        if(type == 2){
            $('#buyer').prop('disabled', false);
            $('#buyerCat').prop('disabled', 'disabled');
            $("#retailCat").hide();
            $("#specBuyer").show();
            $("select#callbacks").attr("required", "true");
        }else if(type == 3){
            $('#buyer').prop('disabled', 'disabled');
            $('#buyerCat').prop('disabled', false);
            $("#retailCat").show();
            $("#specBuyer").hide();
            $("select#callbacks").removeAttr("required", "true");
        }else{
            $('#buyer').prop('disabled', 'disabled');
            $('#buyerCat').prop('disabled', 'disabled');
            $("#retailCat").hide();
            $("#specBuyer").hide();
            $("select#callbacks").removeAttr("required", "true");
        }
        return true;
        
    });


</script>
<script type="text/javascript">
    $(function() {
        NioApp.DataTable('.broadcast-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('broadcast') }}",
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    /*render: function(data, type, row, meta) {
                        return '<td class="nk-tb-col nk-tb-col-check"><div class="custom-control custom-control-sm custom-checkbox notext"><input type="checkbox" class="custom-control-input" id="puid1"><label class="custom-control-label" for="puid1"></label></div></td>'
                    }*/
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'message',
                    name: 'message'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'receivers',
                    name: 'receivers'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'type',
                    name: 'type'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'retailer_catagory',
                    name: 'retailer_catagory'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    "class": "nk-tb-col tb-col-lg text-right",
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });

    $('.broadcast-init').on('click','.eg-swal-av3', function (e) {
        var root_url = "<?php echo Request::root(); ?>";
        var id = $(this).attr('data-id');
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
          if (result.value) {
            console.log('id-' + id);
            $.ajax({
                url: root_url + '/broadcast/delete',
                data: {
                    'id': id
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
                        var table = $('.broadcast-init').DataTable();
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: data.msg,
                              //footer: '<a href>Why do I have this issue?</a>'
                            })
                    }
                }
            });
            
          }
        });
        e.preventDefault();
      });
</script>
@endpush