@extends('layouts.app')

@section('title', 'Tambah Event — Pulse Arena')

@section('content')
    <div class="section-label">TAMBAH_EVENT</div>

    <div class="panel" style="max-width: 680px;" data-aos="fade-up">
        <div id="notice"></div>
        <form id="event-form" novalidate>
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label for="name">Nama event</label>
                    <input class="form-control" id="name" name="name">
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
                    <input class="form-control" id="event_date" name="event_date" type="datetime-local">
                    <div class="field-error" data-error="event_date"></div>
                </div>
                <div class="col-md-6">
                    <label for="location">Lokasi</label>
                    <input class="form-control" id="location" name="location">
                    <div class="field-error" data-error="location"></div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="price">Harga tiket (Rp)</label>
                    <input class="form-control" id="price" name="price" type="number">
                    <div class="field-error" data-error="price"></div>
                </div>
                <div class="col-md-6">
                    <label for="quota">Kuota tiket</label>
                    <input class="form-control" id="quota" name="quota" type="number">
                    <div class="field-error" data-error="quota"></div>
                </div>
            </div>
            <div class="mb-4">
                <label for="description">Deskripsi singkat</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                <div class="field-error" data-error="description"></div>
            </div>
            <button class="btn-pulse" id="submit-btn" type="submit">Simpan event</button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('event-form');
    const notice = document.getElementById('notice');
    const button = document.getElementById('submit-btn');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        notice.innerHTML = '';
        form.querySelectorAll('[data-error]').forEach((el) => (el.textContent = ''));
        button.disabled = true;

        try {
            await api('/events', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(new FormData(form))),
            });

            window.location.href = '/';
        } catch (err) {
            const fields = Object.entries(err.errors);

            fields.forEach(([field, messages]) => {
                const target = form.querySelector(`[data-error="${field}"]`);
                if (target) target.textContent = messages[0];
            });

            if (!fields.length) {
                notice.innerHTML = `<div class="alert-pulse alert-bad">${esc(err.message)}</div>`;
            }

            button.disabled = false;
        }
    });
</script>
@endpush