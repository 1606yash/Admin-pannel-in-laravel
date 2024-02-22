class APIDataTable {
    //Assign values in class
    constructor({ tableElem, pageinationElem, api, authToken, columns, filterSubmit, filterSubmitCallback, filterClearSubmit, filterClearSubmitCallback, filterModalId, filterIds = [], tagId = '#filter_tag_list' }) {
        this.table = document.querySelector(tableElem);
        this.tbody = this.table.querySelector("tbody");
        this.pageinationElem = document.querySelector(pageinationElem);
        this.API = api;
        this.Authorization = authToken;
        this.columns = columns;
        this.filterSubmit = filterSubmit;
        this.filterSubmitCallback = filterSubmitCallback;
        this.filterClearSubmit = filterClearSubmit;
        this.filterClearSubmitCallback = filterClearSubmitCallback;
        this.filterModalId = filterModalId;
        this.filters = {};
        this.filterIds = filterIds;
        this.tagId = tagId;
        this.pageName = window.location;
        // events
        if(this.filterSubmit){
            document.querySelector(this.filterSubmit).addEventListener('click', this.filter.bind(this));
        }
        if(this.filterClearSubmit){
            document.querySelector(this.filterClearSubmit).addEventListener('click', this.clearFilter.bind(this));
        }
        this.init();
    }

    //Create Pagination
    pagination(meta) {
        var vm = this;
        var onClick = function (pageNumber, e) {
                     vm.getData({
                         page: pageNumber,
                     });
                 };
        $(vm.pageinationElem).pagination({
            items: meta.pagination.total,
            itemsOnPage: meta.pagination.per_page,
            currentPage: meta.pagination.current_page,
            onPageClick: onClick,
            cssStyle: 'light-theme'
        });
        // var vm = this;
        // vm.pageinationElem.innerHTML = "";
        // var pageLength = meta.pagination.total / meta.pagination.per_page;
        // var ul = document.createElement("ul");
        // ul.className =
        //     "pagination justify-content-center justify-content-md-start";
        // for (var pageIndex = 0; pageIndex < pageLength; pageIndex++) {
        //     var pageNumber = pageIndex + 1;
        //     var onClick = function (e) {
        //         vm.getData({
        //             page: e.target.dataset.pagenumber,
        //         });
        //     };
        //     var li = document.createElement("li");
        //     li.classList = "page-item";
        //     var a = document.createElement("a");
        //     a.className = "page-link";
        //     a.setAttribute("data-pagenumber", pageNumber);
        //     a.innerHTML = pageNumber;
        //     console.log('pageIndex: ', pageIndex);
        //     if (pageIndex >= 5) {
        //         li.classList += " hide";
        //     }
        //     if (meta.pagination.current_page == pageIndex + 1) {
        //         li.classList += " active";
        //     } else {
        //         a.addEventListener("click", onClick);
        //     }
        //     li.appendChild(a);
        //     ul.appendChild(li);
        // }
        // vm.pageinationElem.appendChild(ul);
    }

    //map Table Rows
    mapRow(data) {
        var tbody = this.tbody;
        var columns = this.columns;
        tbody.innerHTML = "";
        if (data.length > 0) {
            for (var dateIndex = 0; dateIndex < data.length; dateIndex++) {
                var trData = data[dateIndex];
                var tr = document.createElement("tr");
                tr.className = "nk-tb-item";
                for (
                    let itemIndex = 0;
                    itemIndex < columns.length;
                    itemIndex++
                ) {
                    var col = columns[itemIndex];
                    var td = document.createElement("td");
                    td.className = "nk-tb-col tb-col-lg";
                    var coltext = trData[col.data];
                    if (col.render) {
                        coltext = col.render(trData);
                    }
                    td.innerHTML = coltext;
                    tr.appendChild(td);
                }
                tbody.appendChild(tr);
            }
        } else {
            var tr = document.createElement("tr");
            tr.className = "";
            var td = document.createElement("td");
            td.className = "dataTables_empty";
            td.setAttribute("colspan", columns.length);
            td.innerHTML = '<div class="text-center"><div><img width="300" src="'+APP_BASE_URL+'/images/empty_item.svg" /></div><h5>No record found.</h5></div>';
            tr.appendChild(td);
            tbody.appendChild(tr);
        }

        return tbody;
    }

    //Get Data from APIs
    getData(meta) {
        var vm = this;
        $.ajax({
            url: vm.API,
            type: "get",
            data: meta,
            headers: {
                Authorization: vm.Authorization,
            },
            success: function (data) {
                vm.mapRow(data.success.data);
                vm.pagination(data.success.meta);
                vm.filterTags(meta);
            },
        });
    }

    // Get Data from APIs
    init() {
        var vm = this;
        var FilterDetails = localStorage.getItem(vm.pageName);
        if(FilterDetails != '' && FilterDetails != null){
            vm.filters = JSON.parse(FilterDetails);
            jQuery.each(vm.filters, function(key, item) {
                $(vm.filterModalId).find('[name="'+key+'"]').val(item).trigger("change");
            });
        }
        this.getData(vm.filters);
    }

    //Apply Filter
    filter(callback) {
        var vm = this;
        var filterData = {}
        filterData.page = 1;
        
        jQuery.each(vm.filterIds, function(i, item) {
            var element = jQuery(item);
            var filterName = '';
            var filterLabel = element[0].name;
            var filterValue = element.val();
            var filterValueText = $("#"+element[0].id + " option:selected").val();
            if(element[0].type == 'select-one'){
                filterName = filterValueText;
            }else if(element[0].type == 'select-multiple'){
                filterName = filterValue;
            }
            else{
                filterName = filterValue;
            } 
            filterData[element[0].name] = element.val();
            
        });
        this.getData(filterData);
        this.filters = filterData;
        if(callback){
            if(vm.filterSubmitCallback){
                vm.filterSubmitCallback();
            }
        }
        // Save Filter in Local Storage
        window.localStorage.setItem(vm.pageName, JSON.stringify(filterData))
    }

    // Clear Filter
    clearFilter(){
        var vm = this;        
        function resetFilter(modal) {
            $(modal).find("form").trigger("reset");
            $(".form-select").val("0").trigger("change");
            $(".form-select").prop("selectedIndex", 0);
            $(modal).removeData();
        };
        resetFilter(vm.filterModalId);
        if (vm.filterClearSubmitCallback) {
            vm.filterClearSubmitCallback();
        }
        vm.filterTags();
        vm.filter(false);
    }
    // Delete Filter Tag
    deleteFilterTag(item){
        item.val('');
        if ($(item).hasClass("form-select")) {
            item.val("0").trigger("change");
            item.prop("selectedIndex", 0);
        }
        // delete this.filters[item.attr('name')]
        // this.filters.page = 1;
        this.filter(false);
        
    }
    // Create filter Tags
    filterTags(){
        var vm = this;
        
        var $list = $(vm.tagId);
        var ulElement = document.createElement('ul');
        ulElement.innerHTML = '<li class="filterby-text">Filter By:</li>';
        $list.empty();

        jQuery.each(vm.filterIds, function(i, elementName) {
            var element = $(elementName);    

            var getElement = document.getElementById(element[0].id);            
            var filterName = '';
            var filterValue = element.val();
            var filterValueText = $("#"+element[0].id + " option:selected").text();
            var filterLabel = element[0].id;
            var clickElement = function(){
                vm.deleteFilterTag(element)
            }
            if(filterValue != '' && filterValue != null){
                if(getElement.type == 'select-one'){
                    filterName = filterValueText;
                }else{
                    filterName = filterValue;
                }
                var crossElement = document.createElement('a');
                crossElement.innerHTML = '<em class="icon ni ni-cross"></em>';
                crossElement.addEventListener('click', clickElement);
    
                var listElement = document.createElement('li');
                listElement.innerHTML = "<strong>" + filterLabel + "</strong>" + ": " + filterName;
                listElement.append(crossElement)
    
                ulElement.appendChild(listElement);
                $list.append(ulElement);
            }
        });
    }
}
