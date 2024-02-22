@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Import</h3>
                <p>You can import your file.</p>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href="{{ url('sample-imports/sample-city.xlsx') }}" icon="download" class="btn btn-primary" download>Download Template</a>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block table-compact">
        <form method="post" action="{{ url('ecommerce/brands/import') }}" enctype="multipart/form-data">
            @csrf
            <div class="nk-block">
                <div class="card card-bordered sp-plan">
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <div class="sp-plan-action card-inner">
                                <div class="icon">
                                    <em class="icon ni ni-download fs-36px o-5"></em>
                                    <h5 class="o-5">Import Brands</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="sp-plan-info card-inner">
                                <div class="row g-3 align-center">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="importFile" id="importFile"  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"> 
                                                    <label class="custom-file-label" for="importFile">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nk-block">
                <div class="row">
                    <div class="col-md-12">
                        <div class="sp-plan-info pt-0 pb-0 card-inner">  
                                <div class="row">
                                    <div class="col-lg-7 text-right offset-lg-5">
                                        <div class="form-group">
                                            <a href="javascript:history.back()" class="btn btn-outline-light">Cancel</a>
                                            <x-button type="submit" class="btn btn-primary">Submit</x-button>
                                        </div>
                                    </div>
                                </div>
                        </div><!-- .sp-plan-info -->
                    </div><!-- .col -->
                </div><!-- .row -->
            </div>
        </form>
    </div>
@endsection
