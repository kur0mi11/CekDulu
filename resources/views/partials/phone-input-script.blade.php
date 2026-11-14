<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-phone-input]').forEach(function (input) {
            var hint = document.querySelector(input.dataset.phoneInput);
            var defaultHint = hint ? hint.textContent : '';

            function read(raw) {
                var value = raw.trim();
                if (value === '') { return { state: 'empty' }; }
                if (/[^\d\s+\-().]/.test(value)) { return { state: 'invalid' }; }

                var digits = value.replace(/\D/g, '');
                if (digits.indexOf('62') === 0) { digits = '0' + digits.slice(2); }
                else if (digits.indexOf('8') === 0) { digits = '0' + digits; }

                return /^08\d{8,11}$/.test(digits)
                    ? { state: 'valid', value: digits }
                    : { state: 'invalid' };
            }

            function check() {
                var result = read(input.value);

                input.setCustomValidity('');
                if (hint) { hint.classList.remove('text-success', 'text-danger'); }

                if (result.state === 'empty') {
                    if (hint) { hint.textContent = defaultHint; }
                } else if (result.state === 'valid') {
                    if (hint) {
                        hint.textContent = 'Format sesuai. Akan diproses sebagai ' + result.value + '.';
                        hint.classList.add('text-success');
                    }
                } else {
                    if (hint) {
                        hint.textContent = 'Format belum sesuai. Gunakan nomor seluler Indonesia, contoh 081234567890.';
                        hint.classList.add('text-danger');
                    }
                    input.setCustomValidity('Format nomor tidak valid');
                }
            }

            input.addEventListener('input', function () {
                input.classList.remove('is-invalid'); // sembunyikan error server saat pengguna mengetik ulang
                check();
            });

            check();
        });
    });
</script>