<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const openFaq = ref<number | null>(null);

function toggleFaq(index: number) {
    openFaq.value = openFaq.value === index ? null : index;
}

const features = [
    {
        icon: '⚡',
        title: 'Aktivasi Instan',
        desc: 'Daftar dan mulai terima pembayaran dalam hitungan menit. Tanpa proses panjang.',
    },
    {
        icon: '🔗',
        title: 'Integrasi Mudah',
        desc: 'API sederhana dan dokumentasi lengkap. Integrasikan ke platform Anda dengan cepat.',
    },
    {
        icon: '💰',
        title: 'Biaya Rendah',
        desc: 'Nikmati biaya transaksi yang kompetitif untuk setiap pembayaran yang masuk.',
    },
    {
        icon: '🏦',
        title: 'Penarikan Murah',
        desc: 'Tarik dana ke rekening bank Anda kapan saja dengan biaya minimal.',
    },
    {
        icon: '🚀',
        title: 'Tidak Perlu KYC',
        desc: 'Mulai terima pembayaran tanpa verifikasi identitas yang rumit dan memakan waktu.',
    },
];

const faqs = [
    {
        q: 'Apa itu JavaraPay?',
        a: 'JavaraPay adalah layanan payment link yang memudahkan Anda menerima pembayaran online. Cukup buat link pembayaran, bagikan ke pelanggan, dan terima uang langsung ke akun Anda.',
    },
    {
        q: 'Berapa biaya per transaksi?',
        a: 'Biaya transaksi JavaraPay sangat kompetitif dan bervariasi tergantung metode pembayaran yang digunakan pelanggan. Tidak ada biaya bulanan atau biaya tersembunyi.',
    },
    {
        q: 'Bagaimana cara menarik dana?',
        a: 'Anda dapat menarik dana kapan saja melalui dashboard JavaraPay ke rekening bank manapun di Indonesia dengan biaya penarikan yang sangat terjangkau.',
    },
    {
        q: 'Apakah JavaraPay aman?',
        a: 'Ya, JavaraPay menggunakan enkripsi standar industri dan bekerja sama dengan payment gateway terpercaya untuk memastikan setiap transaksi aman dan terlindungi.',
    },
    {
        q: 'Metode pembayaran apa saja yang didukung?',
        a: 'JavaraPay mendukung berbagai metode pembayaran termasuk transfer bank, e-wallet (GoPay, OVO, DANA, ShopeePay), QRIS, kartu kredit/debit, dan minimarket.',
    },
    {
        q: 'Apakah saya perlu memiliki badan usaha?',
        a: 'Tidak. JavaraPay dapat digunakan oleh siapa saja — individu, freelancer, maupun bisnis — tanpa perlu KYC atau verifikasi badan usaha.',
    },
];
</script>

<template>
    <Head title="JavaraPay — Layanan Pembayaran Link Mudah">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <meta name="description" content="JavaraPay — Layanan pembayaran link secara mudah. Aktivasi instan, integrasi mudah, biaya rendah, penarikan murah, tanpa KYC." />
    </Head>

    <div class="jp-landing">
        <!-- NAV -->
        <nav class="jp-nav">
            <div class="jp-nav-inner">
                <a href="/" class="jp-logo">
                    <span class="jp-logo-icon">
                        <svg viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="28" height="28" rx="8" fill="url(#logo-grad)" />
                            <path d="M8 8h4v8a4 4 0 01-4-4V8z" fill="#fff" opacity=".9"/>
                            <path d="M14 8h6v2h-4v4a4 4 0 01-2-3.5V8z" fill="#fff" opacity=".7"/>
                            <defs>
                                <linearGradient id="logo-grad" x1="0" y1="0" x2="28" y2="28">
                                    <stop stop-color="#6366f1"/>
                                    <stop offset="1" stop-color="#8b5cf6"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </span>
                    <span class="jp-logo-text">JavaraPay</span>
                </a>
                <div class="jp-nav-links">
                    <Link href="/docs" class="jp-btn jp-btn-ghost">
                        Docs
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="jp-btn jp-btn-primary"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="jp-btn jp-btn-ghost">
                            Masuk
                        </Link>
                        <Link :href="route('register')" class="jp-btn jp-btn-primary">
                            Daftar Gratis
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- HERO -->
        <section class="jp-hero">
            <div class="jp-hero-glow"></div>
            <div class="jp-hero-content">
                <div class="jp-badge">🚀 Payment Link Platform</div>
                <h1 class="jp-hero-title">
                    Layanan Pembayaran Link <br/><span class="jp-gradient-text">Secara Mudah</span>
                </h1>
                <p class="jp-hero-subtitle">
                    Buat link pembayaran, bagikan ke pelanggan, dan terima uang langsung.
                    Tanpa ribet, tanpa KYC, mulai dalam hitungan menit.
                </p>
                <div class="jp-hero-actions">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="jp-btn jp-btn-hero"
                    >
                        Buka Dashboard
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </Link>
                    <Link
                        v-else
                        :href="route('register')"
                        class="jp-btn jp-btn-hero"
                    >
                        Mulai Sekarang — Gratis
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </Link>
                </div>
                <!-- Stats row -->
                <div class="jp-stats">
                    <div class="jp-stat">
                        <span class="jp-stat-value">0%</span>
                        <span class="jp-stat-label">Biaya Pendaftaran</span>
                    </div>
                    <div class="jp-stat-divider"></div>
                    <div class="jp-stat">
                        <span class="jp-stat-value">&lt;1 mnt</span>
                        <span class="jp-stat-label">Aktivasi Akun</span>
                    </div>
                    <div class="jp-stat-divider"></div>
                    <div class="jp-stat">
                        <span class="jp-stat-value">24/7</span>
                        <span class="jp-stat-label">Sistem Online</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES / WHY -->
        <section class="jp-features" id="features">
            <div class="jp-section-inner">
                <div class="jp-section-header">
                    <span class="jp-section-badge">Keunggulan</span>
                    <h2 class="jp-section-title">Mengapa <span class="jp-gradient-text">JavaraPay</span>?</h2>
                    <p class="jp-section-desc">Semua yang Anda butuhkan untuk menerima pembayaran online dengan mudah dan efisien.</p>
                </div>
                <div class="jp-features-grid">
                    <div v-for="(f, i) in features" :key="i" class="jp-feature-card">
                        <div class="jp-feature-icon">{{ f.icon }}</div>
                        <h3 class="jp-feature-title">{{ f.title }}</h3>
                        <p class="jp-feature-desc">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section class="jp-how">
            <div class="jp-section-inner">
                <div class="jp-section-header">
                    <span class="jp-section-badge">Cara Kerja</span>
                    <h2 class="jp-section-title">Tiga Langkah <span class="jp-gradient-text">Simpel</span></h2>
                </div>
                <div class="jp-steps">
                    <div class="jp-step">
                        <div class="jp-step-num">1</div>
                        <h3>Daftar Akun</h3>
                        <p>Buat akun JavaraPay gratis tanpa syarat KYC.</p>
                    </div>
                    <div class="jp-step-arrow">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="jp-step">
                        <div class="jp-step-num">2</div>
                        <h3>Buat Payment Link</h3>
                        <p>Tentukan nominal dan buat link pembayaran instan.</p>
                    </div>
                    <div class="jp-step-arrow">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="jp-step">
                        <div class="jp-step-num">3</div>
                        <h3>Terima Pembayaran</h3>
                        <p>Pelanggan bayar, dana masuk ke akun Anda.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="jp-faq" id="faq">
            <div class="jp-section-inner">
                <div class="jp-section-header">
                    <span class="jp-section-badge">FAQ</span>
                    <h2 class="jp-section-title">Pertanyaan yang <span class="jp-gradient-text">Sering Ditanyakan</span></h2>
                </div>
                <div class="jp-faq-list">
                    <div
                        v-for="(faq, i) in faqs"
                        :key="i"
                        class="jp-faq-item"
                        :class="{ 'is-open': openFaq === i }"
                    >
                        <button class="jp-faq-q" @click="toggleFaq(i)">
                            <span>{{ faq.q }}</span>
                            <svg class="jp-faq-chevron" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="jp-faq-a">
                            <p>{{ faq.a }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="jp-cta">
            <div class="jp-cta-glow"></div>
            <div class="jp-section-inner">
                <h2 class="jp-cta-title">Siap Menerima Pembayaran?</h2>
                <p class="jp-cta-desc">Bergabung sekarang dan mulai terima pembayaran dalam hitungan menit.</p>
                <Link
                    v-if="!$page.props.auth.user"
                    :href="route('register')"
                    class="jp-btn jp-btn-hero"
                >
                    Daftar Gratis Sekarang
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </Link>
                <Link
                    v-else
                    :href="route('dashboard')"
                    class="jp-btn jp-btn-hero"
                >
                    Buka Dashboard
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </Link>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="jp-footer">
            <div class="jp-footer-inner">
                <p>&copy; {{ new Date().getFullYear() }} JavaraPay. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* ===== BASE ===== */
.jp-landing {
    --jp-bg: #0b0d17;
    --jp-surface: #12152480;
    --jp-border: rgba(255,255,255,0.06);
    --jp-text: #e2e8f0;
    --jp-muted: #94a3b8;
    --jp-accent-1: #6366f1;
    --jp-accent-2: #8b5cf6;
    --jp-accent-3: #a78bfa;

    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background: var(--jp-bg);
    color: var(--jp-text);
    min-height: 100vh;
    overflow-x: hidden;
}

/* ===== NAV ===== */
.jp-nav {
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(20px) saturate(180%);
    background: rgba(11, 13, 23, 0.8);
    border-bottom: 1px solid var(--jp-border);
}
.jp-nav-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.jp-logo {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    text-decoration: none;
    color: #fff;
}
.jp-logo-icon svg {
    width: 32px;
    height: 32px;
}
.jp-logo-text {
    font-weight: 800;
    font-size: 1.25rem;
    letter-spacing: -0.02em;
}
.jp-nav-links {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* ===== BUTTONS ===== */
.jp-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.25rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
    white-space: nowrap;
}
.jp-btn-ghost {
    color: var(--jp-muted);
    background: transparent;
}
.jp-btn-ghost:hover {
    color: #fff;
}
.jp-btn-primary {
    background: linear-gradient(135deg, var(--jp-accent-1), var(--jp-accent-2));
    color: #fff;
    box-shadow: 0 0 20px rgba(99,102,241,0.3);
}
.jp-btn-primary:hover {
    box-shadow: 0 0 30px rgba(99,102,241,0.5);
    transform: translateY(-1px);
}
.jp-btn-hero {
    background: linear-gradient(135deg, var(--jp-accent-1), var(--jp-accent-2));
    color: #fff;
    padding: 0.85rem 2rem;
    font-size: 1rem;
    border-radius: 14px;
    box-shadow: 0 0 40px rgba(99,102,241,0.35);
}
.jp-btn-hero:hover {
    box-shadow: 0 0 60px rgba(99,102,241,0.55);
    transform: translateY(-2px);
}

/* ===== HERO ===== */
.jp-hero {
    position: relative;
    padding: 6rem 1.5rem 4rem;
    text-align: center;
    overflow: hidden;
}
.jp-hero-glow {
    position: absolute;
    top: -180px;
    left: 50%;
    transform: translateX(-50%);
    width: 700px;
    height: 700px;
    background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%);
    pointer-events: none;
}
.jp-hero-content {
    position: relative;
    max-width: 720px;
    margin: 0 auto;
}
.jp-badge {
    display: inline-block;
    padding: 0.35rem 1rem;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 600;
    background: rgba(99,102,241,0.12);
    color: var(--jp-accent-3);
    border: 1px solid rgba(99,102,241,0.2);
    margin-bottom: 1.5rem;
}
.jp-hero-title {
    font-size: clamp(2.2rem, 5vw, 3.5rem);
    font-weight: 900;
    line-height: 1.15;
    letter-spacing: -0.03em;
    color: #fff;
    margin-bottom: 1.25rem;
}
.jp-gradient-text {
    background: linear-gradient(135deg, var(--jp-accent-1), var(--jp-accent-3), #c4b5fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.jp-hero-subtitle {
    font-size: 1.1rem;
    color: var(--jp-muted);
    line-height: 1.7;
    max-width: 560px;
    margin: 0 auto 2rem;
}
.jp-hero-actions {
    margin-bottom: 3rem;
}

/* ===== STATS ===== */
.jp-stats {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}
.jp-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}
.jp-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
}
.jp-stat-label {
    font-size: 0.8rem;
    color: var(--jp-muted);
}
.jp-stat-divider {
    width: 1px;
    height: 40px;
    background: var(--jp-border);
}

/* ===== SECTIONS ===== */
.jp-section-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem;
}
.jp-section-header {
    text-align: center;
    margin-bottom: 3.5rem;
}
.jp-section-badge {
    display: inline-block;
    padding: 0.3rem 0.85rem;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--jp-accent-3);
    background: rgba(99,102,241,0.1);
    border: 1px solid rgba(99,102,241,0.15);
    margin-bottom: 1rem;
}
.jp-section-title {
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
    margin-bottom: 0.75rem;
}
.jp-section-desc {
    font-size: 1.05rem;
    color: var(--jp-muted);
    max-width: 540px;
    margin: 0 auto;
    line-height: 1.65;
}

/* ===== FEATURES ===== */
.jp-features {
    padding: 5rem 0;
}
.jp-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
}
.jp-feature-card {
    background: var(--jp-surface);
    border: 1px solid var(--jp-border);
    border-radius: 16px;
    padding: 2rem 1.75rem;
    transition: all 0.3s ease;
}
.jp-feature-card:hover {
    border-color: rgba(99,102,241,0.25);
    background: rgba(99,102,241,0.05);
    transform: translateY(-4px);
    box-shadow: 0 8px 40px rgba(99,102,241,0.1);
}
.jp-feature-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: rgba(99,102,241,0.08);
}
.jp-feature-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
}
.jp-feature-desc {
    font-size: 0.925rem;
    color: var(--jp-muted);
    line-height: 1.6;
}

/* ===== HOW IT WORKS ===== */
.jp-how {
    padding: 5rem 0;
}
.jp-steps {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.jp-step {
    flex: 1;
    min-width: 200px;
    max-width: 280px;
    text-align: center;
    padding: 2rem 1.25rem;
    background: var(--jp-surface);
    border: 1px solid var(--jp-border);
    border-radius: 16px;
    transition: all 0.3s ease;
}
.jp-step:hover {
    border-color: rgba(99,102,241,0.25);
    transform: translateY(-3px);
}
.jp-step-num {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--jp-accent-1), var(--jp-accent-2));
    color: #fff;
    font-weight: 800;
    font-size: 1.1rem;
    margin: 0 auto 1rem;
}
.jp-step h3 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
}
.jp-step p {
    font-size: 0.9rem;
    color: var(--jp-muted);
    line-height: 1.55;
}
.jp-step-arrow {
    display: flex;
    align-items: center;
    padding-top: 3rem;
    color: var(--jp-accent-3);
    opacity: 0.4;
}
@media (max-width: 768px) {
    .jp-step-arrow {
        display: none;
    }
}

/* ===== FAQ ===== */
.jp-faq {
    padding: 5rem 0;
}
.jp-faq-list {
    max-width: 700px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.jp-faq-item {
    background: var(--jp-surface);
    border: 1px solid var(--jp-border);
    border-radius: 14px;
    overflow: hidden;
    transition: border-color 0.3s ease;
}
.jp-faq-item:hover {
    border-color: rgba(99,102,241,0.2);
}
.jp-faq-item.is-open {
    border-color: rgba(99,102,241,0.3);
}
.jp-faq-q {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.15rem 1.5rem;
    background: transparent;
    border: none;
    color: #fff;
    font-size: 0.975rem;
    font-weight: 600;
    text-align: left;
    cursor: pointer;
    font-family: inherit;
    transition: color 0.2s ease;
}
.jp-faq-q:hover {
    color: var(--jp-accent-3);
}
.jp-faq-chevron {
    flex-shrink: 0;
    color: var(--jp-muted);
    transition: transform 0.3s ease;
}
.jp-faq-item.is-open .jp-faq-chevron {
    transform: rotate(180deg);
}
.jp-faq-a {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease, padding 0.35s ease;
    padding: 0 1.5rem;
}
.jp-faq-item.is-open .jp-faq-a {
    max-height: 300px;
    padding: 0 1.5rem 1.25rem;
}
.jp-faq-a p {
    font-size: 0.925rem;
    color: var(--jp-muted);
    line-height: 1.7;
}

/* ===== CTA ===== */
.jp-cta {
    position: relative;
    padding: 5rem 1.5rem;
    text-align: center;
    overflow: hidden;
}
.jp-cta-glow {
    position: absolute;
    bottom: -100px;
    left: 50%;
    transform: translateX(-50%);
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(139,92,246,0.12) 0%, transparent 70%);
    pointer-events: none;
}
.jp-cta-title {
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.75rem;
    position: relative;
}
.jp-cta-desc {
    font-size: 1.05rem;
    color: var(--jp-muted);
    margin-bottom: 2rem;
    position: relative;
}

/* ===== FOOTER ===== */
.jp-footer {
    border-top: 1px solid var(--jp-border);
    padding: 2rem 1.5rem;
}
.jp-footer-inner {
    max-width: 1100px;
    margin: 0 auto;
    text-align: center;
}
.jp-footer-inner p {
    font-size: 0.85rem;
    color: var(--jp-muted);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 640px) {
    .jp-hero {
        padding: 4rem 1rem 3rem;
    }
    .jp-hero-subtitle {
        font-size: 1rem;
    }
    .jp-stats {
        gap: 1rem;
    }
    .jp-stat-divider {
        height: 30px;
    }
    .jp-features-grid {
        grid-template-columns: 1fr;
    }
}
</style>
