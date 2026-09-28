@extends('layouts.app')

@section('title', 'Daftar Event — Pulse Arena')

@section('content')
    <section class="hero" data-aos="fade-up">
        <h1>Kompetisi robotik, satu arena.</h1>
        <p>Combat robot, line follower, drone racing, dan robo-soccer. Pilih event dan amankan tiketmu.</p>
    </section>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="section-label">EVENT_LIST</div>

        <a href="{{ route('events.create') }}" class="btn-pulse">
            + Tambah Event
        </a>
    </div>

    <div class="row g-4" id="event-list">
        <div class="col-12 muted">Memuat event...</div>
    </div>
@endsection

@push('scripts')
<script>
    (async () => {
        const list = document.getElementById('event-list');

        try {
            const { data } = await api('/events');

            if (!data.length) {
                list.innerHTML = '<div class="col-12 muted">Belum ada event.</div>';
                return;
            }

            list.innerHTML = data.map((e) => {
                const remaining = e.quota - e.tickets_count;
                const expired = new Date(e.event_date) < new Date();
                const state = expired ? 'Kedaluwarsa' : (remaining <= 0 ? 'Habis' : remaining + ' sisa');
                const stateClass = expired || remaining <= 0 ? 'state-full' : '';

                return `
                    <div class="col-md-6 col-lg-4" data-aos="fade-up">
                        <a href="/events/${e.id}" class="event-card">
                            <span class="event-tag">${esc(e.division)}</span>
                            <h3>${esc(e.name)}</h3>
                            <div class="event-meta">${tanggal(e.event_date)} · ${esc(e.location)}</div>
                            <div class="event-foot">
                                <span>${rupiah(e.price)}</span>
                                <span class="${stateClass}">${state}</span>
                            </div>
                        </a>
                    </div>`;
            }).join('');

            AOS.refresh();
        } catch (err) {
            list.innerHTML = `<div class="col-12 field-error">${esc(err.message)}</div>`;
        }
    })();
</script>
@endpush
