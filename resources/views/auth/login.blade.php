
@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">

                <h3 class="text-center mb-4 fw-bold">Iniciar Sesión</h3>

                <form id="loginForm" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16"><path d="M0 4a2 2 0 012-2h12a2 2 0 012 2v.217l-8 4.8-8-4.8V4z"/><path d="M0 6.383V12a2 2 0 002 2h12a2 2 0 002-2V6.383l-7.555 4.533a1 1 0 01-1.11 0L0 6.383z"/></svg>
                            </span>
                            <input id="email" name="email" type="email" class="form-control border-start-0 form-control-lg" required>
                        </div>
                        <div class="invalid-feedback" id="error-email"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group">
                            <input id="password" name="password" type="password" class="form-control form-control-lg" required>
                        </div>
                        <div class="invalid-feedback" id="error-password"></div>
                    </div>

                    <div class="d-grid">
                        <button id="submitBtn" type="submit" class="btn btn-primary w-100 btn-lg">
                            <span id="btnSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                            <span id="btnText">Ingresar</span>
                        </button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('register') }}">¿No tienes cuenta? Regístrate</a>
                </div>

                <div id="loginError" class="alert alert-danger mt-3 d-none"></div>

            </div>
        </div>
    </div>
</div>

<script>
const form = document.getElementById('loginForm');
const submitBtn = document.getElementById('submitBtn');
const btnSpinner = document.getElementById('btnSpinner');
const btnText = document.getElementById('btnText');

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
function clearErrorsLogin(){
    ['email','password'].forEach(id => {
        const el = document.getElementById('error-'+id);
        if(el){ 
            el.innerText = ''; 
            el.classList.remove('d-block'); 
            el.classList.add('d-none'); 
        }
        const input = document.getElementById(id);
        if(input) input.classList.remove('is-invalid');
    });

    const global = document.getElementById('loginError');
    if(global){
        global.classList.add('d-none'); 
        global.innerText = '';
    }
}

form.addEventListener('submit', async function(e){
    e.preventDefault();
    clearErrorsLogin();
    form.classList.add('was-validated');
    if (!form.checkValidity()) return;

    submitBtn.disabled = true;
    btnSpinner.classList.remove('d-none');
    btnText.innerText = 'Ingresando...';

    const payload = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
    };

    try {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        console.log('Respuesta login:', data);

        if (res.ok && data.access_token) {
           
            localStorage.setItem('token', data.access_token);
            window.location.href = 'product/list';
            return;
        }

     
        if (data.errors) {
            Object.keys(data.errors).forEach(key => {
                const el = document.getElementById('error-'+key);
                const input = document.getElementById(key);
                if (input) input.classList.add('is-invalid');
                if (el) { 
                    el.innerText = data.errors[key][0]; 
                    el.classList.remove('d-none'); 
                    el.classList.add('d-block'); 
                }
            });
        }

      
        if (data.message) {
            const global = document.getElementById('loginError');
            global.innerText = data.message; 
            global.classList.remove('d-none');
        }

    } catch (err) {
        const global = document.getElementById('loginError');
        global.innerText = 'Error de red. Intenta nuevamente.'; 
        global.classList.remove('d-none');
    } finally {
        // Reset botón
        submitBtn.disabled = false;
        btnSpinner.classList.add('d-none');
        btnText.innerText = 'Ingresar';
    }
});
</script>

@endsection