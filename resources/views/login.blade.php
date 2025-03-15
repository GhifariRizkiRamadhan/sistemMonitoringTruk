<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Harry</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <!-- Tambahkan link CDN atau file Tailwind Anda -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes slideInFromBottom {
            0% {
                transform: translateY(50px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .slide-in-from-bottom {
            animation: slideInFromBottom 0.8s ease-out;
        }
        .hidden {
            opacity: 0;
        }
        .visible {
            opacity: 1;
            animation: slideInFromBottom 1s ease-out;
        }
        @keyframes glowing {
            0% {
                text-shadow: 
                    0 0 3px rgba(0, 0, 0, 0.3), 
                    0 0 6px rgba(0, 0, 0, 0.2);
            }
            50% {
                text-shadow: 
                    0 0 5px rgba(0, 0, 0, 0.4), 
                    0 0 8px rgba(0, 0, 0, 0.3);
            }
            100% {
                text-shadow: 
                    0 0 3px rgba(0, 0, 0, 0.3), 
                    0 0 6px rgba(0, 0, 0, 0.2);
            }
        }
        .glowing-text {
            animation: glowing 2s ease-in-out infinite alternate;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const formContainer = document.querySelector(".relative.w-96");
            formContainer.classList.add("hidden");

            setTimeout(() => {
                formContainer.classList.remove("hidden");
                formContainer.classList.add("visible");
            }, 500); // Delay untuk animasi setelah page load
        });
    </script>
</head>
<body class="h-screen flex items-center justify-center"
      style="
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                    url('{{ asset('img/pt.png') }}');
        background-size: cover; 
        background-position: center;
      ">
    <div class="relative w-96 bg-white rounded-lg shadow-lg hidden" style="overflow: hidden;">
        <!-- Bagian Atas dengan Latar Gambar -->
        <div class="relative text-center py-12" 
             style="background-image: url('{{ asset('img/logo.png') }}'); background-size: cover; background-position: center;">
            <!-- Overlay hitam semi-transparan -->
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            <div class="relative">
                <!-- Bisa disesuaikan tautan 'Sign Up' jika tidak ada halaman registrasi -->
                <p class="text-sm font-medium text-white" style="color:rgba(255, 255, 255, 0);">Don't you have an account?</p>
                <p class="text-2xl font-medium text-white" style="color:rgba(255, 255, 255, 0);">Selamat Datang</p>
                <p class="text-sm font-medium text-white" style="color:rgba(255, 255, 255, 0);">Don't you have an account?</p>
                <!-- <a href="register" 
                   class="bg-white bg-opacity-70 text-red-500 px-4 py-2 mt-2 inline-block rounded-full shadow-md 
                          text-sm font-medium transition duration-300 hover:bg-white">
                    Sign Up
                </a> -->
            </div>
        </div>
        <!-- Bagian Form Login -->
        <div class="px-8 py-6" style="background: rgba(255, 255, 255, 0.8);">
            <h2 class="text-center text-xl font-bold mb-4 glowing-text" style="color:rgba(0, 0, 0, 0.7); font-family: 'Bangers';">
                SISTEM MONITORING TRUK PT HARRY
            </h2>
            
            <!-- Tampilkan error jika ada -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form class="space-y-4" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="relative">
                    <label for="username" class="sr-only">Username</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" 
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m8-7
                                     a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                    </div>
                    <input type="text" id="username" name="username" required 
                           class="block w-full pl-10 py-2 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-red-500 focus:border-red-500" 
                           placeholder="Username">
                </div>
                <div class="relative">
                    <label for="password" class="sr-only">Password</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" 
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 15v3m0 0v3m0-3h3m-3 
                                     0H9m3-3a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                    </div>
                    <input type="password" id="password" name="password" required 
                           class="block w-full pl-10 py-2 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-red-500 focus:border-red-500" 
                           placeholder="Password">
                </div>
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent 
                                   rounded-md shadow-sm text-sm font-medium text-white bg-black 
                                   hover:bg-red-500 focus:outline-none focus:ring-2 
                                   focus:ring-offset-2 focus:ring-red-500 transition duration-300">
                        LOGIN
                    </button>
                </div>
            </form>
            
            <!-- Tautan Forgot Password jika diperlukan -->
            <!-- <div class="mt-4 text-center">
                <a href="forgotPassword" class="text-sm text-black hover:text-red-500 transition duration-300">
                    Forgot your Password?
                </a>
            </div> -->
        </div>
    </div>
</body>
</html>