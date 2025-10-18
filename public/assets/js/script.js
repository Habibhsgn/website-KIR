document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("submissionForm");
    const submitButton = document.getElementById("submitButton"); // Tombol 'Ajukan Surat' (Luar Modal)
    const checkboxes = document.querySelectorAll('input[name="jenis_surat[]"]');
    const hargaInput = document.getElementById("total_harga"); 
    const hargaDisplay = document.getElementById("total_harga_display"); 
    const confirmSubmitButton = document.getElementById("confirmSubmitButton"); // Tombol 'Bayar dan Ajukan' (Dalam Modal)
    let submitUrl = form.action; // Gunakan let agar bisa diubah

    // ----------------------------------------------------------------------
    // # HELPER FUNCTIONS
    // ----------------------------------------------------------------------

    function resetButton() {
        if (confirmSubmitButton) {
            confirmSubmitButton.disabled = false;
            confirmSubmitButton.textContent = 'Bayar dan Ajukan';
        }
    }

    function hidePreviewModal() {
        try {
            const myModalEl = document.getElementById('previewModal');
            if (myModalEl && typeof bootstrap !== 'undefined') {
                const myModal = bootstrap.Modal.getInstance(myModalEl) || new bootstrap.Modal(myModalEl);
                myModal.hide();
            }
        } catch (e) {
            console.warn("Gagal menyembunyikan modal. Pastikan Bootstrap JS termuat.");
        }
    }

    // ----------------------------------------------------------------------
    // # VALIDATION & PRICE CALCULATION LOGIC (Perbaikan di sini)
    // ----------------------------------------------------------------------

    function checkFormValidity() {
        let isValid = true;

        // 1. Check for at least one Jenis Surat selected
        const suratChecked = document.querySelectorAll('input[name="jenis_surat[]"]:checked');
        if (suratChecked.length === 0) {
            isValid = false;
        }

        // 2. Check all other required fields (menggunakan selector gabungan)
        const requiredFields = document.querySelectorAll('[required], .required-field');
        
        requiredFields.forEach(field => {
            if (field.tagName === 'SELECT') {
                // Check untuk elemen SELECT: value tidak boleh string kosong ""
                if (field.value === "") {
                    isValid = false;
                }
            } else if (field.type === 'radio') {
                // Check untuk elemen RADIO: Periksa apakah ada yang terpilih dalam grupnya
                // Cek hanya sekali untuk setiap grup radio
                if (!document.querySelector(`input[name="${field.name}"]:checked`)) {
                    isValid = false;
                }
            } else if (field.type !== 'checkbox') {
                // Check untuk input/textarea: value tidak boleh kosong
                if (field.value.trim() === "") {
                    isValid = false;
                }
            }
            // Catatan: Jika field adalah radio atau checkbox, iterasi akan dilanjutkan
            // untuk menghindari pengecekan berulang pada grup radio
        });
        
        // **Tambahan: Pastikan group radio yang required terdeteksi**
        // Jika radio di-load dinamis atau tidak memiliki attribute 'required'
        const requiredRadioGroups = ['jenis_kelamin']; // Tambahkan nama group radio lain jika ada
        requiredRadioGroups.forEach(groupName => {
            if (!document.querySelector(`input[name="${groupName}"]:checked`)) {
                isValid = false;
            }
        });


        submitButton.disabled = !isValid;
    }

    // Add event listeners for instant validation feedback
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', checkFormValidity);
        field.addEventListener('change', checkFormValidity);
    });

    // --- Total Price Calculation ---

    function calculateTotalPrice() {
        let total = 0;
        document.querySelectorAll('input[name="jenis_surat[]"]:checked').forEach(
            item => {
                const harga = parseInt(item.getAttribute("data-harga"));
                if (!isNaN(harga)) {
                    total += harga;
                }
            }
        );
        hargaInput.value = total; 
        hargaDisplay.value = total.toLocaleString("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        });

        checkFormValidity();
    }

    checkboxes.forEach(cb => {
        cb.addEventListener("change", calculateTotalPrice);
    });

    calculateTotalPrice(); // Initial calculation
    checkFormValidity(); // Initial validity check

    // ----------------------------------------------------------------------
    // # PREVIEW MODAL LOGIC (Tombol 'Ajukan Surat' - Luar Modal)
    // ----------------------------------------------------------------------

    submitButton.addEventListener("click", function(e) { 
        e.preventDefault(); 
        
        if (submitButton.disabled) {
            // Jika tombol masih disabled, panggil reportValidity untuk feedback native browser
            form.reportValidity(); 
            console.error("Harap lengkapi semua data yang wajib diisi sebelum melanjutkan.");
            return;
        }
        
        showPreviewModal();
    });

    


    function showPreviewModal() {
        const previewContainer = document.getElementById('modal-data-preview');
        let html = `
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <tbody>
        `;

        const getSelectText = (id) => {
            const select = document.getElementById(id);
            if (!select || select.selectedIndex === -1 || select.value === "") return '';
            const text = select.options[select.selectedIndex].textContent;
            return text.replace(/-- Pilih [^--]+ --/g, '').trim();
        };

        const fields = [
            { id: 'jenis_surat', label: 'Jenis Surat', value: Array.from(document.querySelectorAll('input[name="jenis_surat[]"]:checked')).map(c => c.nextElementSibling.textContent).join(', ') },
            { id: 'total_harga_display', label: 'Total Harga', value: hargaDisplay.value },
            { id: 'nama', label: 'Nama', value: document.getElementById('nama')?.value || '' },
            { id: 'jenis_kelamin', label: 'Jenis Kelamin', value: document.querySelector('input[name="jenis_kelamin"]:checked')?.value || '' },
            { id: 'nik', label: 'NIK', value: document.getElementById('nik')?.value || '' },
            { id: 'tempat_lahir', label: 'Tempat Lahir', value: document.getElementById('tempat_lahir')?.value || '' },
            { id: 'tanggal_lahir', label: 'Tanggal Lahir', value: document.getElementById('tanggal_lahir')?.value || '' },
            { id: 'pendidikan', label: 'Pendidikan', value: getSelectText('pendidikan') },
            { id: 'provinsi', label: 'Provinsi', value: getSelectText('provinsi') },
            { id: 'kabupaten', label: 'Kabupaten/Kota', value: getSelectText('kabupaten') },
            { id: 'kecamatan', label: 'Kecamatan', value: getSelectText('kecamatan') },
            { id: 'desa', label: 'Desa/Kelurahan', value: getSelectText('desa') },
            { id: 'pekerjaan', label: 'Pekerjaan', value: document.getElementById('pekerjaan')?.value || '-' },
            { id: 'status', label: 'Status Pernikahan', value: getSelectText('status') },
            { id: 'agama', label: 'Agama', value: getSelectText('agama') },
            { id: 'no_hp', label: 'No HP / WA', value: document.getElementById('no_hp')?.value || '' },
            { id: 'email', label: 'Email', value: document.getElementById('email')?.value || '-' },
            { id: 'keperluan', label: 'Keperluan Surat', value: document.getElementById('keperluan')?.value || '' },
            { id: 'alamat_detail', label: 'Detail Alamat', value: document.getElementById('alamat_detail')?.value || '-' }
        ];

        fields.forEach(field => {
            if (field.value.trim() !== '' && field.value.trim() !== '-') {
                html += `
                    <tr>
                        <th style="width: 40%; background-color:#f8f9fa;">${field.label}</th>
                        <td>${field.value}</td>
                    </tr>
                `;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        previewContainer.innerHTML = html;

        try {
            const myModal = new bootstrap.Modal(document.getElementById('previewModal'));
            myModal.show();
        } catch (e) {
            console.error("Error menampilkan modal. Pastikan Bootstrap JS dimuat.", e);
        }
    }


    // ----------------------------------------------------------------------
    // # ACTUAL SUBMISSION (Tombol 'Bayar dan Ajukan' - Dalam Modal)
    // ----------------------------------------------------------------------

    async function triggerPayment(event) {
        event.preventDefault();

        const formData = new FormData(form);
        
        confirmSubmitButton.disabled = true; 
        confirmSubmitButton.textContent = 'Mengarahkan ke Pembayaran...';

        hidePreviewModal(); 
        // 🔔 Tambahan: tampilkan notifikasi sementara tanpa blokir
        const processingNotice = document.createElement('div');
        processingNotice.id = 'processingNotice';
        processingNotice.innerHTML = `
        <div style="
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-size: 1.2rem;
            z-index: 9999;
        ">
            <div style="
            background: rgba(0,0,0,0.8);
            padding: 30px 40px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            ">
            <div class="spinner-border text-light mb-3" role="status"></div>
            <p><b>Jangan tutup halaman ini!</b></p>
            <p>Pembayaran sedang diproses...</p>
            </div>
        </div>
        `;
        document.body.appendChild(processingNotice);

        
        // --- START PERBAIKAN MIXED CONTENT ---
        let finalSubmitUrl = submitUrl;
        
        // Jika laman dimuat via HTTPS (Ngrok), paksa URL AJAX menjadi HTTPS juga.
        if (window.location.protocol === 'https:' && finalSubmitUrl.startsWith('http://')) {
            finalSubmitUrl = finalSubmitUrl.replace('http://', 'https://');
            console.log("URL AJAX diubah ke HTTPS:", finalSubmitUrl);
        }
        // --- END PERBAIKAN MIXED CONTENT ---

        try {
            const csrfInput = document.querySelector('input[name="_token"]');
            const csrfToken = csrfInput ? csrfInput.value : '';

            // Gunakan finalSubmitUrl yang sudah dikoreksi protokolnya
            const response = await fetch(finalSubmitUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken 
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const redirectUrl = data.redirect_url;
                
                if (redirectUrl) {
                    console.log("Transaksi berhasil dibuat, mengalihkan ke:", redirectUrl);
                    window.location.replace(redirectUrl); 
                } else {
                    console.error("Kesalahan: Backend tidak menyediakan redirect_url.");
                    alert("Gagal memproses pembayaran: URL Midtrans tidak tersedia.");
                    resetButton();
                }
                
            } else {
                document.getElementById('processingNotice')?.remove();
                let errorMessage = data.message || "Terjadi kesalahan saat menyimpan data di server.";
                alert("Error Submission: " + errorMessage);
                console.error("Error Submission (Backend): " + errorMessage, data.errors);
                resetButton();
            }

        } catch (error) {
            console.error('Fatal Error (AJAX atau Koneksi):', error);
            // Menampilkan error.message agar lebih jelas
            document.getElementById('processingNotice')?.remove();

            alert("Terjadi kesalahan sistem atau koneksi: " + (error.message || "Silakan cek konsol browser.")); 
            resetButton();
        }
    }

    confirmSubmitButton.addEventListener("click", triggerPayment); 

    // ----------------------------------------------------------------------
    // # WILAYAH (REGION) LOADERS 
    // ----------------------------------------------------------------------



    // Load Provinsi
    fetch("/wilayah/provinsi")
        .then(res => res.json())
        .then(data => {
            let prov = document.getElementById("provinsi");
            prov.innerHTML = `<option value="">-- Pilih Provinsi --</option>`;
            data.forEach(d => {
                prov.innerHTML += `<option value="${d.id}">${d.provinsi}</option>`;
            });
            checkFormValidity();
        })
        .catch(err => console.error("Error load provinsi:", err));

    // Provinsi -> Kabupaten
    document.getElementById("provinsi").addEventListener("change", function() {
        let id = this.value;
        document.getElementById("kabupaten").innerHTML = `<option value="">Memuat...</option>`;
        document.getElementById("kecamatan").innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
        document.getElementById("desa").innerHTML = `<option value="">-- Pilih Desa/Kelurahan --</option>`;
        checkFormValidity();
        
        if (!id) return; 

        fetch("/wilayah/kabupaten/" + id)
            .then(res => res.json())
            .then(data => {
                let kab = document.getElementById("kabupaten");
                kab.innerHTML = `<option value="">-- Pilih Kabupaten/Kota --</option>`;
                data.forEach(d => {
                    kab.innerHTML += `<option value="${d.id}">${d.kabupaten_kota}</option>`;
                });
                checkFormValidity();
            })
            .catch(err => console.error("Error load kabupaten:", err));
    });

    // Kabupaten -> Kecamatan
    document.getElementById("kabupaten").addEventListener("change", function() {
        let id = this.value;
        document.getElementById("kecamatan").innerHTML = `<option value="">Memuat...</option>`;
        document.getElementById("desa").innerHTML = `<option value="">-- Pilih Desa/Kelurahan --</option>`;
        checkFormValidity();

        if (!id) return; 

        fetch("/wilayah/kecamatan/" + id)
            .then(res => res.json())
            .then(data => {
                let kec = document.getElementById("kecamatan");
                kec.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
                data.forEach(d => {
                    kec.innerHTML += `<option value="${d.id}">${d.kecamatan}</option>`;
                });
                checkFormValidity();
            })
            .catch(err => console.error("Error load kecamatan:", err));
    });

    // Kecamatan -> Kelurahan
    document.getElementById("kecamatan").addEventListener("change", function() {
        let id = this.value;
        document.getElementById("desa").innerHTML = `<option value="">Memuat...</option>`;
        checkFormValidity();

        if (!id) return; 

        fetch("/wilayah/kelurahan/" + id)
            .then(res => res.json())
            .then(data => {
                let desa = document.getElementById("desa");
                desa.innerHTML = `<option value="">-- Pilih Desa/Kelurahan --</option>`;

                data.forEach(d => {
                    desa.innerHTML +=
                        `<option value="${d.id}">${d.kelurahan} - ${d.kd_pos}</option>`;
                });
                checkFormValidity();
            })
            .catch(err => console.error("Error load kelurahan:", err));
    });
});

