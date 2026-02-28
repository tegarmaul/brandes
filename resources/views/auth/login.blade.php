<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Login — Brandes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --green-primary: #22C55E;
            --green-dark: #16A34A;
            --green-overlay: rgba(22, 163, 74, 0.45);
            --text-dark: #111827;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --input-bg: #FFFFFF;
            --radius: 10px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            width: 50%;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 36px 44px 44px;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("images/bg_form login.png") }}');
            background-size: cover;
            background-position: center;
            z-index: 0;
        }

        /* Dark gradient overlay top */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--Linear, linear-gradient(
            180deg,
            rgba(0, 0, 0, 0.85) 9.83%,
            rgba(0, 0, 0, 0.45) 48.7%,
            rgba(0, 166, 62, 0.80) 100%
            ));
            z-index: 1;
        }

        .left-panel > * {
            position: relative;
            z-index: 2;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            background: var(--green-primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 30px;
            height: 30px;
            fill: white;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        /* Hero text */
        .hero-text {
            margin-top: auto;
        }

        .hero-text h1 {
            font-size: 38px;
            font-weight: 785;
            color: var(--C-White, #FFFFFF);
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .hero-text h1 span {
            color: var(--C-Green, #00A63E);
        }

        .hero-text p {
            font-size: 18px;
            color: var(--C-White, #FFFFFF);
            line-height: 30px;
            margin-top: 14px;
            max-width: 380px;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 50%;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 72px;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Alert */
        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: var(--radius);
            padding: 12px 16px;
            color: #DC2626;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form fields */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #9CA3AF;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .input-icon svg {
            width: 18px;
            height: 18px;
        }

        .form-control {
            width: 100%;
            padding: 16px 14px 16px 42px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: inherit;
            color: var(--text-dark);
            background: var(--input-bg);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            appearance: none;
        }

        .form-control::placeholder {
            color: #9CA3AF;
        }

        .form-control:focus {
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
        }

        /* PIN toggle */
        .toggle-eye {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            cursor: pointer;
            color: #9CA3AF;
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.2s;
        }

        .toggle-eye:hover {
            color: var(--text-dark);
        }

        .toggle-eye svg {
            width: 19px;
            height: 19px;
        }

        /* Select */
        select.form-control {
            padding-right: 42px;
            cursor: pointer;
        }

        .select-wrapper {
            position: relative;
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6B7280;
        }

        .select-arrow svg {
            width: 18px;
            height: 18px;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--green-primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            margin-top: 10px;
            letter-spacing: 0.2px;
        }

        .btn-login:hover {
            background: var(--green-dark);
            box-shadow: 0 4px 20px rgba(34, 197, 94, 0.35);
        }

        .btn-login:active {
            transform: scale(0.99);
        }

        /* Footer */
        .form-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel {
                width: 100%;
                min-height: 220px;
                padding: 28px 28px 32px;
            }
            .right-panel {
                width: 100%;
                padding: 40px 28px;
            }
        }
    </style>
</head>
<body>

    {{-- ════════════════════════════════════
         LEFT PANEL — Hero & Branding
    ════════════════════════════════════ --}}
    <div class="left-panel">

        {{-- Logo --}}
        <div class="logo">
            <div class="logo-icon">
                <img src="{{ asset('images/logo_brandes.png') }}" alt="Brandes" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <span class="logo-text">BRANDES</span>
        </div>

        {{-- Hero Copy --}}
        <div class="hero-text">
            <h1>
                Sistem Monitoring<br>
                <span>Keamanan Brankas</span><br>
                Balai Desa Bengle
            </h1>
            <p>Sistem keamanan brankas arsip Desa Bengle dengan akses terenkripsi, autentikasi berlapis, dan pemantauan real-time.</p>
        </div>

    </div>

    {{-- ════════════════════════════════════
         RIGHT PANEL — Login Form
    ════════════════════════════════════ --}}
    <div class="right-panel">

        <div class="form-header">
            <h2>Selamat Datang</h2>
            <p>Silahkan masukan data anda yang sudah terdaftar di sistem<br>agar bisa mengakses.</p>
        </div>

        {{-- Error message --}}
        @if ($errors->any())
            <div class="alert-error">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            {{-- Nama --}}
            <div class="form-group">
                <label for="nama">Nama</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12.125C14.3312 12.125 16.484 12.6545 18.0859 13.5547C19.6586 14.4406 20.875 15.8005 20.875 17.5V17.6016C20.876 18.757 20.8833 20.2773 19.5527 21.3613C18.9827 21.8245 18.2211 22.1658 17.2354 22.4062L16.7988 22.5039C15.5935 22.7482 14.0302 22.875 12 22.875C9.96977 22.875 8.40552 22.7482 7.20215 22.5039C6.00149 22.2617 5.09877 21.8908 4.44824 21.3613C3.11773 20.2774 3.12401 18.7571 3.125 17.6016V17.5C3.125 15.8005 4.34141 14.4406 5.91504 13.5547L6.22168 13.3906C7.78595 12.5907 9.81539 12.125 12 12.125ZM12.001 13.875C9.91248 13.875 8.06651 14.3534 6.77246 15.0811C5.45199 15.8236 4.875 16.7139 4.875 17.5C4.875 18.8195 4.92197 19.4901 5.55273 20.0039C5.90118 20.2874 6.49971 20.5763 7.54883 20.7881L7.95898 20.8633C8.96079 21.029 10.2763 21.125 12 21.125C13.9697 21.125 15.4055 20.9998 16.4512 20.7881C17.5003 20.5763 18.0989 20.2873 18.4473 20.0029L18.5586 19.9053C19.0839 19.4042 19.125 18.7375 19.125 17.5C19.125 16.7138 18.5489 15.8236 17.2275 15.0811C15.9335 14.3534 14.0875 13.875 12.001 13.875ZM12 1.125C13.2929 1.125 14.533 1.63851 15.4473 2.55273C16.3615 3.46697 16.875 4.70707 16.875 6C16.875 7.29293 16.3615 8.53303 15.4473 9.44727C14.533 10.3615 13.2929 10.875 12 10.875C10.7071 10.875 9.46696 10.3615 8.55273 9.44727C7.63851 8.53303 7.125 7.29292 7.125 6C7.125 4.70708 7.63851 3.46697 8.55273 2.55273C9.46696 1.63851 10.7071 1.12501 12 1.125ZM12 2.875C11.1712 2.87501 10.3761 3.204 9.79004 3.79004C9.20401 4.37609 8.875 5.17121 8.875 6C8.875 6.82879 9.20401 7.62391 9.79004 8.20996C10.3761 8.796 11.1712 9.12499 12 9.125C12.8288 9.125 13.6239 8.79599 14.21 8.20996C14.796 7.62391 15.125 6.8288 15.125 6C15.125 5.1712 14.796 4.37609 14.21 3.79004C13.6239 3.20401 12.8288 2.875 12 2.875Z" fill="#101828" stroke="#101828" stroke-width="0.25"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        class="form-control"
                        placeholder="Masukan nama anda"
                        value="{{ old('nama') }}"
                        required
                        autocomplete="off"
                    >
                </div>
            </div>

            {{-- PIN --}}
            <div class="form-group">
                <label for="pin">PIN</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M8 10V8C8 5.239 9.239 3 12 3C14.761 3 16 5.239 16 8V10M3.5 17.8V13.2C3.5 12.08 3.5 11.52 3.718 11.093C3.90957 10.7163 4.21554 10.41 4.592 10.218C5.02 10.001 5.58 10.001 6.7 10.001H17.3C18.42 10.001 18.98 10.001 19.408 10.218C19.7843 10.4097 20.0903 10.7157 20.282 11.092C20.5 11.52 20.5 12.08 20.5 13.2V17.8C20.5 18.92 20.5 19.48 20.282 19.908C20.0903 20.2843 19.7843 20.5903 19.408 20.782C18.98 21 18.42 21 17.3 21H6.7C5.58 21 5.02 21 4.592 20.782C4.21569 20.5903 3.90974 20.2843 3.718 19.908C3.5 19.481 3.5 18.921 3.5 17.8Z" stroke="#101828" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        id="pin"
                        name="pin"
                        class="form-control"
                        placeholder="Masukan PIN anda"
                        required
                        autocomplete="off"
                        inputmode="numeric"
                        maxlength="6"
                    >
                    <button type="button" class="toggle-eye" onclick="togglePin()" id="eyeBtn" title="Tampilkan / sembunyikan PIN">
                        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Role --}}
            <div class="form-group">
                <label for="role">Role</label>
                <div class="input-wrapper select-wrapper">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                        </svg>
                    </span>
                    <select id="role" name="role" class="form-control" required>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    <span class="select-arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login">Masuk ke Dashboard</button>

        </form>

        <div class="form-footer">
            &copy; {{ date('Y') }}. Sistem Monitoring Keamanan Brankas
        </div>

    </div>

    <script>
        function togglePin() {
            const pinInput = document.getElementById('pin');
            const eyeIcon  = document.getElementById('eyeIcon');
            const isHidden = pinInput.type === 'password';

            pinInput.type = isHidden ? 'text' : 'password';

            // Swap icon — eye vs eye-slash
            eyeIcon.innerHTML = isHidden
                ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>`;
        }
    </script>

</body>
</html>