$(document).ready(function () {
    function fetchData(url, targetId) {
        $.ajax({
            url: url,
            method: "GET",
            success: function (data) {
                $(targetId).html(data);
            },
            complete: function (data) {
                setTimeout(fetchData, 10000);
            }
        });
    }

    var target = "#dashboard-table";

    function updateAllData() {
        fetchData(window.routes.dashboard, target);
    }

    updateAllData();
    setTimeout(updateAllData, 10000);
});