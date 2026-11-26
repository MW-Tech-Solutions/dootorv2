@extends('layouts.dashboard')

@section('title', 'YouTube & Promo Adverts - ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('styles')
<style>
    .promo-live-pill-preview {
        background: rgba(15, 23, 42, 0.94);
        backdrop-filter: blur(12px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        border: 1px solid #d4af37;
        color: #ffffff;
        border-radius: 9999px;
        padding: 10px 18px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform 0.2s ease;
    }
    .promo-live-pill-preview:hover {
        transform: translateY(-2px);
    }
    .play-circle-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e11d48;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .pulse-dot {
        position: absolute;
        top: 0;
        right: -2px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #facc15;
        border: 1.5px solid #ffffff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') ?? 'Please check the form for errors:' }}
            @if($errors->any())
                <ul class="mb-0 mt-2 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h3 fw-bold text-dark mb-0">
                    <i class="bi bi-youtube text-danger me-1"></i> YouTube &amp; Promo Adverts
                </h1>
                @if(!empty($settings->promo_video_url))
                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1.5 small">
                        <i class="bi bi-broadcast me-1"></i> Live on Homepage
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3 py-1.5 small">
                        <i class="bi bi-pause-circle me-1"></i> No Video Configured
                    </span>
                @endif
            </div>
            <p class="text-secondary small mb-0">Manage your homepage promotional advert video, interactive floating player badge, and official YouTube channel.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>View Homepage</span>
            </a>
        </div>
    </div>

    @php
        $rawPromo = $settings->promo_video_url ?? '';
        $promoId = '';
        if (!empty($rawPromo)) {
            if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $rawPromo, $ytM)) {
                $promoId = $ytM[1];
            }
        }
        $embedSrc = $promoId ? ('https://www.youtube.com/embed/' . $promoId . '?enablejsapi=1') : '';
        $videoLabel = !empty($settings->promo_video_title) ? $settings->promo_video_title : 'Featured: Official Services Video';
    @endphp

    <div class="row g-4">
        <!-- Left Column: Settings Form -->
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4 bg-white h-100">
                <div class="border-bottom pb-3 mb-4">
                    <h2 class="h5 fw-bold text-dark mb-1">
                        <i class="bi bi-sliders me-2 text-success"></i> Advert Configuration
                    </h2>
                    <p class="text-secondary small mb-0">Enter your promotional video link. It will automatically pop up as an interactive floating badge for all landing page visitors.</p>
                </div>

                <form action="{{ route('admin.adverts.update') }}" method="POST" id="advertsForm">
                    @csrf

                    <!-- YouTube Video URL -->
                    <div class="mb-4">
                        <label for="promo_video_url" class="form-label fw-bold text-dark small mb-1">
                            Featured YouTube Video URL <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-danger">
                                <i class="bi bi-youtube fs-5"></i>
                            </span>
                            <input type="text" 
                                   name="promo_video_url" 
                                   id="promo_video_url" 
                                   class="form-control border-start-0 py-2.5" 
                                   value="{{ old('promo_video_url', $settings->promo_video_url) }}" 
                                   placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..." 
                                   autocomplete="off">
                        </div>
                        <div class="form-text small text-muted mt-1">
                            <i class="bi bi-info-circle me-1"></i> Supports standard YouTube videos (<code>youtube.com/watch?v=...</code>), short links (<code>youtu.be/...</code>), Shorts (<code>youtube.com/shorts/...</code>), or embed links.
                        </div>
                    </div>

                    <!-- Button / Badge Label -->
                    <div class="mb-4">
                        <label for="promo_video_title" class="form-label fw-bold text-dark small mb-1">
                            Floating Pill Button Label <span class="text-muted fw-normal">(Displayed on Homepage)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-tag-fill"></i>
                            </span>
                            <input type="text" 
                                   name="promo_video_title" 
                                   id="promo_video_title" 
                                   class="form-control border-start-0 py-2.5" 
                                   value="{{ old('promo_video_title', $settings->promo_video_title) }}" 
                                   placeholder="Featured: Official Services Video" 
                                   maxlength="255">
                        </div>
                        <div class="form-text small text-muted mt-1">
                            Text displayed inside the floating badge on the bottom-left of the homepage.
                        </div>
                    </div>

                    <!-- Official YouTube Channel -->
                    <div class="mb-4">
                        <label for="youtube_url" class="form-label fw-bold text-dark small mb-1">
                            Official YouTube Channel URL <span class="text-muted fw-normal">(Footer &amp; Social Links)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-link-45deg fs-5"></i>
                            </span>
                            <input type="url" 
                                   name="youtube_url" 
                                   id="youtube_url" 
                                   class="form-control border-start-0 py-2.5" 
                                   value="{{ old('youtube_url', $settings->youtube_url) }}" 
                                   placeholder="https://www.youtube.com/@dootor-enterprises">
                        </div>
                        <div class="form-text small text-muted mt-1">
                            Links the YouTube icon in the public website footer directly to your official YouTube channel.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top flex-wrap gap-2">
                        @if(!empty($settings->promo_video_url))
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2" onclick="clearPromoVideo()">
                                <i class="bi bi-trash3 me-1"></i> Clear / Disable Video
                            </button>
                        @else
                            <div></div>
                        @endif

                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm d-flex align-items-center gap-2" style="background-color: #004225; border: none;">
                            <i class="bi bi-check2-circle"></i>
                            <span>Save Advert Settings</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Interactive Live Preview -->
        <div class="col-12 col-xl-5">
            <div class="d-flex flex-column gap-4">

                <!-- Player Preview Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h6 fw-bold text-dark mb-0">
                            <i class="bi bi-tv text-primary me-1.5"></i> Live Video Preview
                        </h3>
                        <span class="badge bg-light text-secondary border small" id="previewStatusBadge">
                            {{ $promoId ? 'Video Detected' : 'No Video' }}
                        </span>
                    </div>

                    <!-- Video Iframe / Placeholder Container -->
                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden border bg-dark shadow-sm position-relative">
                        <iframe id="previewIframe" 
                                src="{{ $embedSrc }}" 
                                title="YouTube video player" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen
                                style="{{ $promoId ? '' : 'display:none;' }}"></iframe>
                        
                        <div id="noVideoPlaceholder" class="d-flex flex-column align-items-center justify-content-center text-center p-4 text-white-50" style="{{ $promoId ? 'display:none !important;' : '' }}">
                            <div class="rounded-circle bg-white bg-opacity-10 p-3 mb-2">
                                <i class="bi bi-play-btn fs-1 text-white"></i>
                            </div>
                            <span class="fw-semibold text-white small">No Advert Video Configured</span>
                            <span class="extra-small text-white-50 mt-1" style="font-size: 11px;">Paste a YouTube link on the left to preview the video live here.</span>
                        </div>
                    </div>
                </div>

                <!-- Floating Pill Live Appearance -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h3 class="h6 fw-bold text-dark mb-2">
                        <i class="bi bi-eye text-warning me-1.5"></i> Homepage Floating Pill Appearance
                    </h3>
                    <p class="text-secondary extra-small mb-3" style="font-size: 12px;">This is the exact floating badge that appears in the bottom-left corner of the landing page:</p>

                    <div class="p-4 rounded-4 text-center" style="background: linear-gradient(135deg, #091a11 0%, #112d1e 100%);">
                        <div class="promo-live-pill-preview cursor-pointer" id="pillPreviewElement">
                            <span class="play-circle-badge">
                                <i class="bi bi-play-fill fs-5" style="margin-left: 2px;"></i>
                                <span class="pulse-dot"></span>
                            </span>
                            <span class="text-white small fw-bold" id="pillPreviewText">{{ $videoLabel }}</span>
                        </div>
                    </div>
                </div>

                <!-- Guidance Info Box -->
                <div class="card border-0 bg-success bg-opacity-10 rounded-4 p-3.5 border-start border-4 border-success">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-lightbulb-fill text-success fs-5 mt-0.5"></i>
                        <div class="small text-secondary">
                            <strong class="text-dark d-block mb-1">How it Works for Visitors</strong>
                            When a client visits the homepage, the floating advert pill appears automatically in the bottom-left corner. Clicking it opens a cinema-mode popup modal playing your YouTube advert video with zero external page redirects.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('promo_video_url');
    const titleInput = document.getElementById('promo_video_title');
    const iframe = document.getElementById('previewIframe');
    const placeholder = document.getElementById('noVideoPlaceholder');
    const pillText = document.getElementById('pillPreviewText');
    const statusBadge = document.getElementById('previewStatusBadge');

    function extractYouTubeId(url) {
        if (!url) return '';
        const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/);
        return match ? match[1] : '';
    }

    function updatePreview() {
        const url = (urlInput.value || '').trim();
        const id = extractYouTubeId(url);

        if (id) {
            iframe.src = 'https://www.youtube.com/embed/' + id + '?enablejsapi=1';
            iframe.style.display = 'block';
            placeholder.classList.add('d-none');
            placeholder.style.display = 'none';
            statusBadge.textContent = 'Video Ready: #' + id;
            statusBadge.className = 'badge bg-success-subtle text-success border border-success small';
        } else if (url.startsWith('http')) {
            iframe.src = url;
            iframe.style.display = 'block';
            placeholder.classList.add('d-none');
            placeholder.style.display = 'none';
            statusBadge.textContent = 'Custom Embed Link';
            statusBadge.className = 'badge bg-info-subtle text-info border border-info small';
        } else {
            iframe.src = '';
            iframe.style.display = 'none';
            placeholder.classList.remove('d-none');
            placeholder.style.display = 'flex';
            statusBadge.textContent = 'No Video';
            statusBadge.className = 'badge bg-light text-secondary border small';
        }

        const title = (titleInput.value || '').trim();
        pillText.textContent = title || 'Featured: Official Services Video';
    }

    urlInput.addEventListener('input', updatePreview);
    titleInput.addEventListener('input', updatePreview);
});

function clearPromoVideo() {
    if (confirm('Are you sure you want to remove the advert video from the homepage?')) {
        document.getElementById('promo_video_url').value = '';
        document.getElementById('advertsForm').submit();
    }
}
</script>
@endsection
