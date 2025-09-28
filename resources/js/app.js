import './bootstrap';


document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("darkModeToggle");
    toggle?.addEventListener("click", () => {
        document.documentElement.classList.toggle("dark");
    });
});

