@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="login-wrap">
    <div class="card login-card shadow">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <span class="brand-mark" style="width:72px;height:72px;"><i class="bi bi-mortarboard-fill" style="font-size:2rem;"></i></span>
                <h1 class="h4 fw-bold mt-3 mb-1" style="color:var(--bk-navy);">Sistem Informasi Bimbingan dan Konseling</h1>
                <p class="text-muted mb-1 fw-semibold">SMAN 1 Sindangkerta</p>
                <p class="text-muted small mb-0">Login sesuai peran: Siswa, Guru BK, atau Admin.</p>
            </div>
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email"><i class="bi bi-envelope"></i> Email</label>
                    <input class="form-control form-control-lg" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password"><i class="bi bi-key"></i> Password</label>
                    <input class="form-control form-control-lg" type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <button class="btn btn-primary w-100 btn-lg" type="submit"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
            </form>
            <div class="alert alert-light border small mt-4 mb-0">
                <i class="bi bi-info-circle"></i> <strong>Akun demo:</strong> admin@bk.demo, guru1@bk.demo, siswa1@bk.demo (password demo tercantum di README.md).
                Ganti password demo sebelum dipakai di lingkungan sekolah yang sebenarnya.
            </div>
        </div>
    </div>
    <p class="text-center text-muted small mt-3"><i class="bi bi-shield-lock"></i> Data konsultasi siswa bersifat rahasia.</p>
</div>
@endsection
