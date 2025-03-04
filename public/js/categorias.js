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
var _a;
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("categoriaForm");
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
            nombreInput.classList.toggle("is-invalid", !nombreValido);
        }
    }));
    const mensajeDiv = document.getElementById("mensaje");
    const categoriasContainer = document.getElementById("categorias-container");
    form.addEventListener("submit", (event) => __awaiter(void 0, void 0, void 0, function* () {
        event.preventDefault();
        event.stopPropagation();
        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }
        const formData = new FormData(form);
        try {
            const response = yield fetch("/api/categorias/add", {
                method: "POST",
                body: formData
            });
            const data = yield response.json();
            if (data.success) {
                mensajeDiv.innerHTML = `<div class="alert alert-success">${data.success}</div>`;
                form.reset();
                form.classList.remove("was-validated");
                cargarCategorias();
            }
            else {
                mensajeDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        }
        catch (error) {
            mensajeDiv.innerHTML = `<div class="alert alert-danger">Error al añadir categoría.</div>`;
            console.error(error);
        }
    }));
    // Función para cargar categorías de forma asíncrona
    function cargarCategorias() {
        return __awaiter(this, void 0, void 0, function* () {
            const response = yield fetch("/api/categorias/list");
            const categorias = yield response.json();
            categoriasContainer.innerHTML = "";
            categorias.forEach((categoria) => {
                const div = document.createElement("div");
                div.className = "col-md-4 mb-4";
                div.innerHTML = `
                <div class="card shadow-sm border-0">
                    <img src="data:image/jpeg;base64,${categoria.foto}" class="card-img-top" alt="${categoria.nombre}">
                    <div class="card-body text-center">
                        <h5 class="card-title">${categoria.nombre}</h5>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button class="btn btn-warning btn-sm edit-btn" data-id="${categoria.idcategoria}">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${categoria.idcategoria}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
                categoriasContainer.appendChild(div);
            });
        });
    }
    cargarCategorias();
});
(_a = document.getElementById("categorias-container")) === null || _a === void 0 ? void 0 : _a.addEventListener("click", (e) => {
    const target = e.target;
    if (target.closest(".edit-btn")) {
        const button = target.closest(".edit-btn");
        const id = button.dataset.id;
        if (id) {
            console.log(`Redirigiendo a /categorias/editar/${id}`);
            window.location.href = `/editCategoria/${id}`;
        }
        else {
            console.error("Error: No se encontró el ID de la categoría.");
        }
    }
});
document.addEventListener("DOMContentLoaded", () => {
    const categoriasContainer = document.getElementById("categorias-container");
    if (!categoriasContainer)
        return;
    categoriasContainer.addEventListener("click", (event) => __awaiter(void 0, void 0, void 0, function* () {
        var _a;
        const target = event.target;
        if (target.closest(".delete-btn")) {
            const button = target.closest(".delete-btn");
            const id = button.dataset.id;
            if (!id) {
                console.error("No se encontró el ID de la categoría.");
                return;
            }
            if (confirm("¿Estás seguro de que quieres eliminar esta categoría y todos sus animales?")) {
                try {
                    const response = yield fetch(`/categoriaDelete/${id}`, {
                        method: "DELETE",
                        headers: { "Content-Type": "application/json" }
                    });
                    const data = yield response.json();
                    if (data.success) {
                        alert("Categoría eliminada correctamente.");
                        (_a = button.closest(".col-md-4")) === null || _a === void 0 ? void 0 : _a.remove(); // ⬅️ Elimina la tarjeta de la categoría
                    }
                    else {
                        alert("Error al eliminar la categoría.");
                    }
                }
                catch (error) {
                    console.error("Error al eliminar:", error);
                }
            }
        }
    }));
});
//# sourceMappingURL=categorias.js.map