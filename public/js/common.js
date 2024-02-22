// Extend NioApp Object
NioApp.Toggle.collapseDrawer = function (elem) {
    var def = {
        active: "active",
        content: "content-active",
        break: true,
    };
    NioApp.Toggle.removed(elem, def);
};

NioApp.Toggle.expendDrawer = function (elem) {
    var def = {
        active: "active",
        content: "content-active",
        break: true,
    };
    NioApp.Toggle.trigger(elem, def);
};

NioApp.resetModalForm = function (
    modalId,
    resetData,
    toggleId,
    forceFullyCloseModal,
    callback
) {
    function resetFilter(modal) {
        $(modal).find("form").trigger("reset");
        $(".form-select").val("0").trigger("change");
        $(".form-select").prop("selectedIndex", 0);
        $(modal).removeData();
        window.localStorage.removeItem(window.location);
    }
    $(toggleId).click(function () {
        resetFilter(modalId);
        if (forceFullyCloseModal) {
            console.log("forceFullyCloseModal: ", forceFullyCloseModal);

            $(modalId + " .close").click();
        }

        if (callback) {
            callback();
        }

        resetData.draw();
    });
};

NioApp.getAuditLogs = function (
    parentElement,
    elementClass,
    resourceAttr,
    url,
    modelId
) {
    $(parentElement).on("click",elementClass,function() {
        var resourceId = $(this).attr('data-'+resourceAttr);
        $.ajax({
            url: url+'/'+resourceId,
            data: {
            },
            //dataType: "html",
            method: "GET",
            cache: false,
            success: function (response) {
                if (response.success) {
                    $("#orderLogs").html("");
                    $.each(response.logs, function (key, value) {
                        $("#orderLogs").append(value); 
                    });
                    $(modelId).modal('show');
                } else {
                    Swal.fire({
                          icon: 'error',
                          title: 'Oops...',
                          text: response.msg,
                        })
                }
            }
        });
    })
};

NioApp.formatToCurrency = function (amount) {
    if (amount) {
        let expo = amount;
        if (amount.toString().search('e') != -1) {
            expo = amount.toLocaleString();
            return '₹' + expo;
        } else {
            let floatPrice = parseFloat(amount);
            let finalAmount = floatPrice
                ? '₹' + floatPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                : '₹0.00';
            return finalAmount;
        }
    } else {
        return '₹0.00';
    }
};

NioApp.filterTag = function (items, table, id) {
    var pageName = window.location;
    var filter = {}; 
    function setFilter(filterLabel, filterName){
        filter[filterLabel] = filterName;
        window.localStorage.setItem(pageName, JSON.stringify(filter))
    }

    function removeFilter(filterLabel){
        delete filter[filterLabel];
        window.localStorage.setItem(pageName, JSON.stringify(filter))
    }

    function clickThisElement(item){
        console.log(item);
        item.val('');
        if ($(item).hasClass("form-select")) {
            item.val("0").trigger("change");
            item.prop("selectedIndex", 0);
        }
        removeFilter(item[0].id);
        table.draw();
    }
    function clickThisSubElement(item, deleteValue){
        var values = item.val();
        const index = values.indexOf(deleteValue);
        if (index > -1) {
            values.splice(index, 1);
        }
        item.val(values).trigger("change");
        table.draw();
    }
    var $list = $(id);
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
    jQuery.each(items, function(i, elementName) {
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
                setFilter(filterLabel, filterName);
            }else if(element[0].type == 'select-multiple'){
                filterName = filterValue;

                setFilter(filterLabel, filterName);
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
                setFilter(filterLabel, filterName);
            }   
        }else{
            setFilter(filterLabel, '');
        }      
    });
    
}

$(document).ready(function () {
    $("form").parsley();
    $(".modal").on("hidden.bs.modal", function () {
        // $(this).find('form').trigger('reset');
        // $('.form-select').val('0').trigger('change');
        //$('.form-select').prop('selectedIndex',0);
        // $(this).removeData();
    });
});

// window.Parsley.on('form:validated', function(){
//     $('select').on('select2:select', function(evt) {
//         $("#select-id").parsley().validate();
//     });
// });
