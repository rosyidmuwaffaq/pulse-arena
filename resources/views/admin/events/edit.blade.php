@extends('layouts.admin')

@section('title', 'Edit Event — Pulse Arena')

@section('content')
    <a href="{{ route('admin.events.index') }}" class="muted">← Kembali ke Admin</a>
    <div class="section-label mt-4">EDIT_EVENT</div>

    <div class="panel" style="max-width: 820px;" data-aos="fade-up">
        <div id="notice"></div>
        <form id="event-form" novalidate>
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label for="name">Nama event</label>
                    <input class="form-control" id="name" name="name" required>
                    <div class="field-error" data-error="name"></div>
                </div>
                <div class="col-md-4">
                    <label for="division">Divisi</label>
                    <select class="form-select" id="division" name="division">
                        <option>Combat Robot</option>
                        <option>Line Follower</option>
                        <option>Drone Racing</option>
                        <option>Robo-Soccer</option>
                    </select>
                    <div class="field-error" data-error="division"></div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="event_date">Tanggal dan jam</label>
                    <input class="form-control" id="event_date" name="event_date" type="datetime-local" required>
                    <div class="field-error" data-error="event_date"></div>
                </div>
                <div class="col-md-6">
                    <label for="location">Lokasi</label>
                    <input class="form-control" id="location" name="location" required>
                    <div class="field-error" data-error="location"></div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="price">Harga tiket (Rp)</label>
                    <input class="form-control" id="price" name="price" type="number" min="1" required>
                    <div class="field-error" data-error="price"></div>
                </div>
                <div class="col-md-6">
                    <label for="quota">Kuota tiket</label>
                    <input class="form-control" id="quota" name="quota" type="number" min="1" required>
                    <div class="field-error" data-error="quota"></div>
                </div>
            </div>
            <div class="mb-4">
                <label for="description">Deskripsi singkat</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                <div class="field-error" data-error="description"></div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn-pulse" id="submit-btn" type="submit" disabled>Simpan perubahan</button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-light px-4 py-2">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const eventId = {{ (int) $id }};
    const form = document.getElementById('event-form');
    const notice = document.getElementById('notice');
    const button = document.getElementById('submit-btn');

    function showNotice(message, bad = false) {
        notice.innerHTML = `<div class="alert-pulse ${bad ? 'alert-bad' : 'alert-ok'}">${esc(message)}</div>`;
    }

    function toLocalDateTime(value) {
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';
        const local = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
        return local.toISOString().slice(0, 16);
    }

    async function loadEvent() {
        try {
            const { data: event } = await api('/events/' + eventId);
            for (const field of ['name', 'division', 'event_date', 'location', 'price', 'quota', 'description']) {
                const input = form.elements[field];
                if (!input) continue;
                input.value = field === 'event_date' ? toLocalDateTime(event[field]) : (event[field] ?? '');
            }
            button.disabled = false;
        } catch (err) {
            showNotice(err.status === 404 ? 'Event tidak ditemukan.' : err.message, true);
            form.querySelectorAll('input, select, textarea, button').forEach((el) => el.disabled = true);
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        notice.innerHTML = '';
        form.querySelectorAll('[data-error]').forEach((el) => el.textContent = '');
        button.disabled = true;

        try {
            const payload = Object.fromEntries(new FormData(form));
            payload.price = Number(payload.price);
            payload.quota = Number(payload.quota);
            await api('/events/' + eventId, {
                method: 'PUT',
                body: JSON.stringify(payload),
            });
            window.location.href = '/admin/events';
        } catch (err) {
            const fields = Object.entries(err.errors ?? {});
            fields.forEach(([field, messages]) => {
                const target = form.querySelector(`[data-error="${field}"]`);
                if (target) target.textContent = messages[0];
            });
            if (!fields.length) showNotice(err.message, true);
            button.disabled = false;
        }
    });

    loadEvent();
</script>
@endpush
