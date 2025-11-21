@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 85vh; background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);">
    <div class="card shadow-sm" style="width: 100%; max-width: 520px; border-radius: 12px; overflow: hidden;">
        <div class="p-4 text-center text-white" style="background: linear-gradient(90deg,#0d6efd,#6610f2);">
            <h3 class="mb-0">Crear Cuenta</h3>
            <small class="d-block opacity-75">Empieza a gestionar tu catálogo de productos</small>
        </div>

        <div class="card-body p-4">
            <form id="registerForm" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16"><path d="M8 8a3 3 0 100-6 3 3 0 000 6z"/><path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 0 6 0 6 0 6 0z"/></svg>
                            </span>
                            <input id="name" name="name" type="text" class="form-control border-start-0" required>
                        </div>
                        <div class="invalid-feedback" id="error-name"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16"><path d="M0 4a2 2 0 012-2h12a2 2 0 012 2v.217l-8 4.8-8-4.8V4z"/><path d="M0 6.383V12a2 2 0 002 2h12a2 2 0 002-2V6.383l-7.555 4.533a1 1 0 01-1.11 0L0 6.383z"/></svg>
                            </span>
                            <input id="email" name="email" type="email" class="form-control border-start-0" required>
                        </div>
                        <div class="invalid-feedback" id="error-email"></div>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input id="password" name="password" type="password" class="form-control" required>
                        <button type="button" id="togglePassword" class="btn btn-outline-secondary">Mostrar</button>
                    </div>
                    <div class="invalid-feedback" id="error-password"></div>
                </div>

                <div class="mt-3">
                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    <div class="invalid-feedback" id="error-password_confirmation"></div>
                </div>

                <div class="d-grid mt-4">
                    <button id="submitBtn" type="submit" class="btn btn-primary btn-lg">
                        <span id="btnSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                        <span id="btnText">Registrarme</span>
                    </button>
                </div>
            </form>

            <div class="mt-3 text-center">
                <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
            </div>

            <div id="registerError" class="mt-3 alert alert-danger d-none" role="alert"></div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility (Bootstrap-friendly)
document.getElementById('togglePassword').addEventListener('click', function() {
    const pw = document.getElementById('password');
    const btn = this;
    if (pw.type === 'password') { pw.type = 'text'; btn.innerText = 'Ocultar'; }
    else { pw.type = 'password'; btn.innerText = 'Mostrar'; }
});

    const form = document.getElementById('registerForm');
const submitBtn = document.getElementById('submitBtn');
const btnSpinner = document.getElementById('btnSpinner');
const btnText = document.getElementById('btnText');

function clearErrors(){
    ['name','email','password','password_confirmation'].forEach(id => {
        const el = document.getElementById('error-'+id);
        if(el){ el.innerText = ''; el.classList.remove('d-block'); el.classList.add('d-none'); }
        const input = document.getElementById(id);
        if(input) input.classList.remove('is-invalid');
    });
    const global = document.getElementById('registerError');
    global.classList.add('d-none'); global.innerText = '';
}

    form.addEventListener('submit', async function(e){
        e.preventDefault();
        clearErrors();

        // Bootstrap client-side validation
        form.classList.add('was-validated');
        if (!form.checkValidity()) {
            return;
        }

        submitBtn.disabled = true;
        btnSpinner.classList.remove('d-none');
        btnText.innerText = 'Registrando...';

        const payload = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
        };

        try {
            const res = await fetch('http://localhost:8000/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (res.ok) {
                // Si la API devuelve token
                if (data.token) localStorage.setItem('token', data.token);
                window.location.href = '/dashboard';
                return;
            }

            // Mostrar errores de validación devueltos por backend
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const el = document.getElementById('error-'+key);
                    const input = document.getElementById(key);
                    if (input) input.classList.add('is-invalid');
                    if (el) { el.innerText = data.errors[key][0]; el.classList.remove('d-none'); el.classList.add('d-block'); }
                });
            }

            if (data.message) {
                const global = document.getElementById('registerError');
                global.innerText = data.message; global.classList.remove('d-none');
            }
        } catch (err) {
            const global = document.getElementById('registerError');
            global.innerText = 'Error de red. Intenta nuevamente.'; global.classList.remove('d-none');
        } finally {
            submitBtn.disabled = false;
            btnSpinner.classList.add('d-none');
            btnText.innerText = 'Registrarme';
        }
    });
</script>

@endsection
