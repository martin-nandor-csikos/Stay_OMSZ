$(document).ready(function () {
    function fetchData(url, targetId) {
        $.ajax({
            url: url,
            method: "GET",
            success: function (data) {
                $(targetId).html(data);
            }
        });
    }

    var route = "{{ route('admin.dashboardTable') }}";
    var target = "#dashboard-table";

    function updateAllData() {
        fetchData(route, target);
    }

    updateAllData();
    setInterval(updateAllData, 3000);
});