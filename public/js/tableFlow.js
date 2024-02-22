class CustomDataTable {
    //Assign values in class
    constructor({ tableElem, option, filterSubmit, filterSubmitCallback, filterClearSubmit, filterClearSubmitCallback, filterItems, tagId, filterModalId }) {
        this.tableElem = tableElem;
        this.option = option;
        this.filterSubmit = filterSubmit;
        this.filterSubmitCallback = filterSubmitCallback;
        this.filterClearSubmit = filterClearSubmit;
        this.filterClearSubmitCallback = filterClearSubmitCallback;
        this.filterModalId = filterModalId;
        this.filterItems = filterItems;
        this.tagId = tagId;
        this.pageName = window.location;
        // events
        if (document.querySelector(this.filterSubmit)) {
            document.querySelector(this.filterSubmit).addEventListener('click', this.setFilter.bind(this));
        }

        if (document.querySelector(this.filterClearSubmit)) {
            document.querySelector(this.filterClearSubmit).addEventListener('click', this.clearFilter.bind(this));
        }

        this.table = this.init();
    }

    // Get Data from APIs
    init() {
        var vm = this;
        var FilterDetails = localStorage.getItem(vm.pageName);
        // Fetch Filter Data From Local Storage And Fill In The Form'S Fields
        function fetchFilterDetails() {
            if(FilterDetails != '' && FilterDetails != null){
                var FilteredList = JSON.parse(FilterDetails);
                jQuery.each(FilteredList, function(key, item) {
                    $(vm.filterModalId).find('[name="'+key+'"]').val(item).trigger("change");
                });
            }
        };
        fetchFilterDetails();
        vm.option.ajax.data = function(d) {
            if(FilterDetails != '' && FilterDetails != null){
                var getFilter = JSON.parse(FilterDetails);
                d = Object.assign(d, getFilter);
            }
        }
        vm.option.ajax.dataSrc = function(res) {
            $('.record_count').text(res.recordsTotal);
            return res.data;
        }
        vm.filterTag();
        return NioApp.DataTable(vm.tableElem, vm.option)
    }
    // Draw Table
    draw(){
        this.table[this.tableElem].draw();
        this.filterTag();
    }
    // Set Filter
    setFilter(callback = true){
        var vm = this;
        var filter = {}; 
        vm.table[this.tableElem].destroy();
        // Save Filter in Local Storage
        function saveFilter(filterLabel, filterName){
            filter[filterLabel] = filterName;
            window.localStorage.setItem(vm.pageName, JSON.stringify(filter))
        }
        vm.option.ajax.data = function(d) {
            jQuery.each(vm.filterItems, function(i, item) {
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
                d[element[0].name] = element.val();
                saveFilter(filterLabel, filterName) 
            });
        }

        vm.option.ajax.dataSrc = function(res) {
            $('.record_count').text(res.recordsTotal);
            return res.data;
        }
        if(callback){
            vm.filterSubmitCallback();
        }
        vm.filterTag();
        vm.table = NioApp.DataTable(vm.tableElem, vm.option)
    }
    // Clear Filter
    clearFilter(){
        var vm = this;
        vm.table[this.tableElem].destroy();
        function resetFilter(modal) {
            $(modal).find("form").trigger("reset");
            $(".form-select").val("0").trigger("change");
            $(".form-select").prop("selectedIndex", 0);
            $(modal).removeData();
            window.localStorage.setItem(vm.pageName, '');
        };
        resetFilter(vm.filterModalId);
        if (vm.filterClearSubmitCallback) {
            vm.filterClearSubmitCallback();
        }
        vm.filterTag()
        vm.table = NioApp.DataTable(vm.tableElem, vm.option)
    }
    // Apply Filter
    filterTag() {
        var vm = this;
        function removeFilter(filterLabel){
            var filter = window.localStorage.getItem(vm.pageName);
            filter = JSON.parse(filter);
            delete filter[filterLabel];
            window.localStorage.setItem(vm.pageName, JSON.stringify(filter))
        }   
        function clickThisElement(item){
            item.val('');
            if ($(item).hasClass("form-select")) {
                item.val("0").trigger("change");
                item.prop("selectedIndex", 0);
            }
            removeFilter(item[0].id);
            vm.setFilter(false);
        }
        function clickThisSubElement(item, deleteValue){
            var values = item.val();
            const index = values.indexOf(deleteValue);
            if (index > -1) {
                values.splice(index, 1);
            }
            item.val(values).trigger("change");
            removeFilter(item[0].id);
            vm.setFilter(false);
        }
        var $list = $(vm.tagId);
        var ulElement = document.createElement('ul');
        ulElement.innerHTML = '<li class="filterby-text">Filter By:</li>';
        $list.empty();
        
        function printTag(clickElement, filterLabel, filterName){
            var crossElement = document.createElement('a');
            crossElement.innerHTML = '<em class="icon ni ni-cross"></em>';
            crossElement.addEventListener('click', clickElement);
            var listElement = document.createElement('li');
            listElement.innerHTML = "<strong>" + filterLabel + "</strong>" + ": " + filterName;
            listElement.append(crossElement);
            ulElement.appendChild(listElement);
            $list.append(ulElement);
            
        }
        
        jQuery.each(vm.filterItems, function(i, elementName) {
            var element = $(elementName);
            var filterName = '';
            var filterValue = element.val();
            var filterValueText = $("#"+element[0].id + " option:selected").text();
            var filterLabel = element[0].id;
            var clickElement = function(){
                clickThisElement(element)
            }
            if(filterValue != '' && filterValue != null){
                if(element[0].type == 'select-one'){
                    filterName = filterValueText;
                    printTag(clickElement, filterLabel, filterName);
                }else if(element[0].type == 'select-multiple'){
                    filterName = filterValue;
    
                    if(element[0].selectedOptions.length >= 1){
                        jQuery.each(element[0].selectedOptions, function(index, option) {
                            var filterName = option.innerHTML;
                            var clickSubElement = function(){
                                clickThisSubElement(element, option.value)
                            }
                            printTag(clickSubElement, filterLabel, filterName);
                        });
                    }
                }
                else{
                    filterName = filterValue;
                    printTag(clickElement, filterLabel, filterName);
                }   
            }     
        });   
    }
}