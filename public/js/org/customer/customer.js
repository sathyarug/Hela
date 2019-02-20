

var SOURCE_HID = 0;
var CLUSTER_HID = 0;
var LOCATION_HID = 0;
var SUB_LOCATION_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
var TABLE2 = null;
var TABLE3 = null;
var TABLE4 = null;

$(document).ready(function(){

 TABLE3 = $('#customer_listing_tbl').DataTable({
    order: [[ 1, "asc" ]],
    scrollY:        "300px",
    scrollX:        true,
    scrollCollapse: true,
});
});





