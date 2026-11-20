<script>
    (function () {
        function togglePasswordVisibility(target, btn) {
            var input = null;
            if (typeof target === 'string') {
                if (target.startsWith('#') || target.startsWith('.')) {
                    input = document.querySelector(target);
                } else {
                    input = document.getElementById(target) || document.querySelector('input[name="' + target + '"]');
                }
            } else if (target instanceof HTMLElement) {
                input = target;
            }

            if (!input && btn) {
                var group = btn.closest('.input-group') || btn.parentElement;
                if (group) {
                    input = group.querySelector('input[type="password"], input[type="text"]');
                }
            }

            if (!input) return;

            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            if (btn) {
                var icon = btn.querySelector('i');
                if (icon) {
                    if (isPassword) {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                }
                var newLabel = isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi';
                btn.setAttribute('aria-label', newLabel);
                btn.setAttribute('title', newLabel);
            }
        }

        window.togglePasswordVisibility = togglePasswordVisibility;

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-toggle-password]');
            if (!btn) return;

            e.preventDefault();
            var target = btn.getAttribute('data-toggle-password');
            togglePasswordVisibility(target, btn);
        });
    })();
</script>
