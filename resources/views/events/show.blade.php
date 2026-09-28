@extends('layouts.app')

@section('title', 'Detail Event — Pulse Arena')

@section('content')
    <a href="{{ route('events.index') }}" class="muted">← Kembali ke daftar</a>

    <div class="row g-4 mt-1">
        <div class="col-lg-7">
            <div class="panel" id="info" data-aos="fade-up">
                <p class="muted mb-0">Memuat event...</p>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="panel" data-aos="fade-up">
                <div class="section-label">PESAN_TIKET</div>
                <div id="notice"></div>
                <form id="booking-form" novalidate>
                    <div class="mb-3">
                        <label for="buyer_name">Nama lengkap</label>
                        <input class="form-control" id="buyer_name" name="buyer_name">
                        <div class="field-error" data-error="buyer_name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="buyer_email">Email</label>
                        <input class="form-control" id="buyer_email" name="buyer_email" type="email">
                        <div class="field-error" data-error="buyer_email"></div>
                    </div>
                    <button class="btn-pulse w-100" id="submit-btn" type="submit">Pesan tiket</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const eventId = {{ (int) $id }};
    const info = document.getElementById('info');
    const form = document.getElementById('booking-form');
    const notice = document.getElementById('notice');
    const button = document.getElementById('submit-btn');

    function showNotice(type, text) {
        notice.innerHTML = `<div class="alert-pulse ${type}">${esc(text)}</div>`;
    }

    function clearErrors() {
        notice.innerHTML = '';
        form.querySelectorAll('[data-error]').forEach((el) => (el.textContent = ''));
    }

    async function loadEvent() {
        try {
            const { data: e } = await api('/events/' + eventId);
            const remaining = e.quota - e.tickets_count;
            const expired = new Date(e.event_date) < new Date();

            info.innerHTML = `
                <span class="event-tag">${esc(e.division)}</span>
                <h1 class="h3 fw-bold mb-3">${esc(e.name)}</h1>
                <p class="muted">${esc(e.description ?? 'Belum ada deskripsi.')}</p>
                <div class="spec-row"><span>Tanggal</span><span>${tanggal(e.event_date)}</span></div>
                <div class="spec-row"><span>Lokasi</span><span>${esc(e.location)}</span></div>
                <div class="spec-row"><span>Harga</span><span>${rupiah(e.price)}</span></div>
                <div class="spec-row"><span>Sisa tiket</span><span>${Math.max(remaining, 0)} dari ${e.quota}</span></div>`;

            button.disabled = expired || remaining <= 0;
            button.textContent = expired ? 'Event kedaluwarsa' : (remaining <= 0 ? 'Tiket habis' : 'Pesan tiket');
        } catch (err) {
            info.innerHTML = `<p class="field-error mb-0">${err.status === 404 ? 'Event tidak ditemukan.' : esc(err.message)}</p>`;
            button.disabled = true;
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();
        button.disabled = true;

        try {
            const { data } = await api(`/events/${eventId}/tickets`, {
                method: 'POST',
                body: JSON.stringify({
                    buyer_name: form.buyer_name.value,
                    buyer_email: form.buyer_email.value,
                }),
            });

            showNotice('alert-ok', 'Tiket berhasil dipesan. Kode tiket: ' + data.ticket_code);
            form.reset();
        } catch (err) {
            const fields = Object.entries(err.errors);

            fields.forEach(([field, messages]) => {
                const target = form.querySelector(`[data-error="${field}"]`);
                if (target) target.textContent = messages[0];
            });

            if (!fields.length) showNotice('alert-bad', err.message);
        } finally {
            await loadEvent();
        }
    });

    loadEvent();
</script>
@endpush