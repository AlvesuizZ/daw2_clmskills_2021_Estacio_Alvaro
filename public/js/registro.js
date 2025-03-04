"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const emailInput = document.getElementById("email");
    const emailFeedback = document.getElementById("email-feedback");
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
        }
        else {
            confirmPasswordInput.setCustomValidity("");
        }
    });
    emailInput.addEventListener("blur", () => __awaiter(void 0, void 0, void 0, function* () {
        const response = yield fetch(`/api/validarEmail?email=${emailInput.value}`);
        const data = yield response.json();
        if (data.exists) {
            emailFeedback.textContent = "Este email ya está registrado.";
            emailInput.setCustomValidity("Email en uso");
        }
        else {
            emailFeedback.textContent = "";
            emailInput.setCustomValidity("");
        }
    }));
});
//# sourceMappingURL=registro.js.map