@extends('layouts.app')

@section('title', 'Tiket Saya — Pulse Arena')

@section('content')
    <div class="section-label">DAFTAR_TIKET</div>
    <h1 class="h3 fw-bold mb-3">Tiket yang sudah dipesan</h1>

    <form id="search-form" class="panel mb-4 d-flex gap-2 flex-wrap" data-aos="fade-up">
        <input class="form-control flex-grow-1" id="email" type="email" placeholder="Filter berdasarkan email pemesan" style="min-width: 240px;">
        <select class="form-select" id="status" style="max-width: 190px;">
            <option value="">Semua status</option>
            <option value="valid">Valid</option>
            <option value="used">Digunakan</option>
            <option value="cancelled">Dibatalkan</option>
        </select>
        <button class="btn-pulse" type="submit">Terapkan filter</button>
        <button class="btn btn-outline-light" id="reset-filter" type="button">Reset</button>
    </form>

    <div id="ticket-count" class="muted small mb-3"></div>
    <div id="ticket-list"><p class="muted">Memuat daftar tiket...</p></div>
@endsection

@push('scripts')
<script>
    const list = document.getElementById('ticket-list');
    const count = document.getElementById('ticket-count');
    const form = document.getElementById('search-form');

    async function loadTickets() {
        list.innerHTML = '<p class="muted">Memuat tiket...</p>';
        count.textContent = '';
        const params = new URLSearchParams();
        const email = document.getElementById('email').value.trim();
        const status = document.getElementById('status').value;
        if (email) params.set('email', email);
        if (status) params.set('status', status);

        try {
            const query = params.toString();
            const { data } = await api('/tickets' + (query ? '?' + query : ''));
            if (!data.length) {
                list.innerHTML = '<div class="panel muted">Tidak ada tiket yang sesuai dengan filter.</div>';
                return;
            }
            count.textContent = `${data.length} tiket ditemukan`;
            list.innerHTML = data.map((t) => `
                <div class="ticket-row mb-3">
                    <div class="ticket-stub">${esc(t.ticket_code)}</div>
                    <div class="ticket-info">
                        <div>
                            <strong>${esc(t.event?.name ?? 'Event tidak tersedia')}</strong>
                            <div class="muted small">${t.event ? tanggal(t.event.event_date) + ' · ' + esc(t.event.location) : ''}</div>
                            <div class="muted small">Atas nama ${esc(t.buyer_name)}</div>
                        </div>
                        <span class="badge-status">${esc(t.status)}</span>
                    </div>
                </div>`).join('');
        } catch (err) {
            list.innerHTML = `<div class="panel field-error">${esc(err.message)}</div>`;
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        loadTickets();
    });
    document.getElementById('reset-filter').addEventListener('click', () => {
        form.reset();
        loadTickets();
    });
    loadTickets();
</script>
@endpush
