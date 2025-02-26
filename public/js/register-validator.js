document.addEventListener("DOMContentLoaded", () => {
    console.log("JavaScript cargado correctamente");

    const form = document.getElementById("registerForm");

    form.addEventListener("submit", function (event) {
        let isValid = true;

        // Capturar valores y limpiar espacios
        const nombreInput = document.getElementById("nombre");
        const emailInput = document.getElementById("email");
        const telefonoInput = document.getElementById("telef");
        const direccionInput = document.getElementById("direccion");
        const passwordInput = document.getElementById("password");
        const confirmPasswordInput = document.getElementById("confirmPassword");

        const nombre = nombreInput.value.trim();
        const email = emailInput.value.trim();
        const telefono = telefonoInput.value.trim();
        const direccion = direccionInput.value.trim();
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        console.log("Nombre:", nombre);
        console.log("Email:", email);
        console.log("Teléfono:", telefono);
        console.log("Dirección:", direccion);
        console.log("Password:", password);
        console.log("Confirm Password:", confirmPassword);

        // Validar que los campos no estén vacíos
        if (nombre === "") {
            nombreInput.classList.add("is-invalid");
            isValid = false;
        } else {
            nombreInput.classList.remove("is-invalid");
            nombreInput.classList.add("is-valid");
        }

        if (email === "") {
            emailInput.classList.add("is-invalid");
            isValid = false;
        } else {
            emailInput.classList.remove("is-invalid");
            emailInput.classList.add("is-valid");
        }

        // Validar Teléfono (Debe empezar con 6 y tener 9 dígitos)
        if (!/^6\d{8}$/.test(telefono)) {
            telefonoInput.classList.add("is-invalid");
            isValid = false;
        } else {
            telefonoInput.classList.remove("is-invalid");
            telefonoInput.classList.add("is-valid");
        }

        // Validar Dirección (No vacía y con caracteres válidos)
        if (direccion === "" || !/^[a-zA-Z0-9\s,.#-]+$/.test(direccion)) {
            direccionInput.classList.add("is-invalid");
            isValid = false;
        } else {
            direccionInput.classList.remove("is-invalid");
            direccionInput.classList.add("is-valid");
        }

        // Validar Contraseña (Debe tener al menos 8 caracteres, 1 mayúscula, 1 minúscula y 1 número)
        if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}/.test(password)) {
            passwordInput.classList.add("is-invalid");
            isValid = false;
        } else {
            passwordInput.classList.remove("is-invalid");
            passwordInput.classList.add("is-valid");
        }

        // Validar Confirmación de Contraseña
        if (password !== confirmPassword || confirmPassword === "") {
            confirmPasswordInput.classList.add("is-invalid");
            isValid = false;
        } else {
            confirmPasswordInput.classList.remove("is-invalid");
            confirmPasswordInput.classList.add("is-valid");
        }

        // Si hay errores, detener el envío del formulario
        if (!isValid) {
            console.log("⚠️ Hay errores en el formulario, no se enviará.");
            event.preventDefault();
            event.stopPropagation();
        } else {
            console.log("✅ Todos los datos son correctos, enviando formulario...");
        }
    });
});
