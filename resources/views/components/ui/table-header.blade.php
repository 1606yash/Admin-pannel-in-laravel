<div class="card mb-2">
    <div class="card-inner pt-2 pb-2 position-relative card-tools-toggle">
        <div class="card-title-group">
            <div class="card-tools">
                <div class="form-inline flex-nowrap gx-3">
                    <div class="form-wrap 50px">
                        <select class="form-select form-select-sm" data-search="off" data-placeholder="Bulk Action">
                            <option value="">Bulk Action</option>
                            <option value="email">Send Email</option>
                            <option value="group">Change Group</option>
                            <option value="suspend">Suspend User</option>
                            <option value="delete">Delete User</option>
                        </select>
                    </div>
                    <div class="btn-wrap">
                        <span class="d-none d-md-block"><button class="btn btn-dim btn-outline-light disabled">Apply</button></span>
                        <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
                    </div>
                </div><!-- .form-inline -->
            </div><!-- .card-tools -->
            <div class="card-tools mr-n1">
                <ul class="btn-toolbar gx-1">
                    <li>
                        <a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search"><em class="icon ni ni-search"></em></a>
                    </li><!-- li -->
                    <li class="btn-toolbar-sep"></li><!-- li -->
                    <li>
                        <div class="toggle-wrap">
                            <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-menu-right"></em></a>
                            <div class="toggle-content" data-content="cardTools">
                                <ul class="btn-toolbar gx-1">
                                    <li class="toggle-close">
                                        <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-arrow-left"></em></a>
                                    </li><!-- li -->
                                    
                                    <li>
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterBuyer">
                                                <div class="dot dot-primary"></div>
                                                <em class="icon ni ni-filter-alt"></em>
                                            </a>
                                        </div><!-- .dropdown -->
                                    </li><!-- li -->
                                    <li>
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                                <em class="icon ni ni-setting"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                                <ul class="link-check">
                                                    <li><span>Show</span></li>
                                                    <li class="active"><a href="#">10</a></li>
                                                    <li><a href="#">20</a></li>
                                                    <li><a href="#">50</a></li>
                                                </ul>
                                            </div>
                                        </div><!-- .dropdown -->
                                    </li><!-- li -->
                                    <li class="btn-toolbar-sep mr-2"></li>
                                    <li>
                                        <a href="#" data-target="addRole" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Discount</span></a>
                                    </li>
                                </ul><!-- .btn-toolbar -->
                            </div><!-- .toggle-content -->
                        </div><!-- .toggle-wrap -->
                    </li><!-- li -->
                </ul><!-- .btn-toolbar -->
            </div><!-- .card-tools -->
        </div><!-- .card-title-group -->
        <div class="card-search search-wrap" data-search="search">
            <div class="card-body">
                <div class="search-content">
                    <a href="#" class="search-back btn btn-icon toggle-search" data-target="search"><em class="icon ni ni-arrow-left"></em></a>
                    <input type="text" class="form-control border-transparent form-focus-none" placeholder="Search">
                    <button class="search-submit btn btn-icon"><em class="icon ni ni-search"></em></button>
                </div>
            </div>
        </div><!-- .card-search -->
    </div><!-- .card-inner -->
</div>