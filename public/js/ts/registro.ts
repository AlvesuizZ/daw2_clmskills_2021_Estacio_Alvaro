document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm") as HTMLFormElement;
    const passwordInput = document.getElementById("password") as HTMLInputElement;
    const confirmPasswordInput = document.getElementById("confirmPassword") as HTMLInputElement;
    const emailInput = document.getElementById("email") as HTMLInputElement;
    const emailFeedback = document.getElementById("email-feedback") as HTMLElement;

    // Validación del formulario con Bootstrap
    form.addEventListener("submit", (event) => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add("was-validated");
    });


    confirmPasswordInput.addEventListener("input", () => {
        if (confirmPasswordInput.value !== passwordInput.value) {
            confirmPasswordInput.setCustomValidity("Las contraseñas no coinciden.");
        } else {
            confirmPasswordInput.setCustomValidity("");
        }
    });


    emailInput.addEventListener("blur", async () => {
        const response = await fetch(`/api/validarEmail?email=${emailInput.value}`);
        const data = await response.json();

        if (data.exists) {
            emailFeedback.textContent = "Este email ya está registrado.";
            emailInput.setCustomValidity("Email en uso");
        } else {
            emailFeedback.textContent = "";
            emailInput.setCustomValidity("");
        }
    });
});
