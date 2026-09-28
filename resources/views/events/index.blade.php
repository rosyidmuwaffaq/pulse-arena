@extends('layouts.app')

@section('title', 'Tiket Saya — Pulse Arena')

@section('content')
    <div class="section-label">TIKET_SAYA</div>

    <form id="search-form" class="panel mb-4 d-flex gap-2 flex-wrap" data-aos="fade-up">
        <input class="form-control flex-grow-1" id="email" type="email" placeholder="Email yang dipakai saat memesan" style="min-width: 240px;">
        <button class="btn-pulse" type="submit">Cari tiket</button>
    </form>

    <div id="ticket-list">
        <p class="muted">Masukkan email untuk melihat tiket yang sudah dipesan.</p>
    </div>
@endsection

@push('scripts')
<script>
    const list = document.getElementById('ticket-list');

    document.getElementById('search-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const email = document.getElementById('email').value.trim();
        if (!email) return;

        list.innerHTML = '<p class="muted">Memuat tiket...</p>';

        try {
            const { data } = await api('/tickets?email=' + encodeURIComponent(email));

            if (!data.length) {
                list.innerHTML = '<p class="muted">Tidak ada tiket untuk email ini.</p>';
                return;
            }

            list.innerHTML = data.map((t) => `
                <div class="ticket-row">
                    <div class="ticket-stub">${esc(t.ticket_code)}</div>
                    <div class="ticket-info">
                        <div>
                            <strong>${esc(t.event.name)}</strong>
                            <div class="muted small">${tanggal(t.event.event_date)} · ${esc(t.event.location)}</div>
                            <div class="muted small">Atas nama ${esc(t.buyer_name)}</div>
                        </div>
                        <span class="badge-status">${esc(t.status)}</span>
                    </div>
                </div>`).join('');
        } catch (err) {
            list.innerHTML = `<p class="field-error">${esc(err.message)}</p>`;
        }
    });
</script>
@endpush