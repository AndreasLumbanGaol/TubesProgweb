<?php
include_once 'koneksi.php';
$selected_loc = isset($_SESSION['selected_location']) ? $_SESSION['selected_location'] : 'Bandung';
$selected_loc_escaped = mysqli_real_escape_string($conn, $selected_loc);
$theaters_query = mysqli_query($conn, "SELECT * FROM theater WHERE Location = '$selected_loc_escaped' ORDER BY TheaterID ASC");
$modal_theaters = [];
if ($theaters_query) {
    while ($row = mysqli_fetch_assoc($theaters_query)) {
        $modal_theaters[] = $row;
    }
}

$theater_studios = [];
$studio_types_query = mysqli_query($conn, "SELECT TheaterID, Type FROM studio");
if ($studio_types_query) {
    while ($row = mysqli_fetch_assoc($studio_types_query)) {
        $theater_studios[$row['TheaterID']][] = $row['Type'];
    }
}
?>
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

    .selection-grid-container { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
    .btn-grid-option { background-color: #241414; border: 1px solid #4a2d2d; color: #ccc; border-radius: 8px; padding: 8px 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-align: center; min-width: 90px; }
    .btn-grid-option:hover { background-color: #381f1f; border-color: #d4af37; color: #fff; }
    .btn-grid-option.active { background: linear-gradient(135deg, #b30000 0%, #7e0000 100%); border-color: #d4af37 !important; color: #fff !important; box-shadow: 0 0 10px rgba(179, 0, 0, 0.4); }

    .date-strip-container {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 10px;
        scrollbar-width: thin;
        scrollbar-color: #3a2626 transparent;
    }
    .date-strip-container::-webkit-scrollbar {
        height: 6px;
    }
    .date-strip-container::-webkit-scrollbar-thumb {
        background: #3a2626;
        border-radius: 3px;
    }
    .date-item {
        flex: 0 0 auto;
        width: 70px;
        height: 70px;
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .date-item:hover:not(.disabled) {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(212, 175, 55, 0.4);
    }
    .date-item.active {
        background: linear-gradient(135deg, #d4af37 0%, #b8962e 100%);
        border-color: #d4af37 !important;
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.25);
    }
    .date-item.active .date-day-name {
        color: #000000 !important;
        font-weight: 700;
    }
    .date-item.active .date-day-num {
        color: #000000 !important;
        font-weight: 800;
    }
    .date-day-name {
        font-size: 11px;
        color: #888;
        margin-bottom: 2px;
        text-transform: capitalize;
    }
    .date-day-num {
        font-size: 18px;
        font-weight: bold;
        color: #fff;
    }
    .date-item.disabled {
        opacity: 0.2;
        cursor: not-allowed;
        pointer-events: none;
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
                            <?php if (!empty($modal_theaters)): foreach ($modal_theaters as $idx => $t): 
                                $checked = ($idx === 0) ? 'checked' : '';
                                $brand_class = 'brand-cinemapolis';
                                $brand_short = 'Tixly';
                                $sub_name = str_replace(['XXI ', 'CGV ', 'Tixly '], '', $t['Name']);
                                if (stripos($t['Name'], 'XXI') !== false) {
                                    $brand_class = 'brand-xxi';
                                    $brand_short = 'XXI';
                                } elseif (stripos($t['Name'], 'CGV') !== false) {
                                    $brand_class = 'brand-cgv';
                                    $brand_short = 'CGV';
                                }
                            ?>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check-custom" name="cinema" id="cinema-<?php echo $t['TheaterID']; ?>" value="<?php echo htmlspecialchars($t['Name']); ?>" onchange="updateStudioOptions()" <?php echo $checked; ?> data-theater-id="<?php echo $t['TheaterID']; ?>">
                                <label class="card-selection-option" for="cinema-<?php echo $t['TheaterID']; ?>">
                                    <span class="cinema-brand-title <?php echo $brand_class; ?>"><?php echo $brand_short; ?></span>
                                    <span class="cinema-brand-sub"><?php echo htmlspecialchars($sub_name); ?></span>
                                </label>
                            </div>
                            <?php endforeach; else: ?>
                            <div class="col-12 text-center text-muted">Tidak ada bioskop di kota ini.</div>
                            <?php endif; ?>
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
                        <div class="form-section-title">Pilih Tanggal</div>
                        <div class="date-strip-container" id="date-strip-container">
                            <span class="text-muted fs-7">Pilih Bioskop dan Studio Terlebih Dahulu...</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-section-title">Pilih Jam Tayang</div>
                        <div class="selection-grid-container" id="time-options-container">
                            <span class="text-muted fs-7">Pilih tanggal terlebih dahulu...</span>
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
    const theaterStudios = <?php echo json_encode($theater_studios); ?>;
    const cinemaPrices = {
        "XXI": { "Regular": 50000, "Velvet": 120000, "Gold Class": 150000 },
        "CGV": { "Regular": 35000, "Velvet": 90000, "Gold Class": 110000 },
        "Tixly": { "Regular": 40000, "Velvet": 100000, "Gold Class": 130000 }
    };

    let allSchedules = [];
    let selectedDateLabel = null;
    let selectedTime = null;
    let selectedScheduleItem = null;

    const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

    function generateDateStrip() {
        const container = document.getElementById('date-strip-container');
        container.innerHTML = '';
        
        // Generate 7 days starting from today
        for (let i = 0; i < 7; i++) {
            const d = new Date();
            d.setDate(d.getDate() + i);
            
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const dateNum = String(d.getDate()).padStart(2, '0');
            const dateString = `${year}-${month}-${dateNum}`; // YYYY-MM-DD
            
            const dayIndex = d.getDay();
            let dayName = dayNames[dayIndex];
            if (i === 0) {
                dayName = "Hari ini";
            }
            
            const dateItem = document.createElement('div');
            dateItem.className = 'date-item disabled';
            dateItem.id = `date-item-${dateString}`;
            dateItem.setAttribute('data-date', dateString);
            
            // Format Indonesian full date label
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            let formattedLabel = d.toLocaleDateString('id-ID', options);
            formattedLabel = formattedLabel.charAt(0).toUpperCase() + formattedLabel.slice(1);
            dateItem.setAttribute('data-label', formattedLabel);
            
            dateItem.innerHTML = `
                <span class="date-day-name">${dayName}</span>
                <span class="date-day-num">${d.getDate()}</span>
            `;
            
            dateItem.onclick = function() {
                if (dateItem.classList.contains('disabled')) return;
                
                document.querySelectorAll('#date-strip-container .date-item').forEach(item => {
                    item.classList.remove('active');
                });
                dateItem.classList.add('active');
                
                selectedDateLabel = dateItem.getAttribute('data-label');
                selectDateByRaw(dateString);
            };
            
            container.appendChild(dateItem);
        }
    }

    function fetchJadwal() {
        const movieTitle = document.getElementById('modal-movie-title').textContent;
        const checkedCinema = document.querySelector('input[name="cinema"]:checked');
        const checkedStudio = document.querySelector('input[name="studio_type"]:checked');
        const dateContainer = document.getElementById('date-strip-container');
        const timeContainer = document.getElementById('time-options-container');

        // Reset state
        allSchedules = [];
        selectedDateLabel = null;
        selectedTime = null;
        selectedScheduleItem = null;
        
        dateContainer.innerHTML = '<span class="text-muted fs-7">Mencari Jadwal...</span>';
        timeContainer.innerHTML = '<span class="text-muted fs-7">Pilih tanggal terlebih dahulu...</span>';

        if (!checkedCinema || !checkedStudio) {
            dateContainer.innerHTML = '<span class="text-muted fs-7">Pilih Bioskop & Studio Terlebih Dahulu...</span>';
            return;
        }

        const selectedCinema = checkedCinema.value;
        const selectedStudio = checkedStudio.value;

        fetch(`api/api_jadwal.php?movie=${encodeURIComponent(movieTitle)}&cinema=${encodeURIComponent(selectedCinema)}&type=${encodeURIComponent(selectedStudio)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    allSchedules = data;
                    
                    // Render date strip structure
                    generateDateStrip();
                    
                    // Enable items that have schedules
                    const availableDates = data.map(item => item.raw_date);
                    let hasAvailableDate = false;
                    
                    document.querySelectorAll('#date-strip-container .date-item').forEach(item => {
                        const itemDate = item.getAttribute('data-date');
                        if (availableDates.includes(itemDate)) {
                            item.classList.remove('disabled');
                            hasAvailableDate = true;
                        }
                    });
                    
                    if (hasAvailableDate) {
                        // Auto-select first available date
                        const firstAvailable = dateContainer.querySelector('.date-item:not(.disabled)');
                        if (firstAvailable) {
                            firstAvailable.click();
                        }
                    } else {
                        dateContainer.innerHTML = '<span class="text-danger fs-7">Jadwal tidak tersedia untuk 7 hari ke depan.</span>';
                        timeContainer.innerHTML = '<span class="text-muted fs-7">Pilih tanggal terlebih dahulu...</span>';
                    }
                } else {
                    dateContainer.innerHTML = '<span class="text-danger fs-7">Jadwal tidak tersedia</span>';
                    timeContainer.innerHTML = '<span class="text-muted fs-7">Pilih tanggal terlebih dahulu...</span>';
                }
            })
            .catch(error => {
                dateContainer.innerHTML = '<span class="text-danger fs-7">Gagal memuat jadwal</span>';
                timeContainer.innerHTML = '<span class="text-muted fs-7">Pilih tanggal terlebih dahulu...</span>';
            });
    }

    function selectDateByRaw(rawDate) {
        selectedDateLabel = null;
        selectedTime = null;
        selectedScheduleItem = null;
        
        const timeContainer = document.getElementById('time-options-container');
        timeContainer.innerHTML = '';
        
        const filteredTimes = allSchedules.filter(j => j.raw_date === rawDate);
        
        if (filteredTimes.length > 0) {
            // Find full date label from DOM
            const dateItem = document.getElementById(`date-item-${rawDate}`);
            if (dateItem) {
                selectedDateLabel = dateItem.getAttribute('data-label');
            } else {
                selectedDateLabel = filteredTimes[0].date_label;
            }
            
            filteredTimes.forEach(j => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn-grid-option';
                btn.textContent = j.time;
                btn.onclick = () => selectTime(j, btn);
                timeContainer.appendChild(btn);
            });
        } else {
            timeContainer.innerHTML = '<span class="text-danger fs-7">Jadwal tidak tersedia untuk tanggal ini. Silakan pilih tanggal lain.</span>';
        }
    }

    function selectTime(scheduleObj, btnElement) {
        selectedScheduleItem = scheduleObj;
        selectedTime = scheduleObj.time;
        
        // Remove active class from other time buttons
        document.querySelectorAll('#time-options-container .btn-grid-option').forEach(btn => {
            btn.classList.remove('active');
        });
        btnElement.classList.add('active');
    }

    function updateStudioPrices() {
        const checkedCinema = document.querySelector('input[name="cinema"]:checked');
        if (!checkedCinema) return;
        const selectedCinemaName = checkedCinema.value;
        
        let brand = "Tixly";
        if (selectedCinemaName.includes("XXI")) brand = "XXI";
        else if (selectedCinemaName.includes("CGV")) brand = "CGV";
        
        const prices = cinemaPrices[brand];
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

    function updateStudioOptions() {
        const checkedCinema = document.querySelector('input[name="cinema"]:checked');
        if (!checkedCinema) return;
        
        const theaterId = checkedCinema.getAttribute('data-theater-id');
        const availableTypes = theaterStudios[theaterId] || [];
        
        const types = ['Regular', 'Velvet', 'Gold Class'];
        let firstAvailableSelected = false;

        types.forEach(type => {
            const inputId = 'type-' + (type === 'Gold Class' ? 'gold' : type.toLowerCase());
            const input = document.getElementById(inputId);
            if (!input) return;
            const col = input.closest('.col-md-4');

            const isAvailable = availableTypes.includes(type) || (type === 'Gold Class' && availableTypes.includes('Gold Class'));
            
            if (isAvailable) {
                col.style.display = 'block';
                if (!firstAvailableSelected) {
                    input.checked = true;
                    firstAvailableSelected = true;
                }
            } else {
                col.style.display = 'none';
                input.checked = false;
            }
        });
        
        updateStudioPrices();
        fetchJadwal();
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('input[name="cinema"]').forEach(radio => {
            radio.addEventListener('change', updateStudioOptions);
        });

        document.querySelectorAll('input[name="studio_type"]').forEach(radio => {
            radio.addEventListener('change', fetchJadwal);
        });

        const bookingModal = document.getElementById('bookingModal');
        if (bookingModal) {
            bookingModal.addEventListener('show.bs.modal', function(event) {
                const triggerElement = event.relatedTarget;
                
                document.getElementById('modal-movie-title').textContent = triggerElement.getAttribute('data-title');
                document.getElementById('modal-movie-poster').src = triggerElement.getAttribute('data-poster');
                document.getElementById('hidden-movie-poster').value = triggerElement.getAttribute('data-poster');
                document.getElementById('modal-movie-duration').querySelector('span').textContent = triggerElement.getAttribute('data-duration') || 'N/A';

                updateStudioOptions();
            });
        }

        const confirmBtn = document.getElementById('confirm-booking-btn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                const checkedCinema = document.querySelector('input[name="cinema"]:checked');
                const checkedStudio = document.querySelector('input[name="studio_type"]:checked');
                
                if(!checkedCinema || !checkedStudio || !selectedDateLabel || !selectedTime || !selectedScheduleItem) {
                    alert("Maaf, silakan pilih bioskop, studio, tanggal, dan jam tayang terlebih dahulu.");
                    return;
                }

                const queryParams = new URLSearchParams({
                    movie: document.getElementById('modal-movie-title').textContent,
                    poster: document.getElementById('hidden-movie-poster').value,
                    duration: document.getElementById('modal-movie-duration').textContent.trim(),
                    cinema: checkedCinema.value,
                    type: checkedStudio.value,
                    price: checkedStudio.getAttribute('data-price'),
                    date: selectedScheduleItem.raw_date,
                    time: selectedScheduleItem.raw_time
                });

                window.location.href = 'booking.php?' + queryParams.toString();
            });
        }
    });
</script>