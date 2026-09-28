async function api(path, options = {}) {
    const response = await fetch('/api' + path, {
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        ...options,
    });

    const body = response.status === 204 ? null : await response.json();

    if (!response.ok) {
        const error = new Error(body?.message ?? 'Terjadi kesalahan.');
        error.status = response.status;
        error.errors = body?.errors ?? {};
        throw error;
    }

    return body;
}

const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

const tanggal = (iso) =>
    new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });

const esc = (text) =>
    String(text ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

document.addEventListener('DOMContentLoaded', () => AOS.init({ duration: 600, once: true }));