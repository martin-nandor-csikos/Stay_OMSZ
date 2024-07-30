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

    const routes = {
        weeklyStats: window.routes.weeklyStats,
        closedWeekStats: window.routes.closedWeekStats,
        inactivities: window.routes.inactivities,
        registratedUsers: window.routes.registratedUsers,
        adminLogs: window.routes.adminLogs
    };

    const targets = {
        weeklyStatsTable: "#weekly-stats-table",
        closedWeekStatsTable: "#closed-week-stats-table",
        inactivitiesTable: "#inactivities-table",
        registratedUsersTable: "#registrated-users-table",
        adminLogsTable: "#admin-logs-table"
    };

    function updateAllData() {
        fetchData(routes.weeklyStats, targets.weeklyStatsTable);
        fetchData(routes.closedWeekStats, targets.closedWeekStatsTable);
        fetchData(routes.inactivities, targets.inactivitiesTable);
        fetchData(routes.registratedUsers, targets.registratedUsersTable);
        fetchData(routes.adminLogs, targets.adminLogsTable);
    }

    updateAllData();
    setInterval(updateAllData, 3000);
});