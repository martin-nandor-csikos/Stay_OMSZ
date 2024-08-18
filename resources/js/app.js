import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// Dark mode
var themeToggleBtn = document.getElementById("theme-toggle");
var themeToggleRespBtn = document.getElementById("theme-toggle-resp");

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

$(function() {
    var darkThemeEnabled = getCookie("color-theme") === "dark";

    if (darkThemeEnabled) {
        document.documentElement.classList.add("dark");
        themeToggleBtn.textContent = "Sötét mód";
        themeToggleRespBtn.textContent = "Sötét mód";
    } else {
        document.documentElement.classList.remove("dark");
        themeToggleBtn.textContent = "Világos mód";
        themeToggleRespBtn.textContent = "Világos mód";
    }

    document.body.classList.remove('hidden');
});

themeToggleBtn.addEventListener("click", function () {
    if (getCookie("color-theme")) {
        if (getCookie("color-theme") === "light") {
            document.documentElement.classList.add("dark");
            setCookie("color-theme", "dark", 365);
            themeToggleBtn.textContent = "Sötét mód";
        } else {
            document.documentElement.classList.remove("dark");
            setCookie("color-theme", "light", 365);
            themeToggleBtn.textContent = "Világos mód";
        }
    } else {
        if (document.documentElement.classList.contains("dark")) {
            document.documentElement.classList.remove("dark");
            setCookie("color-theme", "light", 365);
            themeToggleBtn.textContent = "Világos mód";
        } else {
            document.documentElement.classList.add("dark");
            setCookie("color-theme", "dark", 365);
            themeToggleBtn.textContent = "Sötét mód";
        }
    }
});

themeToggleRespBtn.addEventListener("click", function () {
    if (getCookie("color-theme")) {
        if (getCookie("color-theme") === "light") {
            document.documentElement.classList.add("dark");
            setCookie("color-theme", "dark", 365);
            themeToggleRespBtn.textContent = "Sötét mód";
        } else {
            document.documentElement.classList.remove("dark");
            setCookie("color-theme", "light", 365);
            themeToggleRespBtn.textContent = "Világos mód";
        }
    } else {
        if (document.documentElement.classList.contains("dark")) {
            document.documentElement.classList.remove("dark");
            setCookie("color-theme", "light", 365);
            themeToggleRespBtn.textContent = "Világos mód";
        } else {
            document.documentElement.classList.add("dark");
            setCookie("color-theme", "dark", 365);
            themeToggleRespBtn.textContent = "Sötét mód";
        }
    }
});

// Admin Ajax
/*
$(document).ready(function () {
    function fetchData(url, targetId) {
        $.ajax({
            url: url,
            method: "GET",
            success: function (data) {
                $(targetId).html(data);
            },
            complete: function () {
                setTimeout(function () {
                    fetchData(url, targetId);
                }, 3000);
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
    setTimeout(updateAllData, 3000);
});
*/

// Report checkbox
$(document).ready(function() {
    $('input[type=checkbox]').click(function () {
        let price = parseFloat($('#price').val()) || 0;
        let checkedValues = $('input[type=checkbox]:checked').map(function () {
            return this.value;
        }).get().join(', ');

        $('#diagnosis').val(checkedValues || '');

        let checkboxPrices = {
            'vizs': 20000,
            'kot': 30000,
            'gip': 35000,
            'gyogy': 30000,
            'kav': 20000,
            'as': 30000,
            'ss': 35000,
            'emb': 20000,
            'th': 150000
        };

        price = 0;

        $.each(checkboxPrices, function (id, amount) {
            if ($('#' + id).is(':checked')) {
                if ((price + amount) >= 300000) {
                    price = 300000;
                } else {
                    price += amount;
                }
            }
        });

        $('#price').val(price);
    });
});


// Dashboard ajax
// $(document).ready(function () {
//     function fetchData(url, targetId) {
//         $.ajax({
//             url: url,
//             method: "GET",
//             success: function (data) {
//                 $(targetId).html(data);
//             },
//             complete: function () {
//                 setTimeout(function () {
//                     fetchData(url, targetId);
//                 }, 10000);
//             }
//         });
//     }

//     var target = "#dashboard-table";

//     function updateAllData() {
//         fetchData(window.routes.dashboard, target);
//     }

//     updateAllData();
//     setTimeout(updateAllData, 10000);
// });