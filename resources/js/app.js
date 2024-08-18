import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

var themeToggleBtn = document.getElementById("theme-toggle");

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

var currentTheme = getCookie("color-theme");

if (currentTheme) {
    if (currentTheme === "dark") {
        document.documentElement.classList.add("dark");
        themeToggleBtn.textContent = "Sötét mód";
    } else {
        document.documentElement.classList.remove("dark");
        themeToggleBtn.textContent = "Világos mód";
    }
}

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
