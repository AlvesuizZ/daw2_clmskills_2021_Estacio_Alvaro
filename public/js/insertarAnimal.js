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
    const form = document.getElementById("insertAnimalForm");
    const nombreInput = document.getElementById("nombrecomun");
    const nombreCientificoInput = document.getElementById("nombrecientifico");
    const fotoInput = document.getElementById("foto");
    const categoriaInput = document.getElementById("idcategoria");
    const resumenInput = document.getElementById("resumen");
    if (!form)
        return;
    let nombreValido = false;
    let nombreCientificoValido = false;
    // 🚀 Función para validar nombres únicos de forma asíncrona
    function validarNombre(nombre, tipo) {
        return __awaiter(this, void 0, void 0, function* () {
            const response = yield fetch(`/animales/verificar-${tipo}?${tipo}=${nombre}`);
            const data = yield response.json();
            return !data.exists;
        });
    }
    // Validación asíncrona de nombre común
    nombreInput.addEventListener("blur", () => __awaiter(void 0, void 0, void 0, function* () {
        if (nombreInput.value.length >= 3) {
            nombreValido = yield validarNombre(nombreInput.value, "nombre");
            nombreInput.classList.toggle("is-invalid", !nombreValido);
        }
    }));
    // Validación asíncrona de nombre científico
    nombreCientificoInput.addEventListener("blur", () => __awaiter(void 0, void 0, void 0, function* () {
        if (nombreCientificoInput.value.length >= 3) {
            nombreCientificoValido = yield validarNombre(nombreCientificoInput.value, "nombrecientifico");
            nombreCientificoInput.classList.toggle("is-invalid", !nombreCientificoValido);
        }
    }));
    // Validación del formulario antes de enviarlo
    form.addEventListener("submit", (event) => {
        let valid = true;
        // Validar nombre común
        if (nombreInput.value.length < 3 || nombreInput.value.length > 50) {
            nombreInput.classList.add("is-invalid");
            valid = false;
        }
        else {
            nombreInput.classList.remove("is-invalid");
        }
        // Validar nombre científico
        if (nombreCientificoInput.value.length < 3 || nombreCientificoInput.value.length > 50) {
            nombreCientificoInput.classList.add("is-invalid");
            valid = false;
        }
        else {
            nombreCientificoInput.classList.remove("is-invalid");
        }
        // Validar categoría seleccionada
        if (!categoriaInput.value) {
            categoriaInput.classList.add("is-invalid");
            valid = false;
        }
        else {
            categoriaInput.classList.remove("is-invalid");
        }
        // Validar descripción
        if (resumenInput.value.trim() === "") {
            resumenInput.classList.add("is-invalid");
            valid = false;
        }
        else {
            resumenInput.classList.remove("is-invalid");
        }
        // Validar imagen (obligatoria y menor de 150 KB)
        if (!fotoInput.files || fotoInput.files.length === 0) {
            fotoInput.classList.add("is-invalid");
            valid = false;
        }
        else {
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
        // Verificar nombres únicos antes de enviar
        if (!nombreValido || !nombreCientificoValido) {
            valid = false;
        }
        if (!valid) {
            event.preventDefault(); // ⛔ Evita que el formulario se envíe si hay errores
        }
    });
});
//# sourceMappingURL=insertarAnimal.js.map