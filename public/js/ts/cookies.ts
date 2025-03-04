document.addEventListener("DOMContentLoaded", () => {
    const cookieBanner = document.getElementById("cookie-banner") as HTMLElement;
    const acceptCookies = document.getElementById("accept-cookies") as HTMLButtonElement;

    if (localStorage.getItem("cookiesAccepted") === "true") {
        cookieBanner.style.display = "none";
    } else {
        cookieBanner.style.display = "block";
    }

    acceptCookies.addEventListener("click", () => {
        localStorage.setItem("cookiesAccepted", "true");
        cookieBanner.style.display = "none";
    });
});
