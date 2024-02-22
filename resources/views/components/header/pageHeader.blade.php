@props([
    'title' => '',
    'subTitle' => '',
])
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">{{ $title }}</h3>
            <p>{{ $subTitle }}</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <!-- <li>
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-right">
                                        <em class="icon ni ni-search"></em>
                                    </div>
                                    <input type="text" class="form-control" id="default-04" placeholder="Quick search by id">
                                </div>
                            </li> -->
                        <li class="nk-block-tools-opt">
                            <a href="#" id="showall" data-toggle="modal" data-target="#modalFilterProduct" class="btn btn-outline-light d-none d-md-inline-flex"><em class="icon ni ni-filter-alt"></em><span>Filter</span></a>
                        </li>
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addProduct" class="toggle btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a>
                            <a href="#" data-target="addProduct" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Product</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->