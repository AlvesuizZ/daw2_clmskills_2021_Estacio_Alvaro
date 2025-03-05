document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("editCategoriaForm") as HTMLFormElement;
    const nombreInput = document.getElementById("nombre") as HTMLInputElement;
    const fotoInput = document.getElementById("foto") as HTMLInputElement;

    if (!form) return;

    let nombreValido = false;

    //  Validación asíncrona para verificar si el nombre ya existe
    async function validarNombreCategoria(nombre: string): Promise<boolean> {
        const response = await fetch(`/categorias/verificar-nombre?nombre=${nombre}`);
        const data = await response.json();
        return !data.exists;
    }

    // Validación del nombre al perder el foco
    nombreInput.addEventListener("blur", async () => {
        if (nombreInput.value.length >= 3) {
            nombreValido = await validarNombreCategoria(nombreInput.value);

            // Si el nombre ya existe, marcar como inválido
            nombreInput.classList.toggle("is-invalid", !nombreValido);
            nombreInput.classList.toggle("is-valid", nombreValido);
        }
    });

    // Validación antes de enviar el formulario
    form.addEventListener("submit", async function (event) {
        let valid = true;

        // Validar nombre (entre 3 y 50 caracteres)
        if (nombreInput.value.length < 3 || nombreInput.value.length > 50) {
            nombreInput.classList.add("is-invalid");
            valid = false;
        } else {
            nombreInput.classList.remove("is-invalid");
        }

        //  **Revalidar nombre único antes de enviar**
        if (!nombreValido) {
            nombreInput.classList.add("is-invalid");
            valid = false;
        }

        // Validar imagen (si el usuario sube una nueva)
        if (fotoInput.files && fotoInput.files.length > 0) {
            const file = fotoInput.files[0];
            const allowedTypes = ["image/jpeg", "image/png"];

            if (!allowedTypes.includes(file.type) || file.size > 150 * 1024) {
                fotoInput.classList.add("is-invalid");
                valid = false;
            } else {
                fotoInput.classList.remove("is-invalid");
            }
        }

        if (!valid) {
            event.preventDefault(); // ⛔ Evita el envío del formulario si hay errores
        }
    });
});
