{{--
    Thread pesan konsultasi per pengajuan.
    Variabel yang wajib dikirim: $pengajuan (dengan relasi pesan->pengirim
    yang sudah diurutkan kronologis) dan $routePesan (URL POST kirim pesan).
    Pesan sendiri rata kanan warna biru tua; pesan lawan rata kiri abu muda.
    Mengirim pesan tidak mengubah status pengajuan.
--}}
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-chat-dots"></i> Pesan / Balasan</div>
    <div class="card-body">
        <p class="text-muted small mb-3">Balasan langsung di sini — guru BK dan siswa bisa saling membalas tanpa menunggu catatan sesi selesai. Mengirim pesan tidak mengubah status pengajuan.</p>
        @if($pengajuan->pesan->isEmpty())
            <p class="text-muted mb-3">Belum ada pesan. Mulai percakapan dengan menulis pesan di bawah.</p>
        @else
            <div class="d-flex flex-column gap-2 mb-3">
                @foreach($pengajuan->pesan as $pesan)
                    @php $milikSaya = $pesan->pengirim_id === auth()->id(); @endphp
                    <div class="d-flex {{ $milikSaya ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-3 rounded-3 {{ $milikSaya ? 'text-white' : '' }}" style="{{ $milikSaya ? 'background: var(--bk-navy); max-width: 85%;' : 'background: #f4f8fb; border: 1px solid #dde5ec; max-width: 85%;' }}">
                            <div class="small fw-bold mb-1">{{ $pesan->pengirim->name ?? '-' }} &middot; {{ $pesan->labelPengirim() }}</div>
                            <div style="white-space: pre-wrap; word-wrap: break-word;">{{ $pesan->isi }}</div>
                            <div class="small mt-1 {{ $milikSaya ? 'text-white-50' : 'text-muted' }}">{{ $pesan->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ $routePesan }}">
            @csrf
            <div class="mb-2">
                <label class="form-label" for="isi_pesan">Tulis pesan</label>
                <textarea class="form-control @error('isi') is-invalid @enderror" id="isi_pesan" name="isi" rows="3" maxlength="2000" placeholder="Tulis balasan di sini (maksimal 2000 karakter)..." required>{{ old('isi') }}</textarea>
                @error('isi')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i> Kirim</button>
            </div>
        </form>
    </div>
</div>
