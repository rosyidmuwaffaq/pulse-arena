@extends('layouts.admin')

@section('title', 'Admin Event — Pulse Arena')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <div class="section-label">ADMIN_PANEL</div>
            <h1 class="h3 fw-bold mb-1">Kelola Event</h1>
            <p class="muted mb-0">Tambah, edit, dan hapus event Pulse Arena.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn-pulse">+ Tambah Event</a>
    </div>

    <div id="notice" aria-live="polite"></div>

    <div class="row g-3 mb-4" id="ranking">
        <div class="col-12 muted">Memuat statistik event...</div>
    </div>

    <div class="panel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h2 class="h5 fw-bold mb-0">Daftar Event</h2>
            <button type="button" class="btn btn-outline-light btn-sm" id="refresh-btn">Muat ulang</button>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Tanggal</th>
                        <th>Harga</th>
                        <th>Tiket</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="event-list">
                    <tr><td colspan="5" class="muted py-4">Memuat daftar event...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const eventList = document.getElementById('event-list');
    const notice = document.getElementById('notice');
    const ranking = document.getElementById('ranking');

    function showNotice(message, bad = false) {
        notice.innerHTML = `<div class="alert-pulse ${bad ? 'alert-bad' : 'alert-ok'}">${esc(message)}</div>`;
    }

    async function loadRanking() {
        try {
            const { data } = await api('/events/ranking');
            const card = (title, event) => `
                <div class="col-md-6">
                    <div class="panel h-100">
                        <div class="section-label">${title}</div>
                        ${event ? `<div class="fw-bold">${esc(event.name)}</div><div class="muted small">${Number(event.tickets_count ?? 0)} tiket dipesan</div>` : '<div class="muted">Belum ada event.</div>'}
                    </div>
                </div>`;
            ranking.innerHTML = card('TIKET_TERBANYAK', data.most) + card('TIKET_TERENDAH', data.least);
        } catch (err) {
            ranking.innerHTML = `<div class="col-12 field-error">Statistik tidak dapat dimuat: ${esc(err.message)}</div>`;
        }
    }

    async function loadEvents() {
        eventList.innerHTML = '<tr><td colspan="5" class="muted py-4">Memuat daftar event...</td></tr>';
        try {
            const { data } = await api('/events');
            if (!data.length) {
                eventList.innerHTML = '<tr><td colspan="5" class="muted py-4">Belum ada event.</td></tr>';
                return;
            }

            eventList.innerHTML = data.map((e) => `
                <tr>
                    <td>
                        <div class="event-tag mb-2">${esc(e.division)}</div>
                        <div class="fw-semibold">${esc(e.name)}</div>
                        <div class="muted small">${esc(e.location)}</div>
                    </td>
                    <td class="small">${tanggal(e.event_date)}</td>
                    <td class="small">${rupiah(e.price)}</td>
                    <td class="small">${Number(e.tickets_count ?? 0)} / ${Number(e.quota)}</td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-light mb-1" href="/admin/events/${e.id}/edit">Edit</a>
                        <button class="btn btn-sm btn-danger mb-1" type="button" data-delete-id="${e.id}" data-event-name="${esc(e.name)}">Hapus</button>
                    </td>
                </tr>`).join('');
        } catch (err) {
            eventList.innerHTML = `<tr><td colspan="5" class="field-error py-4">${esc(err.message)}</td></tr>`;
        }
    }

    eventList.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-delete-id]');
        if (!button) return;

        const id = button.dataset.deleteId;
        const name = button.dataset.eventName;
        if (!confirm(`Hapus event "${name}"? Event yang sudah memiliki tiket tidak dapat dihapus.`)) return;

        button.disabled = true;
        try {
            await api('/events/' + id, { method: 'DELETE' });
            showNotice('Event berhasil dihapus.');
            await Promise.all([loadEvents(), loadRanking()]);
        } catch (err) {
            showNotice(err.message, true);
            button.disabled = false;
        }
    });

    document.getElementById('refresh-btn').addEventListener('click', () => {
        Promise.all([loadEvents(), loadRanking()]);
    });

    loadEvents();
    loadRanking();
</script>
@endpush
