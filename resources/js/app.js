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

function updateThemeToggle(button, isDark) {
    if (!button) {
        return;
    }

    var modeName = isDark ? "Sötét mód" : "Világos mód";
    var icon = isDark
        ? '<svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M21.64 13a1 1 0 0 0-1.05-.14A8.05 8.05 0 0 1 10.9 3.46a1 1 0 0 0-1.19-1.3A10 10 0 1 0 21.84 14.05 1 1 0 0 0 21.64 13Z"/></svg>'
        : '<svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>';

    button.innerHTML = icon;
    button.title = modeName;
    button.setAttribute("aria-label", modeName);
}

$(function() {
    var darkThemeEnabled = getCookie("color-theme") === "dark";

    if (darkThemeEnabled) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }

    updateThemeToggle(themeToggleBtn, darkThemeEnabled);
    updateThemeToggle(themeToggleRespBtn, darkThemeEnabled);

    document.body.classList.remove('hidden');
});

function toggleTheme(button) {
    if (getCookie("color-theme")) {
        if (getCookie("color-theme") === "light") {
            document.documentElement.classList.add("dark");
            setCookie("color-theme", "dark", 365);
            updateThemeToggle(themeToggleBtn, true);
            updateThemeToggle(themeToggleRespBtn, true);
        } else {
            document.documentElement.classList.remove("dark");
            setCookie("color-theme", "light", 365);
            updateThemeToggle(themeToggleBtn, false);
            updateThemeToggle(themeToggleRespBtn, false);
        }
    } else {
        if (document.documentElement.classList.contains("dark")) {
            document.documentElement.classList.remove("dark");
            setCookie("color-theme", "light", 365);
            updateThemeToggle(themeToggleBtn, false);
            updateThemeToggle(themeToggleRespBtn, false);
        } else {
            document.documentElement.classList.add("dark");
            setCookie("color-theme", "dark", 365);
            updateThemeToggle(themeToggleBtn, true);
            updateThemeToggle(themeToggleRespBtn, true);
        }
    }
}

if (themeToggleBtn) {
    themeToggleBtn.addEventListener("click", function () {
        toggleTheme(themeToggleBtn);
    });
}

if (themeToggleRespBtn) {
    themeToggleRespBtn.addEventListener("click", function () {
        toggleTheme(themeToggleRespBtn);
    });
}

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
