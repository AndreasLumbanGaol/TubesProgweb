<!-- Booking Selection Modal -->
<style>
    /* Styling khusus Modal Tixly Premium */
    .tixly-modal .modal-content {
        background: linear-gradient(135deg, #120707 0%, #0a0303 100%);
        border: 1px solid rgba(212, 175, 55, 0.4);
        border-radius: 20px;
        color: #fff;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.8), 0 0 25px rgba(212, 175, 55, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .tixly-modal .modal-header {
        border-bottom: 1px solid rgba(212, 175, 55, 0.15);
        padding: 20px 24px;
    }
    
    .tixly-modal .modal-title {
        color: #d4af37;
        font-family: 'Times New Roman', Georgia, serif;
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 1px;
    }
    
    .tixly-modal .btn-close-white {
        filter: invert(1) grayscale(1) brightness(2);
        opacity: 0.8;
        transition: all 0.3s;
    }
    
    .tixly-modal .btn-close-white:hover {
        transform: rotate(90deg);
        opacity: 1;
    }

    /* Info Poster Film */
    .modal-movie-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 16px;
    }
    
    .modal-movie-poster-img {
        width: 100%;
        max-width: 90px;
        aspect-ratio: 2/3;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #d4af37;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
    }
    
    .modal-movie-title-text {
        font-size: 20px;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 4px;
    }
    
    .modal-movie-meta {
        font-size: 13px;
        color: #d4af37;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Desain Pilihan Radio Kustom */
    .btn-check-custom {
        display: none;
    }
    
    .card-selection-option {
        background-color: rgba(212, 175, 55, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 14px 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .card-selection-option:hover {
        background-color: rgba(255, 255, 255, 0.04);
        border-color: rgba(212, 175, 55, 0.4);
        transform: translateY(-2px);
    }
    
    .btn-check-custom:checked + .card-selection-option {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
        border-color: #d4af37 !important;
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.25);
    }
    
    /* Tema Brand Bioskop */
    .cinema-brand-title {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 2px;
        letter-spacing: 0.5px;
    }
    .brand-xxi { color: #d4af37; text-shadow: 0 0 10px rgba(212, 175, 55, 0.3); }
    .brand-cgv { color: #ff3e3e; text-shadow: 0 0 10px rgba(255, 62, 62, 0.3); }
    .brand-cinemapolis { color: #00d2ff; text-shadow: 0 0 10px rgba(0, 210, 255, 0.3); }
    
    .cinema-brand-sub {
        font-size: 11px;
        color: #888;
        font-weight: 500;
    }
    
    /* Tema Tipe Studio */
    .studio-type-title {
        font-size: 15px;
        font-weight: bold;
        color: #fff;
        margin-bottom: 4px;
    }
    .studio-type-title.type-velvet { color: #ff9f43; }
    .studio-type-title.type-gold { color: #ee5253; }
    
    .studio-type-price {
        font-size: 13px;
        color: #d4af37;
        font-weight: 700;
        font-family: monospace;
    }
    
    .studio-type-desc {
        font-size: 10px;
        color: #666;
        margin-top: 4px;
        font-weight: 500;
    }

    /* Pilihan Form Tanggal & Waktu */
    .custom-form-select {
        background-color: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        padding: 10px 16px !important;
        transition: all 0.3s !important;
        cursor: pointer;
    }
    .custom-form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 8px rgba(212, 175, 55, 0.3) !important;
        outline: none !important;
    }
    .custom-form-select option {
        background-color: #120707;
        color: #fff;
    }
    
    .form-section-title {
        color: #d4af37;
        font-weight: 700;
        font-size: 12px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .form-section-title::after {
        content: '';
        flex-grow: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(212, 175, 55, 0.3) 0%, rgba(212, 175, 55, 0) 100%);
    }

    /* Tombol */
    .btn-cancel-custom {
        background-color: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #aaa;
        padding: 10px 24px;
        border-radius: 30px;
        font-weight: bold;
        transition: all 0.3s;
    }
    .btn-cancel-custom:hover {
        background-color: rgba(255, 255, 255, 0.05);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .btn-confirm-custom {
        background: linear-gradient(135deg, #d4af37 0%, #b8962e 100%);
        color: #000;
        border: none;
        padding: 10px 32px;
        border-radius: 30px;
        font-weight: 800;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
    }
    .btn-confirm-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        background: linear-gradient(135deg, #f3ca44 0%, #d4af37 100%);
    }
    .btn-confirm-custom:active {
        transform: translateY(0);
    }
</style>

<div class="modal fade tixly-modal" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Beli Tiket Film</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <!-- Banner Info Film Terpilih -->
                <div class="modal-movie-card mb-4">
                    <div class="row align-items-center g-3">
                        <div class="col-auto">
                            <img id="modal-movie-poster" src="" alt="Poster Film" class="modal-movie-poster-img">
                        </div>
                        <div class="col">
                            <h4 id="modal-movie-title" class="modal-movie-title-text">Judul Film</h4>
                            <p id="modal-movie-duration" class="modal-movie-meta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                </svg>
                                <span>-</span>
                            </p>
                        </div>
                    </div>
                </div>

                <form id="booking-selection-form">
                    <!-- Menyimpan link poster asli secara tersembunyi -->
                    <input type="hidden" id="hidden-movie-poster" name="poster_url">

                    <!-- BAGIAN 1: PILIH BIOSKOP -->
                    <div class="mb-4">
                        <div class="form-section-title">Pilih Bioskop</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="cinema" id="cinema-xxi" value="XXI Cinema" checked>
                                <label class="card-selection-option" for="cinema-xxi">
                                    <span class="cinema-brand-title brand-xxi">XXI</span>
                                    <span class="cinema-brand-sub">LUXURY EXPERIENCE</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="cinema" id="cinema-cgv" value="CGV Cinemas">
                                <label class="card-selection-option" for="cinema-cgv">
                                    <span class="cinema-brand-title brand-cgv">CGV</span>
                                    <span class="cinema-brand-sub">RETRO AUDITORIUM</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="cinema" id="cinema-cinemapolis" value="Cinemapolis">
                                <label class="card-selection-option" for="cinema-cinemapolis">
                                    <span class="cinema-brand-title brand-cinemapolis">Cinepolis</span>
                                    <span class="cinema-brand-sub">MODERN SCREEN</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 2: PILIH TIPE STUDIO -->
                    <div class="mb-4">
                        <div class="form-section-title">Pilih Tipe Studio</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="studio_type" id="type-regular" value="Regular" data-price="50000" checked>
                                <label class="card-selection-option" for="type-regular">
                                    <span class="studio-type-title">Regular</span>
                                    <span class="studio-type-price">Rp 50.000</span>
                                    <span class="studio-type-desc">Standard Audio & Comfort Seats</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="studio_type" id="type-velvet" value="Velvet" data-price="120000">
                                <label class="card-selection-option" for="type-velvet">
                                    <span class="studio-type-title type-velvet">Velvet Class</span>
                                    <span class="studio-type-price">Rp 120.000</span>
                                    <span class="studio-type-desc">Luxury Beds with Pillows & Blankets</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="studio_type" id="type-gold" value="Gold Class" data-price="150000">
                                <label class="card-selection-option" for="type-gold">
                                    <span class="studio-type-title type-gold">Gold Class</span>
                                    <span class="studio-type-price">Rp 150.000</span>
                                    <span class="studio-type-desc">Premium Recliners & Reclining Button</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 3: PILIH JADWAL & JAM TAYANG -->
                    <div>
                        <div class="form-section-title">Pilih Jadwal Tayang</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="booking_date" class="form-label text-white-50" style="font-size: 12px; font-weight: 500;">Pilih Tanggal</label>
                                <select class="form-select custom-form-select" name="booking_date" id="booking_date">
                                    <option value="Hari Ini">Hari Ini (Today)</option>
                                    <option value="Besok">Besok (Tomorrow)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="booking_time" class="form-label text-white-50" style="font-size: 12px; font-weight: 500;">Pilih Jam</label>
                                <select class="form-select custom-form-select" name="booking_time" id="booking_time">
                                    <option value="12:00 WIB">12:00 WIB</option>
                                    <option value="14:30 WIB">14:30 WIB</option>
                                    <option value="17:00 WIB">17:00 WIB</option>
                                    <option value="19:30 WIB">19:30 WIB</option>
                                    <option value="21:45 WIB">21:45 WIB</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-cancel-custom" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="confirm-booking-btn" class="btn btn-confirm-custom">Lanjutkan Pilih Kursi &rarr;</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inisialisasi event modal setelah dokumen dimuat
    document.addEventListener("DOMContentLoaded", function() {
        // Pemetaan harga tiket dinamis per bioskop
        const cinemaPrices = {
            "XXI Cinema": {
                "Regular": { price: 50000, label: "Rp 50.000" },
                "Velvet": { price: 120000, label: "Rp 120.000" },
                "Gold Class": { price: 150000, label: "Rp 150.000" }
            },
            "CGV Cinemas": {
                "Regular": { price: 35000, label: "Rp 35.000" },
                "Velvet": { price: 90000, label: "Rp 90.000" },
                "Gold Class": { price: 110000, label: "Rp 110.000" }
            },
            "Cinemapolis": {
                "Regular": { price: 40000, label: "Rp 40.000" },
                "Velvet": { price: 100000, label: "Rp 100.000" },
                "Gold Class": { price: 130000, label: "Rp 130.000" }
            }
        };

        // Fungsi memperbarui harga tipe studio berdasarkan bioskop terpilih
        function updateStudioPrices() {
            const selectedCinema = document.querySelector('input[name="cinema"]:checked').value;
            const prices = cinemaPrices[selectedCinema];
            if (prices) {
                // Regular
                const regInput = document.getElementById('type-regular');
                regInput.setAttribute('data-price', prices.Regular.price);
                regInput.nextElementSibling.querySelector('.studio-type-price').textContent = prices.Regular.label;

                // Velvet Class
                const velInput = document.getElementById('type-velvet');
                velInput.setAttribute('data-price', prices.Velvet.price);
                velInput.nextElementSibling.querySelector('.studio-type-price').textContent = prices.Velvet.label;

                // Gold Class
                const goldInput = document.getElementById('type-gold');
                goldInput.setAttribute('data-price', prices["Gold Class"].price);
                goldInput.nextElementSibling.querySelector('.studio-type-price').textContent = prices["Gold Class"].label;
            }
        }

        // Daftarkan listener perubahan bioskop
        document.querySelectorAll('input[name="cinema"]').forEach(radio => {
            radio.addEventListener('change', updateStudioPrices);
        });

        // Logika pengisian modal secara dinamis
        const bookingModal = document.getElementById('bookingModal');
        if (bookingModal) {
            bookingModal.addEventListener('show.bs.modal', function(event) {
                // Tombol/kartu yang memicu modal
                const triggerElement = event.relatedTarget;
                
                // Ekstrak data dari atribut element
                const movieTitle = triggerElement.getAttribute('data-title');
                const moviePoster = triggerElement.getAttribute('data-poster');
                const movieDuration = triggerElement.getAttribute('data-duration');
                
                // Dapatkan element di dalam modal
                const modalTitle = bookingModal.querySelector('#modal-movie-title');
                const modalPoster = bookingModal.querySelector('#modal-movie-poster');
                const modalHiddenPoster = bookingModal.querySelector('#hidden-movie-poster');
                const modalDuration = bookingModal.querySelector('#modal-movie-duration span');
                
                // Perbarui konten modal
                modalTitle.textContent = movieTitle;
                modalPoster.src = moviePoster;
                modalHiddenPoster.value = moviePoster;
                modalDuration.textContent = movieDuration || 'N/A';

                // Jalankan fungsi update harga agar akurat saat modal tampil
                updateStudioPrices();
            });
        }

        // Arahkan ke booking.php dengan parameter terpilih
        const confirmBtn = document.getElementById('confirm-booking-btn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                const form = document.getElementById('booking-selection-form');
                const movieTitle = document.getElementById('modal-movie-title').textContent;
                const moviePoster = document.getElementById('hidden-movie-poster').value;
                const movieDuration = document.getElementById('modal-movie-duration').textContent.trim();
                
                // Ambil nilai input radio/select
                const selectedCinema = form.querySelector('input[name="cinema"]:checked').value;
                const selectedStudioInput = form.querySelector('input[name="studio_type"]:checked');
                const selectedStudio = selectedStudioInput.value;
                const selectedPrice = selectedStudioInput.getAttribute('data-price');
                const selectedDate = document.getElementById('booking_date').value;
                const selectedTime = document.getElementById('booking_time').value;

                // Bangun parameter query URL
                const queryParams = new URLSearchParams({
                    movie: movieTitle,
                    poster: moviePoster,
                    duration: movieDuration,
                    cinema: selectedCinema,
                    type: selectedStudio,
                    price: selectedPrice,
                    date: selectedDate,
                    time: selectedTime
                });

                // Arahkan browser ke booking.php
                window.location.href = 'booking.php?' + queryParams.toString();
            });
        }
    });
</script>
