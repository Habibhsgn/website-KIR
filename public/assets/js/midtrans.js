document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("submissionForm");
    const confirmSubmitButton = document.getElementById("confirmSubmitButton"); 
    const initialSubmitButton = document.getElementById("submitButton");       
    const submitUrl = form.action;

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

    const waitForSnap = (maxAttempts = 50, delay = 100) => {
        return new Promise((resolve, reject) => {
            let attempts = 0;
            const check = () => {
                if (typeof window.snap !== 'undefined' && typeof window.snap.pay === 'function') {
                    console.log('✅ Midtrans Snap ready.');
                    resolve(true);
                } else if (attempts < maxAttempts) {
                    attempts++;
                    setTimeout(check, delay); 
                } else {
                    reject(new Error("Gagal menginisialisasi Midtrans Snap setelah 5 detik."));
                }
            };
            check();
        });
    };

    // ----------------------------------------------------------------------
    // # LOGIKA PEMBAYARAN (Tombol: 'Bayar dan Ajukan')
    // ----------------------------------------------------------------------

    async function triggerPayment(event) {
        // Penting: Cegah submit dari tombol di modal
        event.preventDefault(); 

        const formData = new FormData(form);
        
        // Pencegahan Double Submit
        confirmSubmitButton.disabled = true; 
        confirmSubmitButton.textContent = 'Memproses Pembayaran...';

        hidePreviewModal(); 
        
        try {
            const csrfInput = document.querySelector('input[name="_token"]');
            const csrfToken = csrfInput ? csrfInput.value : '';

            // Kirim Data ke Backend (AJAX)
            const response = await fetch(submitUrl, {
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
                const snapToken = data.snap_token;
                
                await waitForSnap();
                    
                // Panggil Midtrans Pop-up 
                window.snap.pay(snapToken, {
                    onSuccess: function(result){
                        alert("Pembayaran Berhasil! Pesanan Anda telah diajukan.");
                        form.reset(); 
                        resetButton();
                    },
                    onPending: function(result){
                        alert("Pesanan Dibuat! Silakan selesaikan pembayaran. Anda dapat melakukan pengajuan baru.");
                        form.reset(); 
                        resetButton();
                    },
                    onError: function(result){
                        alert("Pembayaran Gagal. Silakan coba lagi.");
                        resetButton();
                    },
                    onClose: function(){
                        alert('Anda menutup jendela pembayaran. Status pesanan Anda adalah PENDING.');
                        resetButton();
                    }
                });
                
            } else {
                let errorMessage = data.message || "Terjadi kesalahan saat menyimpan data di server.";
                alert("Error Submission: " + errorMessage);
                resetButton();
            }

        } catch (error) {
            console.error('Fatal Error:', error);
            alert("Terjadi kesalahan sistem atau Snap tidak dapat dimuat: " + error.message);
            resetButton();
        }
    }

    if (confirmSubmitButton) {
        confirmSubmitButton.addEventListener("click", triggerPayment);
    }

    // ----------------------------------------------------------------------
    // # LOGIKA TAMPILKAN MODAL (Tombol: 'Ajukan Surat')
    // ----------------------------------------------------------------------

    if (initialSubmitButton) {
        initialSubmitButton.addEventListener('click', function(e) {
            // Jika Anda masih mendapat redirect, ini adalah baris yang perlu dipastikan berjalan
            e.preventDefault(); 
            
            let isFormValid = form.checkValidity();

            if (!isFormValid) {
                alert("Harap lengkapi semua kolom yang wajib diisi!");
                return;
            }
            
            try {
                const modalElement = document.getElementById('previewModal');
                const previewModal = new bootstrap.Modal(modalElement);
                // Tambahkan pengisian data preview di sini jika Anda sudah mengimplementasikannya

                previewModal.show();
            } catch (error) {
                alert("Error: Gagal menampilkan modal. Pastikan file Bootstrap JavaScript sudah dimuat.");
                console.error(error);
            }
        });
    }
});