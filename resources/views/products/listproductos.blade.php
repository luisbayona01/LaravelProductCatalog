@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="text-white mb-1 fw-bold">📦 Catálogo de Productos</h1>
                <p class="text-white-50 mb-0">Gestiona tu inventario de productos</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/product/register" class="btn btn-light btn-lg fw-bold shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle me-2" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Agregar Producto
                </a>
           
            </div>
        </div>

        <div id="productGrid" class="row g-4">
            <!-- Los productos se cargarán aquí -->
            <div class="col-12 text-center">
                <div class="spinner-border text-white" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-white mt-3">Cargando productos...</p>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .product-actions {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 15;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .action-btn.edit {
        background: #667eea;
        color: white;
    }

    .action-btn.edit:hover {
        background: #5568d3;
        transform: scale(1.1);
    }

    .action-btn.delete {
        background: #ff6b6b;
        color: white;
    }

    .action-btn.delete:hover {
        background: #ff5252;
        transform: scale(1.1);
    }

    .carousel-inner {
        border-radius: 15px 15px 0 0;
        background: #f8f9fa;
        aspect-ratio: 1 / 1;
    }

    .carousel-item {
        height: 100%;
    }

    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: auto;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
    }

    .carousel-control-prev {
        left: 10px;
    }

    .carousel-control-next {
        right: 10px;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        background: rgba(0, 0, 0, 0.6);
    }

    .badge-count {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #667eea;
        color: white;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: bold;
        z-index: 10;
    }

    .product-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-name {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
        min-height: 50px;
    }

    .product-description {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 10px;
        flex-grow: 1;
        overflow: hidden;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #eee;
        margin-top: auto;
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: bold;
        color: #667eea;
    }

    .product-stock {
        font-size: 0.9rem;
        color: #999;
    }

    .product-stock.low {
        color: #ff6b6b;
        font-weight: bold;
    }

    .carousel-indicators {
        bottom: -30px;
    }

    .carousel-indicators button {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ccc;
    }

    .carousel-indicators .active {
        background-color: #667eea;
    }

    .no-products {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .no-products-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #ccc;
    }
</style>

<script>
function isTokenExpired(token) {
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.exp < Math.floor(Date.now() / 1000);
    } catch {
        return true;
    }
}

const token = localStorage.getItem("token");

if (token && isTokenExpired(token)) {
    localStorage.removeItem("token");
    window.location.href = "/login";
}

// Mostrar/ocultar elementos según autenticación
if (token) {
    document.querySelectorAll(".auth-only").forEach(el => el.classList.remove("d-none"));
    document.querySelectorAll(".no-auth").forEach(el => el.classList.add("d-none"));
} else {
    document.querySelectorAll(".auth-only").forEach(el => el.classList.add("d-none"));
    document.querySelectorAll(".no-auth").forEach(el => el.classList.remove("d-none"));
}

async function loadProducts() {
    
    const token = localStorage.getItem('token');
    

    if (!token) {
        document.getElementById('productGrid').innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <strong>Error:</strong> No hay token de autenticación. Por favor, inicia sesión.
                </div>
            </div>
        `;
        return;
    }

    try {
        const response = await fetch('/api/products', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        const products = data.data || data || [];

        if (products.length === 0) {
            document.getElementById('productGrid').innerHTML = `
                <div class="col-12">
                    <div class="no-products">
                        <div class="no-products-icon">📦</div>
                        <h3>No hay productos</h3>
                        <p>No hay productos disponibles en este momento. <a href="/product/register" class="text-decoration-none">Agrega uno ahora</a></p>
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        products.forEach((product, index) => {
            const images = product.product_images || [];
            const imageCount = images.length;
            const defaultImage = images.length > 0 ? images[0].image_path : 'https://via.placeholder.com/400?text=Sin+imagen';
            const stockClass = product.stock < 5 ? 'low' : '';
            const carouselId = `carousel-${product.id}`;

            html += `
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="product-card">
                        <!-- Botones de acción -->
                        <div class="product-actions">
                            <a href="/product/edit/${product.id}" class="action-btn edit" title="Editar producto">
                                ✏️
                            </a>
                            <button type="button" class="action-btn delete" onclick="deleteProduct(${product.id})" title="Eliminar producto">
                                🗑️
                            </button>
                        </div>

                        <!-- Carrusel de imágenes -->
                        <div id="${carouselId}" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                            <div class="carousel-inner">
                                ${images.map((img, idx) => `
                                    <div class="carousel-item ${idx === 0 ? 'active' : ''}">
                                        <img src="${img.image_path}" alt="${product.name}" class="d-block w-100">
                                    </div>
                                `).join('')}
                                ${images.length === 0 ? `
                                    <div class="carousel-item active">
                                        <img src="${defaultImage}" alt="${product.name}" class="d-block w-100">
                                    </div>
                                ` : ''}
                            </div>

                            ${images.length > 1 ? `
                                <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </button>
                            ` : ''}

                            ${images.length > 1 ? `
                                <div class="carousel-indicators">
                                    ${images.map((img, idx) => `
                                        <button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${idx}" 
                                                class="${idx === 0 ? 'active' : ''}" aria-current="${idx === 0 ? 'true' : 'false'}"></button>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>

                        <!-- Badge de cantidad de imágenes -->
                        <div class="badge-count">
                            📷 ${imageCount} ${imageCount === 1 ? 'imagen' : 'imágenes'}
                        </div>

                        <!-- Información del producto -->
                        <div class="product-info">
                            <h5 class="product-name">${product.name}</h5>
                            <p class="product-description">${product.description || 'Sin descripción'}</p>
                            
                            <div class="product-meta">
                                <div>
                                    <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
                                    <div class="product-stock ${stockClass}">
                                        Stock: ${product.stock} ${product.stock < 5 ? '⚠️ Bajo' : ''}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-${product.status === 1 ? 'success' : 'secondary'}">
                                        ${product.status === 1 ? 'Activo' : 'Inactivo'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        document.getElementById('productGrid').innerHTML = html;

    } catch (error) {
        console.error('Error cargando productos:', error);
        document.getElementById('productGrid').innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <strong>Error:</strong> No se pudieron cargar los productos. ${error.message}
                </div>
            </div>
        `;
    }
}

// Cargar productos cuando el documento esté listo
document.addEventListener('DOMContentLoaded', loadProducts);

async function deleteProduct(productId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
        return;
    }

    const token = localStorage.getItem('token');
    if (!token) {
        alert('No hay token de autenticación');
        return;
    }

    try {
        const response = await fetch(`http://localhost:8000/api/products/${productId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            // Mostrar mensaje de éxito
            showNotification('Producto eliminado correctamente', 'success');
            // Recargar los productos
            setTimeout(() => loadProducts(), 1000);
        } else {
            const data = await response.json();
            showNotification(data.message || 'Error al eliminar el producto', 'error');
        }
    } catch (error) {
        console.error('Error eliminando producto:', error);
        showNotification('Error de conexión al eliminar', 'error');
    }
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Agregar estilos de animación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Cargar productos cuando el documento esté listo
document.addEventListener('DOMContentLoaded', loadProducts);

function logout() {
    localStorage.removeItem("token");
    window.location.href = "/";
}

</script>

@endsection