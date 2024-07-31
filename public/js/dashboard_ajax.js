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

    var target = "#dashboard-table";

    function updateAllData() {
        fetchData(window.routes.dashboard, target);
    }

    updateAllData();
    setInterval(updateAllData, 3000);
});