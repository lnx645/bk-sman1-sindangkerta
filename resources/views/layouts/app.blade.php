<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Sistem Informasi Bimbingan dan Konseling SMAN 1 Sindangkerta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bk-navy: #14375a;
            --bk-navy-2: #1b4f72;
            --bk-blue: #2e7cb6;
            --bk-gold: #c9a227;
            --bk-gold-soft: #f7ecd0;
            --bk-bg: #eef2f6;
            --bk-card: #ffffff;
            --bk-text: #1f2d3d;
            --bk-muted: #5b6b7d;
        }
        body { background: var(--bk-bg); color: var(--bk-text); font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; }
        /* Palet konsisten: tombol & aksen utama memakai biru tua, bukan biru bawaan acak. */
        .btn-primary, .bg-primary { background-color: var(--bk-navy) !important; border-color: var(--bk-navy) !important; }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--bk-navy-2) !important; border-color: var(--bk-navy-2) !important; }
        .btn-outline-primary { color: var(--bk-navy); border-color: var(--bk-navy); }
        .btn-outline-primary:hover { background-color: var(--bk-navy); border-color: var(--bk-navy); color: #fff; }
        .text-primary { color: var(--bk-navy) !important; }
        .bk-navbar { background: linear-gradient(90deg, #0f2d4d, var(--bk-navy-2)); box-shadow: 0 2px 12px rgba(15, 45, 77, .25); padding-top: .4rem; padding-bottom: .4rem; }
        /* Navbar SATU BARIS di semua lebar: container tidak boleh wrap, brand terpotong
           rapi (ellipsis) bila sempit, tombol tidak pernah jatuh ke baris kedua. */
        .bk-navbar .container-fluid { flex-wrap: nowrap !important; gap: .5rem; padding-left: .75rem; padding-right: .75rem; }
        .bk-navbar .navbar-brand { flex: 1 1 auto; min-width: 0; margin-right: 0; }
        .bk-navbar .navbar-toggler { flex: 0 0 auto; padding: .4rem .55rem; min-height: 44px; }
        .bk-navbar .navbar-actions { flex: 0 0 auto; }
        .brand-mark { width: 36px; height: 36px; border-radius: 50%; background: var(--bk-gold); color: #14375a; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; }
        /* Logo sekolah: kotak kecil ber-radius kecil (BUKAN lingkaran penuh) supaya
           lambang segi lima tidak terpotong ujungnya; rasio aspek dijaga
           object-fit: contain sehingga logo tidak pernah gepeng. */
        .brand-logo { width: 40px; height: 40px; border-radius: .4rem; object-fit: contain; background: #fff; padding: 2px; flex: 0 0 auto; }
        .brand-title { min-width: 0; overflow: hidden; }
        .brand-title .brand-line { display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .brand-subtitle { font-size: .8rem; color: #d7e3f0; }
        .user-chip { background: rgba(255, 255, 255, .14); color: #fff; border: 1px solid rgba(255, 255, 255, .25); border-radius: 999px; padding: .35rem .8rem; }
        .sidebar { min-height: calc(100vh - 56px); }
        .sidebar .nav-link { color: #334155; border-radius: .5rem; padding: .55rem .75rem; font-weight: 500; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background: #dcebf7; color: var(--bk-navy); }
        .sidebar .nav-link.active { border-left: 4px solid var(--bk-gold); }
        .bk-mobile-nav { background: #fff; border-bottom: 1px solid #dde5ec; }
        .bk-mobile-nav a, .bk-mobile-nav .mobile-logout { color: var(--bk-navy); font-weight: 600; padding: .8rem 1rem; display: block; text-decoration: none; border-bottom: 1px solid #eef2f6; min-height: 44px; }
        .bk-mobile-nav .mobile-logout { background: none; border: none; border-bottom: 1px solid #eef2f6; width: 100%; text-align: left; font-size: 1rem; }
        .bk-mobile-nav a.active { background: #dcebf7; border-left: 4px solid var(--bk-gold); }
        .bk-mobile-nav .mobile-user { padding: .8rem 1rem; color: var(--bk-muted); font-size: .9rem; }
        /* Tabel: scroll hanya di dalam wadahnya, halaman tidak pernah meluber. */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive .table { min-width: 0; }
        .navbar-brand { min-width: 0; }
        .navbar-brand .brand-title { white-space: normal; line-height: 1.15; }
        .card { border: 1px solid #dde5ec; border-radius: .9rem; box-shadow: 0 1px 4px rgba(20, 55, 90, .06); }
        .card-header { background: #f4f8fb; border-bottom: 1px solid #dde5ec; font-weight: 700; color: var(--bk-navy); }
        .page-head { border-bottom: 3px solid var(--bk-gold); padding-bottom: .6rem; margin-bottom: 1.25rem; }
        .page-head h1 { color: var(--bk-navy); font-weight: 800; }
        .page-head p { color: var(--bk-muted); }
        .stat-card { border-top: 4px solid var(--bk-blue); }
        .stat-card.stat-gold { border-top-color: var(--bk-gold); }
        .stat-card.stat-green { border-top-color: #2e7d5b; }
        .stat-card .stat-icon { width: 46px; height: 46px; border-radius: .7rem; background: #e3eef8; color: var(--bk-navy); display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .stat-card .stat-value { font-size: 2rem; font-weight: 800; color: var(--bk-navy); line-height: 1; }
        .stat-card .stat-label { font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; color: var(--bk-muted); font-weight: 700; }
        main .table { background: #fff; }
        main .table thead th { background: var(--bk-navy); color: #fff; border: none; font-weight: 700; white-space: nowrap; }
        main .table tbody tr:hover { background: #eef5fb; }
        main .table td, main .table th { vertical-align: middle; }
        .form-label { font-weight: 600; color: var(--bk-navy); }
        .form-control, .form-select { border-radius: .6rem; }
        .form-control:focus, .form-select:focus { border-color: var(--bk-gold); box-shadow: 0 0 0 .25rem rgba(201, 162, 39, .25); }
        .btn:focus-visible, a:focus-visible, .form-control:focus-visible, .form-select:focus-visible { outline: 3px solid var(--bk-gold); outline-offset: 2px; }
        .alert { border-radius: .75rem; }
        .highlight-box { background: var(--bk-gold-soft); border: 1px solid #e3cf8a; border-left: 5px solid var(--bk-gold); border-radius: .75rem; padding: 1rem; }
        .highlight-box-green { background: #e7f4ee; border: 1px solid #a9d3c0; border-left: 5px solid #2e7d5b; border-radius: .75rem; padding: 1rem; }
        .badge-status { font-weight: 700; border: 1px solid transparent; padding: .4em .7em; }
        .badge-status-diajukan { background: #e8edf2; color: #334155; border-color: #cbd5e1; }
        .badge-status-ditinjau { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
        .badge-status-disetujui { background: #dcfce7; color: #166534; border-color: #86efac; }
        .badge-status-ditolak { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
        .badge-status-dijadwalkan_ulang { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .badge-status-selesai { background: #dcebf7; color: #14375a; border-color: #7fb3dd; }
        .badge-status-dibatalkan { background: #e2e8f0; color: #475569; border-color: #94a3b8; }
        .bk-footer { color: var(--bk-muted); font-size: .85rem; border-top: 1px solid #dde5ec; }
        .login-wrap { max-width: 520px; margin: 2.5rem auto; }
        .login-card { border-top: 6px solid var(--bk-gold); }
        .pagination .page-link { color: var(--bk-navy); }
        .pagination .page-item.active .page-link { background: var(--bk-navy); border-color: var(--bk-navy); }
        @media (max-width: 767px) {
            .btn { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: .4rem; }
            .form-actions { display: flex; flex-direction: column; gap: .5rem; }
            .form-actions .btn { width: 100%; }
            .login-wrap { margin: 1rem auto; }
            main .table { font-size: .9rem; }
            .btn-sm { min-height: 44px; }
            main.p-4 { padding: 1rem !important; }
            .stat-card .stat-value { font-size: 1.6rem; }
            .table-responsive { border-radius: .75rem; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bk-navbar sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            {{-- Logo sekolah: public/img/logo-sekolah.png, dikirim langsung oleh
                 user/pemesan (BUKAN diambil dari Google). Bila file itu hilang,
                 fallback-nya ikon topi wisuda. Jangan ganti dengan logo sekolah lain. --}}
            @if(file_exists(public_path('img/logo-sekolah.png')))
                <img class="brand-logo" src="{{ asset('img/logo-sekolah.png') }}" alt="Logo SMAN 1 Sindangkerta">
            @else
                <span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
            @endif
            <span class="brand-title">
                <span class="brand-line fw-bold d-sm-none">BK &middot; SMAN 1 Sindangkerta</span>
                <span class="brand-line lh-1 fw-bold d-none d-sm-block">Sistem Informasi Bimbingan dan Konseling</span>
                <span class="brand-subtitle d-none d-sm-block">SMAN 1 Sindangkerta</span>
            </span>
        </a>
        @auth
        {{-- Di HP hanya ada brand + hamburger dalam satu baris; tombol Keluar pindah
             ke dalam menu hamburger (di bawah), supaya navbar tidak membungkus ke
             baris kedua seperti sebelumnya. --}}
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#bkMobileNav" aria-controls="bkMobileNav" aria-expanded="false" aria-label="Buka/tutup menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        @endauth
        <div class="navbar-actions d-none d-md-flex align-items-center gap-2 text-white">
            @auth
                <span class="user-chip"><i class="bi bi-person-circle"></i> {{ auth()->user()->name }} · {{ auth()->user()->role }}</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
                    <button class="btn btn-outline-light btn-sm" type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

@auth
<div class="collapse d-md-none bk-mobile-nav" id="bkMobileNav">
    <div class="mobile-user"><i class="bi bi-person-circle"></i> {{ auth()->user()->name }} &middot; {{ auth()->user()->role }}</div>
    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="{{ request()->routeIs('profil') ? 'active' : '' }}" href="{{ route('profil') }}"><i class="bi bi-person"></i> Profil Saya</a>
    @if(auth()->user()->isSiswa())
        <a class="{{ request()->routeIs('siswa.pengajuan.index') ? 'active' : '' }}" href="{{ route('siswa.pengajuan.index') }}"><i class="bi bi-inbox"></i> Pengajuan Saya</a>
        <a class="{{ request()->routeIs('siswa.pengajuan.create') ? 'active' : '' }}" href="{{ route('siswa.pengajuan.create') }}"><i class="bi bi-plus-circle"></i> Ajukan Konsultasi</a>
    @endif
    @if(auth()->user()->isGuruBk())
        <a class="{{ request()->routeIs('guru.pengajuan.*') ? 'active' : '' }}" href="{{ route('guru.pengajuan.index') }}"><i class="bi bi-journal-text"></i> Daftar Permohonan</a>
        <a class="{{ request()->routeIs('guru.jadwal.*') ? 'active' : '' }}" href="{{ route('guru.jadwal.index') }}"><i class="bi bi-calendar-check"></i> Jadwal Konsultasi</a>
    @endif
    @if(auth()->user()->isAdmin())
        <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Kelola Akun</a>
        <a class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}"><i class="bi bi-building"></i> Kelola Kelas</a>
        <a class="{{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" href="{{ route('admin.kategori.index') }}"><i class="bi bi-tags"></i> Kelola Kategori</a>
        <a class="{{ request()->routeIs('admin.rekap.*') ? 'active' : '' }}" href="{{ route('admin.rekap.index') }}"><i class="bi bi-bar-chart"></i> Rekapitulasi</a>
    @endif
    <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
        <button class="mobile-logout" type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
    </form>
</div>
@endauth

<div class="container-fluid">
    <div class="row">
        @auth
        <aside class="col-md-3 col-lg-2 bg-white border-end sidebar p-3 d-none d-md-block">
            <div class="nav flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a class="nav-link {{ request()->routeIs('profil') ? 'active' : '' }}" href="{{ route('profil') }}"><i class="bi bi-person"></i> Profil Saya</a>
                @if(auth()->user()->isSiswa())
                    <hr>
                    <a class="nav-link {{ request()->routeIs('siswa.pengajuan.index') ? 'active' : '' }}" href="{{ route('siswa.pengajuan.index') }}"><i class="bi bi-inbox"></i> Pengajuan Saya</a>
                    <a class="nav-link {{ request()->routeIs('siswa.pengajuan.create') ? 'active' : '' }}" href="{{ route('siswa.pengajuan.create') }}"><i class="bi bi-plus-circle"></i> Ajukan Konsultasi</a>
                @endif
                @if(auth()->user()->isGuruBk())
                    <hr>
                    <a class="nav-link {{ request()->routeIs('guru.pengajuan.*') ? 'active' : '' }}" href="{{ route('guru.pengajuan.index') }}"><i class="bi bi-journal-text"></i> Daftar Permohonan</a>
                    <a class="nav-link {{ request()->routeIs('guru.jadwal.*') ? 'active' : '' }}" href="{{ route('guru.jadwal.index') }}"><i class="bi bi-calendar-check"></i> Jadwal Konsultasi</a>
                @endif
                @if(auth()->user()->isAdmin())
                    <hr>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Kelola Akun</a>
                    <a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}"><i class="bi bi-building"></i> Kelola Kelas</a>
                    <a class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" href="{{ route('admin.kategori.index') }}"><i class="bi bi-tags"></i> Kelola Kategori</a>
                    <a class="nav-link {{ request()->routeIs('admin.rekap.*') ? 'active' : '' }}" href="{{ route('admin.rekap.index') }}"><i class="bi bi-bar-chart"></i> Rekapitulasi</a>
                @endif
            </div>
            <div class="alert alert-info mt-4 small">
                <i class="bi bi-shield-lock"></i> Data konsultasi bersifat rahasia. Hanya siswa pemilik dan guru BK yang berwenang yang dapat melihat isi pengajuan.
            </div>
        </aside>
        @endauth
        <main class="@auth col-md-9 col-lg-10 @else col-12 @endauth p-4">
            @if(session('success'))
                <div class="alert alert-success" role="alert"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> Periksa kembali isian berikut:
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
            <footer class="bk-footer mt-5 pt-3 d-flex flex-column flex-sm-row justify-content-between gap-1">
                <span><i class="bi bi-mortarboard-fill"></i> Sistem Informasi Bimbingan dan Konseling — SMAN 1 Sindangkerta</span>
                <span><i class="bi bi-shield-lock"></i> Data konsultasi bersifat rahasia.</span>
            </footer>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
