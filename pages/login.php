<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Internal – Lab Data Technologies</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#00A0D6',
            secondary: '#6AC259',
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    html { transition: opacity .16s ease; }
    html:not(.is-loaded) { opacity: 0; }
    
    .hero-gradient {
      background: linear-gradient(135deg, #00A0D6 0%, #6AC259 100%);
    }
    
    .gradient-accent {
      background: linear-gradient(135deg, #00A0D6 0%, #0078A6 100%);
    }
    
    .gradient-accent:hover {
      background: linear-gradient(135deg, #0078A6 0%, #005A7A 100%);
    }
    
    .input-focus:focus {
      transform: translateY(-1px);
      box-shadow: 0 10px 25px rgba(0,160,214,0.15);
    }
    
    /* Mobile-specific styles */
    @media (max-width: 768px) {
      /* Base layout adjustments */
      .min-h-screen {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }
      
      .relative.z-10 {
        flex: 1;
        display: flex;
        flex-direction: column;
      }
      
      /* Welcome section */
      .grid > div:first-child {
        padding: 3rem 1.5rem 1.5rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 70vh;
      }
      
      /* Login form section */
      .grid > div:last-child {
        background: white;
        border-radius: 2rem 2rem 0 0;
        padding: 2.5rem 1.5rem 3rem;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        margin-top: auto;
      }
      
      /* Text adjustments */
      h1 {
        font-size: 2rem;
        line-height: 1.2;
        margin-bottom: 1.5rem;
      }
      
      p {
        font-size: 1rem;
        line-height: 1.6;
        color: #4b5563;
      }
      
      /* Form container */
      .bg-white\/80 {
        background: white !important;
        padding: 0;
        box-shadow: none;
        border-radius: 0;
      }
      
      /* Form elements */
      .space-y-6 {
        gap: 1.25rem;
      }
      
      .input-focus, input[type="text"], input[type="password"] {
        height: 3rem;
        font-size: 1rem;
      }
      
      /* Button adjustments */
      button[type="submit"] {
        height: 3rem;
        font-size: 1rem;
        font-weight: 500;
      }
      
      /* Hide background decorations on mobile */
      .absolute.inset-0.opacity-5 {
        display: none;
      }
      
      /* Ensure full width on mobile */
      .max-w-md {
        max-width: 100%;
        width: 100%;
      }
      
      /* Improve touch targets */
      a, button, [type='button'], [type='submit'] {
        min-height: 3rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }
    }
    
    /* Logo size adjustments for all devices */
    .inline-flex.h-12.w-12 {
      height: 5rem;
      width: 5rem;
    }
    
    .animate-float {
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    .card-shadow {
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
  </style>
</head>
<body class="bg-white text-gray-900">
  <!-- Hero Section - Full Page -->
  <section class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 opacity-5">
      <div class="absolute top-20 left-20 w-64 h-64 bg-gradient-to-br from-[#00A0D6] to-[#6AC259] rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 right-20 w-96 h-96 bg-gradient-to-br from-[#6AC259] to-[#00A0D6] rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative z-10 min-h-screen flex items-center justify-center px-6 lg:px-8">
      <div class="w-full max-w-6xl">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <!-- Left Column - Branding -->
          <div class="text-center lg:text-left">
            
            <!-- Welcome Text -->
            <div class="mb-12">
              <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                Selamat Datang di<br>
                <span class="text-gradient">Portal Internal</span>
              </h1>
              
              <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                Akses eksklusif untuk anggota laboratorium. Kelola penelitian, publikasi, dan kolaborasi dalam satu platform terintegrasi.
              </p>
              
            </div>
          </div>
          
          <!-- Right Column - Login Form -->
          <div class="max-w-md mx-auto w-full">
            <div class="bg-white/80 backdrop-blur rounded-3xl shadow-xl border border-gray-100 p-8">
              <!-- Login Logo -->
              <div class="text-center mb-4">
                <span class="inline-flex h-12 w-12 rounded-xl to-blue-600 items-center justify-center group-hover:shadow-xl transition-all duration-300">
                  <img src="../assets/img/logo.png" alt="" class="w-full h-full object-cover rounded-xl">
                </span>
              </div>
              
              <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Masuk ke Portal</h2>
                <p class="text-gray-600">Laboratorium Data Technologies</p>
                <p class="text-sm text-gray-500 mt-1">Politeknik Negeri Malang</p>
              </div>

              <!-- Login Form -->
              <form id="loginForm" onsubmit="return handleLogin(event)" class="space-y-6">
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email atau Username</label>
                    <input id="email" 
                           name="email"
                           type="text" 
                           placeholder="Masukkan email atau username" 
                           class="input-focus w-full h-12 border border-gray-200 rounded-xl px-4 focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" 
                           required>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" 
                           name="password"
                           type="password" 
                           placeholder="Masukkan password" 
                           class="input-focus w-full h-12 border border-gray-200 rounded-xl px-4 focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" 
                           required>
                  </div>
                </div>

                <button type="submit" 
                        id="loginBtn" 
                        class="w-full h-12 gradient-accent text-white font-semibold rounded-xl hover:scale-[1.02] hover:shadow-lg transition-all duration-200">
                  <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Masuk ke Portal
                  </span>
                </button>
              </form>

              <!-- Error Message Container -->
              <div id="loginError" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-sm text-red-700" id="loginErrorMessage">Email atau password salah</span>
                </div>
              </div>

              <!-- Success Message Container -->
              <div id="loginSuccess" class="hidden mt-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="text-sm text-green-700" id="loginSuccessMessage">Login berhasil, mengarahkan...</span>
                </div>
              </div>

              <!-- Loading State -->
              <div id="loginLoading" class="hidden mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-center">
                  <svg class="animate-spin w-5 h-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span class="text-sm text-blue-700">Memproses login...</span>
                </div>
              </div>

              <!-- Back Link -->
              <div class="mt-8 text-center">
                <a href="../index.html" class="inline-flex items-center text-gray-600 hover:text-primary transition-colors text-sm font-medium">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Kembali ke Situs Utama
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
