@extends('layouts.app')
@section('content')

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Actualizar Producto</h5>
          <div class="d-flex gap-2">
            <a href="/product/list" class="btn btn-light btn-sm">
              <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <button class="btn btn-outline-danger btn-sm auth-only d-none" onclick="logout()">
              <i class="bi bi-box-arrow-right me-1"></i>Cerrar
            </button>
          </div>
        </div>
        <div class="card-body p-4">
          <form id="productUpdateForm" class="needs-validation" novalidate enctype="multipart/form-data" data-product-id="{{ request()->route('id') ?? request()->segment(3) }}" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required maxlength="100" placeholder="Ej: Camiseta deportiva">
                <div class="form-text">Nombre claro y breve.</div>
                <div class="invalid-feedback">El nombre es obligatorio.</div>
              </div>

              <div class="col-md-6">
                <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                <select class="form-select" id="category_id" name="category_id" required>
                  <option value="">Cargando categorías...</option>
                </select>
                <div class="invalid-feedback">Seleccione una categoría.</div>
              </div>

              <div class="col-md-12">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="2" maxlength="255" placeholder="Describe brevemente el producto"></textarea>
                <div class="form-text">Máximo 255 caracteres.</div>
              </div>

              <div class="col-md-4">
                <label for="price" class="form-label">Precio <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required placeholder="0.00">
                </div>
                <div class="invalid-feedback">El precio es obligatorio.</div>
              </div>

              <div class="col-md-4">
                <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="stock" name="stock" min="0" required placeholder="Ej: 10">
                <div class="invalid-feedback">El stock es obligatorio.</div>
              </div>

              <div class="col-md-4">
                <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                  <option value="">Seleccione un estado</option>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
                <div class="invalid-feedback">Debe seleccionar un estado.</div>
              </div>

              <div class="col-md-12">
                <label class="form-label">Imágenes Existentes</label>
                <div id="existingImages" class="d-flex flex-wrap gap-2 mb-2"></div>
              </div>

              <div class="col-md-12">
                <label for="newImages" class="form-label">Agregar Nuevas Imágenes</label>
                <input type="file" class="form-control" id="newImages" name="images[]" multiple accept="image/*" aria-describedby="imgHelp">
                <div id="imgHelp" class="form-text">Selecciona nuevas imágenes (opcional).</div>
              </div>

              <div class="col-md-12">
                <div id="preview" class="d-flex flex-wrap gap-2 mb-3"></div>
              </div>

            </div>

            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-success px-4" id="btnUpdateProduct" >
                <i class="bi bi-arrow-repeat me-1"></i> Actualizar Producto
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mostrar/ocultar elementos según autenticación
  const token = localStorage.getItem("token");
  if (token) {
      document.querySelectorAll(".auth-only").forEach(el => el.classList.remove("d-none"));
      document.querySelectorAll(".no-auth").forEach(el => el.classList.add("d-none"));
  } else {
      document.querySelectorAll(".auth-only").forEach(el => el.classList.add("d-none"));
      document.querySelectorAll(".no-auth").forEach(el => el.classList.remove("d-none"));
  }
  
  const form = document.getElementById('productUpdateForm');
  const newImagesInput = document.getElementById('newImages');
  const preview = document.getElementById('preview');
  const existingImagesContainer = document.getElementById('existingImages');
  let selectedFiles = [];
  let imagesToDelete = [];

  const productId = form.dataset.productId;
  const authHeaders = token ? { 'Authorization': 'Bearer ' + token } : {};
    const btnUpdate = document.getElementById('btnUpdateProduct');

    btnUpdate.addEventListener('click', function() {
        updateProduct();
    });
  const showSuccess = (msg) => {
    if (window.Swal) {
      Swal.fire({ icon: 'success', title: '¡Listo!', text: msg, confirmButtonColor: '#198754' });
    } else {
      alert(msg);
    }
  };

  const showError = (msg) => {
    if (window.Swal) {
      Swal.fire({ icon: 'error', title: 'Error', text: msg });
    } else {
      alert(msg);
    }
  };

  // Cargar categorías (con token si existe)
  fetch('/api/categories', { headers: authHeaders })
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('category_id');
      select.innerHTML = '<option value="">Seleccione una categoría</option>';
      data.data.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.name;
        select.appendChild(option);
      });
    })
    .catch(() => {
    
    });

 
  if (productId) {
    fetch(`/api/products/${productId}`, { headers: authHeaders })
      .then(res => res.json())
      .then(data => {
         console.log('upadte',data);
        document.getElementById('name').value = data.name || '';
        document.getElementById('description').value = data.description || '';
        document.getElementById('price').value = data.price ?? '';
        document.getElementById('stock').value = data.stock ?? '';
        document.getElementById('status').value = data.status ?? '';
        document.getElementById('category_id').value = data.category?.id ?? '';

        // Mostrar imágenes existentes
        existingImagesContainer.innerHTML = '';
        if (!data.product_images || data.product_images.length === 0) {
          existingImagesContainer.innerHTML = '<span class="text-muted">No hay imágenes guardadas.</span>';
        } else {
          data.product_images.forEach(img => {
            const wrapper = document.createElement('div');
            wrapper.classList.add('position-relative');
            wrapper.style.width = '100px';

            const image = document.createElement('img');
            image.src = img.image_path;
            image.classList.add('img-thumbnail', 'shadow-sm');
            image.style.width = '100px';
            image.style.height = '100px';
            image.style.objectFit = 'cover';
            image.alt = img.name || 'Imagen del producto';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '<span aria-hidden="true">&times;</span>';
            btn.classList.add('btn', 'btn-danger', 'btn-sm', 'rounded-circle');
            btn.style.position = 'absolute';
            btn.style.top = '-8px';
            btn.style.right = '-8px';
            btn.style.width = '28px';
            btn.style.height = '28px';
            btn.setAttribute('aria-label', 'Eliminar imagen');
            btn.addEventListener('click', () => {
              imagesToDelete.push(img.id);
              wrapper.remove();
              if (existingImagesContainer.children.length === 0) {
                existingImagesContainer.innerHTML = '<span class="text-muted">No hay imágenes guardadas.</span>';
              }
            });

            wrapper.appendChild(image);
            wrapper.appendChild(btn);
            existingImagesContainer.appendChild(wrapper);
          });
        }
      })
      .catch(err => {
        console.error(err);
      });
  }

  // Preview nuevas imágenes
  newImagesInput.addEventListener('change', function() {
    selectedFiles = Array.from(this.files);
    updatePreview();
  });

  function updatePreview() {
    preview.innerHTML = '';
    if (selectedFiles.length === 0) {
      preview.innerHTML = '<span class="text-muted">No se han seleccionado imágenes.</span>';
      return;
    }
    selectedFiles.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function(e) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('position-relative');
        wrapper.style.width = '100px';

        const img = document.createElement('img');
        img.src = e.target.result;
        img.classList.add('img-thumbnail', 'shadow-sm');
        img.style.width = '100px';
        img.style.height = '100px';
        img.style.objectFit = 'cover';
        img.alt = file.name;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerHTML = '<span aria-hidden="true">&times;</span>';
        btn.classList.add('btn', 'btn-danger', 'btn-sm', 'rounded-circle');
        btn.style.position = 'absolute';
        btn.style.top = '-8px';
        btn.style.right = '-8px';
        btn.style.width = '28px';
        btn.style.height = '28px';
        btn.setAttribute('aria-label', 'Eliminar imagen');
        btn.addEventListener('click', () => {
          selectedFiles.splice(index, 1);
          updatePreview();
          // Reset input when cleared
          if (selectedFiles.length === 0) newImagesInput.value = null;
        });

        wrapper.appendChild(img);
        wrapper.appendChild(btn);
        preview.appendChild(wrapper);
      };
      reader.readAsDataURL(file);
    });
  }

  // Validación y envío por AJAX
 function updateProduct() {

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const formData = new FormData(form);

    formData.append('_method', 'PUT'); // Laravel lo convierte en PUT

    selectedFiles.forEach(file => formData.append('images[]', file));
    imagesToDelete.forEach(id => formData.append('delete_images[]', id));

    fetch(`/api/products/${productId}`, {
        method: 'POST',
        body: formData,
        headers: authHeaders // SOLO si NO incluyes Content-Type
    })
    .then(res => res.json())
    .then(data => {
        showSuccess(data.message || 'Producto actualizado correctamente.');
        form.classList.remove('was-validated');
        preview.innerHTML = '';
        existingImagesContainer.innerHTML = '';
        selectedFiles = [];
        imagesToDelete = [];
    })
    .catch(err => {
        console.error(err);
        showError('No se pudo actualizar el producto. Intente nuevamente.');
    });
}
});
</script>

<script>
function logout() {
    localStorage.removeItem("token");
    window.location.href = "/";
}
</script>