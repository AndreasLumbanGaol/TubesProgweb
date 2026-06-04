<style>
    .tixly-modal .modal-content {
        background: linear-gradient(135deg, #120707 0%, #0a0303 100%);
        border: 1px solid rgba(212, 175, 55, 0.4);
        border-radius: 20px;
        color: #fff;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.8), 0 0 25px rgba(212, 175, 55, 0.1);
        backdrop-filter: blur(10px);
    }
    .tixly-modal .modal-header { border-bottom: 1px solid rgba(212, 175, 55, 0.15); padding: 20px 24px; }
    .tixly-modal .modal-title { color: #d4af37; font-family: 'Times New Roman', Georgia, serif; font-size: 24px; font-weight: bold; letter-spacing: 1px; }
    .tixly-modal .btn-close-white { filter: invert(1) grayscale(1) brightness(2); opacity: 0.8; transition: all 0.3s; }
    .tixly-modal .btn-close-white:hover { transform: rotate(90deg); opacity: 1; }

    .modal-movie-card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 16px; }
    .modal-movie-poster-img { width: 100%; max-width: 90px; aspect-ratio: 2/3; object-fit: cover; border-radius: 8px; border: 2px solid #d4af37; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5); }
    .modal-movie-title-text { font-size: 20px; font-weight: bold; color: #ffffff; margin-bottom: 4px; }
    .modal-movie-meta { font-size: 13px; color: #d4af37; margin: 0; display: flex; align-items: center; gap: 8px; }

    .btn-check-custom { display: none; }
    .card-selection-option {
        background-color: rgba(212, 175, 55, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px 10px;
        text-align: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; position: relative; overflow: hidden;
    }
    .card-selection-option:hover { background-color: rgba(255, 255, 255, 0.04); border-color: rgba(212, 175, 55, 0.4); transform: translateY(-2px); }
    .btn-check-custom:checked + .card-selection-option { background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%); border-color: #d4af37 !important; box-shadow: 0 0 15px rgba(212, 175, 55, 0.25); }
    
    .cinema-brand-title { font-size: 18px; font-weight: 800; margin-bottom: 2px; letter-spacing: 0.5px; }
    .brand-xxi { color: #d4af37; text-shadow: 0 0 10px rgba(212, 175, 55, 0.3); }
    .brand-cgv { color: #ff3e3e; text-shadow: 0 0 10px rgba(255, 62, 62, 0.3); }
    .brand-cinemapolis { color: #00d2ff; text-shadow: 0 0 10px rgba(0, 210, 255, 0.3); }
    .cinema-brand-sub { font-size: 11px; color: #888; font-weight: 500; }
    
    .studio-type-title { font-size: 15px; font-weight: bold; color: #fff; margin-bottom: 4px; }
    .studio-type-title.type-velvet { color: #ff9f43; }
    .studio-type-title.type-gold { color: #ee5253; }
    .studio-type-price { font-size: 13px; color: #d4af37; font-weight: 700; font-family: monospace; }
    .studio-type-desc { font-size: 10px; color: #666; margin-top: 4px; font-weight: 500; }

    .custom-form-select { background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; color: #ffffff !important; border-radius: 8px !important; padding: 10px 16px !important; transition: all 0.3s !important; cursor: pointer; }
    .custom-form-select:focus { border-color: #d4af37 !important; box-shadow: 0 0 8px rgba(212, 175, 55, 0.3) !important; outline: none !important; }
    .custom-form-select option { background-color: #120707; color: #fff; }
    
    .form-section-title { color: #d4af37; font-weight: 700; font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .form-section-title::after { content: ''; flex-grow: 1; height: 1px; background: linear-gradient(90deg, rgba(212, 175, 55, 0.3) 0%, rgba(212, 175, 55, 0) 100%); }

    .btn-cancel-custom { background-color: transparent; border: 1px solid rgba(255, 255, 255, 0.15); color: #aaa; padding: 10px 24px; border-radius: 30px; font-weight: bold; transition: all 0.3s; }
    .btn-cancel-custom:hover { background-color: rgba(255, 255, 255, 0.05); color: #fff; border-color: rgba(255, 255, 255, 0.3); }
    .btn-confirm-custom { background: linear-gradient(135deg, #d4af37 0%, #b8962e 100%); color: #000; border: none; padding: 10px 32px; border-radius: 30px; font-weight: 800; transition: all 0.3s; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2); float: right; }
    .btn-confirm-custom:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4); background: linear-gradient(135deg, #f3ca44 0%, #d4af37 100%); }
</style>

<div class="modal fade tixly-modal" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Beli Tiket Film</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                
                <div class="modal-movie-card mb-4">
                    <div class="row align-items-center g-3">
                        <div class="col-auto">
                            <img id="modal-movie-poster" src="" alt="Poster Film" class="modal-movie-poster-img">
                        </div>
                        <div class="col">
                            <h4 id="modal-movie-title" class="modal-movie-title-text">Judul Film</h4>
                            <p id="modal-movie-duration" class="modal-movie-meta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/></svg>
                                <span>-</span>
                            </p>
                        </div>
                    </div>
                </div>

                <form id="booking-selection-form">
                    <input type="hidden" id="hidden-movie-poster" name="poster_url">

                    <div class="mb-4">
                        <div class="form-section-title">Pilih Bioskop</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="cinema" id="cinema-xxi" value="XXI Botanica Mall" onchange="fetchJadwal()" checked>
                                <label class="card-selection-option" for="cinema-xxi">
                                    <span class="cinema-brand-title brand-xxi">XXI</span>
                                    <span class="cinema-brand-sub">Botanica Mall</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="cinema" id="cinema-cgv" value="CGV Paskal 23" onchange="fetchJadwal()">
                                <label class="card-selection-option" for="cinema-cgv">
                                    <span class="cinema-brand-title brand-cgv">CGV</span>
                                    <span class="cinema-brand-sub">Paskal 23</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="cinema" id="cinema-tixly" value="Tixly Central" onchange="fetchJadwal()">
                                <label class="card-selection-option" for="cinema-tixly">
                                    <span class="cinema-brand-title brand-cinemapolis">Tixly</span>
                                    <span class="cinema-brand-sub">Central</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-section-title">Pilih Tipe Studio</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="studio_type" id="type-regular" value="Regular" data-price="50000" onchange="fetchJadwal()" checked>
                                <label class="card-selection-option" for="type-regular">
                                    <span class="studio-type-title">Regular</span>
                                    <span class="studio-type-price">Rp 50.000</span>
                                    <span class="studio-type-desc">Standard Audio & Comfort Seats</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="studio_type" id="type-velvet" value="Velvet" data-price="120000" onchange="fetchJadwal()">
                                <label class="card-selection-option" for="type-velvet">
                                    <span class="studio-type-title type-velvet">Velvet Class</span>
                                    <span class="studio-type-price">Rp 120.000</span>
                                    <span class="studio-type-desc">Luxury Beds & Blankets</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="studio_type" id="type-gold" value="Gold Class" data-price="150000" onchange="fetchJadwal()">
                                <label class="card-selection-option" for="type-gold">
                                    <span class="studio-type-title type-gold">Gold Class</span>
                                    <span class="studio-type-price">Rp 150.000</span>
                                    <span class="studio-type-desc">Premium Recliners</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-section-title">Jadwal Tersedia</div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <select class="form-select custom-form-select" id="booking_jadwal" name="booking_jadwal" required>
                                    <option value="">Mencari Jadwal...</option>
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
    function fetchJadwal() {
        const movieTitle = document.getElementById('modal-movie-title').textContent;
        const selectedCinema = document.querySelector('input[name="cinema"]:checked').value;
        const selectedStudio = document.querySelector('input[name="studio_type"]:checked').value;
        const dropdownJadwal = document.getElementById('booking_jadwal');

        dropdownJadwal.innerHTML = '<option value="">Mencari Jadwal...</option>';

        fetch(`api_jadwal.php?movie=${encodeURIComponent(movieTitle)}&cinema=${encodeURIComponent(selectedCinema)}&type=${encodeURIComponent(selectedStudio)}`)
            .then(response => response.json())
            .then(data => {
                dropdownJadwal.innerHTML = ''; 
                if(data.length > 0) {
                    data.forEach(jadwal => {
                        const option = document.createElement('option');
                        option.value = jadwal.raw_date + '|' + jadwal.raw_time; 
                        option.textContent = `${jadwal.date_label} - ${jadwal.time}`;
                        dropdownJadwal.appendChild(option);
                    });
                } else {
                    dropdownJadwal.innerHTML = '<option value="">Jadwal tidak tersedia</option>';
                }
            })
            .catch(error => {
                dropdownJadwal.innerHTML = '<option value="">Gagal memuat jadwal</option>';
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const cinemaPrices = {
            "XXI Botanica Mall": { "Regular": 50000, "Velvet": 120000, "Gold Class": 150000 },
            "CGV Paskal 23": { "Regular": 35000, "Velvet": 90000, "Gold Class": 110000 },
            "Tixly Central": { "Regular": 40000, "Velvet": 100000, "Gold Class": 130000 }
        };

        function updateStudioPrices() {
            const selectedCinema = document.querySelector('input[name="cinema"]:checked').value;
            const prices = cinemaPrices[selectedCinema];
            if (prices) {
                const regInput = document.getElementById('type-regular');
                regInput.setAttribute('data-price', prices.Regular);
                regInput.nextElementSibling.querySelector('.studio-type-price').textContent = "Rp " + prices.Regular.toLocaleString('id-ID');

                const velInput = document.getElementById('type-velvet');
                velInput.setAttribute('data-price', prices.Velvet);
                velInput.nextElementSibling.querySelector('.studio-type-price').textContent = "Rp " + prices.Velvet.toLocaleString('id-ID');

                const goldInput = document.getElementById('type-gold');
                goldInput.setAttribute('data-price', prices["Gold Class"]);
                goldInput.nextElementSibling.querySelector('.studio-type-price').textContent = "Rp " + prices["Gold Class"].toLocaleString('id-ID');
            }
        }

        document.querySelectorAll('input[name="cinema"]').forEach(radio => {
            radio.addEventListener('change', updateStudioPrices);
        });

        const bookingModal = document.getElementById('bookingModal');
        if (bookingModal) {
            bookingModal.addEventListener('show.bs.modal', function(event) {
                const triggerElement = event.relatedTarget;
                
                document.getElementById('modal-movie-title').textContent = triggerElement.getAttribute('data-title');
                document.getElementById('modal-movie-poster').src = triggerElement.getAttribute('data-poster');
                document.getElementById('hidden-movie-poster').value = triggerElement.getAttribute('data-poster');
                document.getElementById('modal-movie-duration').querySelector('span').textContent = triggerElement.getAttribute('data-duration') || 'N/A';

                updateStudioPrices();
                fetchJadwal();
            });
        }

        const confirmBtn = document.getElementById('confirm-booking-btn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                const rawJadwal = document.getElementById('booking_jadwal').value;
                if(!rawJadwal) {
                    alert("Maaf, jadwal belum tersedia untuk bioskop atau studio ini. Silakan pilih opsi lain.");
                    return;
                }

                const splitJadwal = rawJadwal.split('|');
                
                const queryParams = new URLSearchParams({
                    movie: document.getElementById('modal-movie-title').textContent,
                    poster: document.getElementById('hidden-movie-poster').value,
                    duration: document.getElementById('modal-movie-duration').textContent.trim(),
                    cinema: document.querySelector('input[name="cinema"]:checked').value,
                    type: document.querySelector('input[name="studio_type"]:checked').value,
                    price: document.querySelector('input[name="studio_type"]:checked').getAttribute('data-price'),
                    date: splitJadwal[0],
                    time: splitJadwal[1]
                });

                window.location.href = 'booking.php?' + queryParams.toString();
            });
        }
    });
</script>