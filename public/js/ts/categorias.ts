document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("categoriaForm") as HTMLFormElement | null;
    const nombreInput = document.getElementById("nombre") as HTMLInputElement;
    const fotoInput = document.getElementById("foto") as HTMLInputElement;

    if (!form) return;

    let nombreValido = false;

    // Validación asíncrona para verificar si el nombre ya existe
    async function validarNombreCategoria(nombre: string): Promise<boolean> {
        const response = await fetch(`/categorias/verificar-nombre?nombre=${nombre}`);
        const data = await response.json();
        return !data.exists;    
    }

    // Validación del nombre al perder el foco
    nombreInput.addEventListener("blur", async () => {
        if (nombreInput.value.length >= 3) {
            nombreValido = await validarNombreCategoria(nombreInput.value);
            nombreInput.classList.toggle("is-invalid", !nombreValido);
        }
    });

    const mensajeDiv = document.getElementById("mensaje") as HTMLElement;
    const categoriasContainer = document.getElementById("categorias-container") as HTMLElement;

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch("/api/categorias/add", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                mensajeDiv.innerHTML = `<div class="alert alert-success">${data.success}</div>`;
                form.reset();
                form.classList.remove("was-validated");
                cargarCategorias();
            } else {
                mensajeDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        } catch (error) {
            mensajeDiv.innerHTML = `<div class="alert alert-danger">Error al añadir categoría.</div>`;
            console.error(error);
        }
    });

    // Función para cargar categorías de forma asíncrona
    async function cargarCategorias() {
        const response = await fetch("/api/categorias/list");
        const categorias = await response.json();

        categoriasContainer.innerHTML = "";
        categorias.forEach((categoria: any) => {
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
    }
    cargarCategorias();
});


document.getElementById("categorias-container")?.addEventListener("click", (e) => {
    const target = e.target as HTMLElement;

    if (target.closest(".edit-btn")) {
        const button = target.closest(".edit-btn") as HTMLButtonElement;
        const id = button.dataset.id;
        if (id) {
            console.log(`Redirigiendo a /categorias/editar/${id}`);
            window.location.href = `/editCategoria/${id}`;
        } else {
            console.error("Error: No se encontró el ID de la categoría.");
        }
    }
});




document.addEventListener("DOMContentLoaded", () => {
    const categoriasContainer = document.getElementById("categorias-container");

    if (!categoriasContainer) return;

    categoriasContainer.addEventListener("click", async (event) => {
        const target = event.target as HTMLElement;

        if (target.closest(".delete-btn")) {
            const button = target.closest(".delete-btn") as HTMLButtonElement;
            const id = button.dataset.id;

            if (!id) {
                console.error("No se encontró el ID de la categoría.");
                return;
            }

            if (confirm("¿Estás seguro de que quieres eliminar esta categoría y todos sus animales?")) {
                try {
                    const response = await fetch(`/categoriaDelete/${id}`, { 
                        method: "DELETE",
                        headers: { "Content-Type": "application/json" }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert("Categoría eliminada correctamente.");
                        button.closest(".col-md-4")?.remove(); 
                    } else {
                        alert("Error al eliminar la categoría.");
                    }
                } catch (error) {
                    console.error("Error al eliminar:", error);
                }
            }
        }
    });
});


