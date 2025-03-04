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
    const form = document.getElementById("editCategoriaForm");
    const nombreInput = document.getElementById("nombre");
    const fotoInput = document.getElementById("foto");
    if (!form)
        return;
    let nombreValido = false;
    // 🚀 Validación asíncrona para verificar si el nombre ya existe
    function validarNombreCategoria(nombre) {
        return __awaiter(this, void 0, void 0, function* () {
            const response = yield fetch(`/categorias/verificar-nombre?nombre=${nombre}`);
            const data = yield response.json();
            return !data.exists;
        });
    }
    // Validación del nombre al perder el foco
    nombreInput.addEventListener("blur", () => __awaiter(void 0, void 0, void 0, function* () {
        if (nombreInput.value.length >= 3) {
            nombreValido = yield validarNombreCategoria(nombreInput.value);
            // Si el nombre ya existe, marcar como inválido
            nombreInput.classList.toggle("is-invalid", !nombreValido);
            nombreInput.classList.toggle("is-valid", nombreValido);
        }
    }));
    // Validación antes de enviar el formulario
    form.addEventListener("submit", function (event) {
        return __awaiter(this, void 0, void 0, function* () {
            let valid = true;
            // Validar nombre (entre 3 y 50 caracteres)
            if (nombreInput.value.length < 3 || nombreInput.value.length > 50) {
                nombreInput.classList.add("is-invalid");
                valid = false;
            }
            else {
                nombreInput.classList.remove("is-invalid");
            }
            // 🚀 **Revalidar nombre único antes de enviar**
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
                }
                else {
                    fotoInput.classList.remove("is-invalid");
                }
            }
            if (!valid) {
                event.preventDefault(); // ⛔ Evita el envío del formulario si hay errores
            }
        });
    });
});
//# sourceMappingURL=editCategoria.js.map