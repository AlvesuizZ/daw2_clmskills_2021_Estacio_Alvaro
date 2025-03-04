document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("insertAnimalForm") as HTMLFormElement;
    const nombreInput = document.getElementById("nombrecomun") as HTMLInputElement;
    const nombreCientificoInput = document.getElementById("nombrecientifico") as HTMLInputElement;
    const fotoInput = document.getElementById("foto") as HTMLInputElement;
    const categoriaInput = document.getElementById("idcategoria") as HTMLSelectElement;
    const resumenInput = document.getElementById("resumen") as HTMLTextAreaElement;

    if (!form) return;

    let nombreValido = false;
    let nombreCientificoValido = false;

    // 🚀 Función para validar nombres únicos de forma asíncrona
    async function validarNombre(nombre: string, tipo: string): Promise<boolean> {
        const response = await fetch(`/animales/verificar-${tipo}?${tipo}=${nombre}`);
        const data = await response.json();
        return !data.exists;
    }

    // Validación asíncrona de nombre común
    nombreInput.addEventListener("blur", async () => {
        if (nombreInput.value.length >= 3) {
            nombreValido = await validarNombre(nombreInput.value, "nombre");
            nombreInput.classList.toggle("is-invalid", !nombreValido);
        }
    });

    // Validación asíncrona de nombre científico
    nombreCientificoInput.addEventListener("blur", async () => {
        if (nombreCientificoInput.value.length >= 3) {
            nombreCientificoValido = await validarNombre(nombreCientificoInput.value, "nombrecientifico");
            nombreCientificoInput.classList.toggle("is-invalid", !nombreCientificoValido);
        }
    });

    // Validación del formulario antes de enviarlo
    form.addEventListener("submit", (event: Event) => {
        let valid = true;

        // Validar nombre común
        if (nombreInput.value.length < 3 || nombreInput.value.length > 50) {
            nombreInput.classList.add("is-invalid");
            valid = false;
        } else {
            nombreInput.classList.remove("is-invalid");
        }

        // Validar nombre científico
        if (nombreCientificoInput.value.length < 3 || nombreCientificoInput.value.length > 50) {
            nombreCientificoInput.classList.add("is-invalid");
            valid = false;
        } else {
            nombreCientificoInput.classList.remove("is-invalid");
        }

        // Validar categoría seleccionada
        if (!categoriaInput.value) {
            categoriaInput.classList.add("is-invalid");
            valid = false;
        } else {
            categoriaInput.classList.remove("is-invalid");
        }

        // Validar descripción
        if (resumenInput.value.trim() === "") {
            resumenInput.classList.add("is-invalid");
            valid = false;
        } else {
            resumenInput.classList.remove("is-invalid");
        }

        // Validar imagen (obligatoria y menor de 150 KB)
        if (!fotoInput.files || fotoInput.files.length === 0) {
            fotoInput.classList.add("is-invalid");
            valid = false;
        } else {
            const file = fotoInput.files[0];
            const allowedTypes = ["image/jpeg", "image/png"];

            if (!allowedTypes.includes(file.type) || file.size > 150 * 1024) {
                fotoInput.classList.add("is-invalid");
                valid = false;
            } else {
                fotoInput.classList.remove("is-invalid");
            }
        }

        // Verificar nombres únicos antes de enviar
        if (!nombreValido || !nombreCientificoValido) {
            valid = false;
        }

        if (!valid) {
            event.preventDefault(); // ⛔ Evita que el formulario se envíe si hay errores
        }
    });
});
