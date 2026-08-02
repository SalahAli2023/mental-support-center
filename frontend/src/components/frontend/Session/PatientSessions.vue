<template>
  <div>
    <Header />
    <div class="min-h-screen bg-gradient-to-b from-[#fdfaf5] via-white to-white">
      <!-- Hero Section -->
      <section class="relative overflow-hidden bg-gradient-to-l from-[#9EBF3B]/15 via-white to-[#D6A29A]/10 pt-32">
        <div class="hero-shape hero-shape-left"></div>
        <div class="hero-shape hero-shape-right"></div>
        <div class="max-w-7xl mx-auto px-4 py-12">
          <div class="grid lg:grid-cols-2 gap-8 items-center">
            <div>
              <p class="text-sm uppercase tracking-[0.3em] text-brand-500 mb-4">
                {{ currentLanguage === 'ar' ? 'جلسات الدعم' : 'Support Sessions' }}
              </p>
              <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-snug mb-4">
                <span v-if="!isAuthenticated">
                  {{ currentLanguage === 'ar' ? 'إدارة جلساتك العلاجية' : 'Manage your therapy sessions' }}
                </span>
                <span v-else-if="isTherapist">
                  {{ currentLanguage === 'ar' ? 'لوحة المعالج - إدارة الجلسات' : 'Therapist dashboard – manage sessions'
                  }}
                </span>
                <span v-else>
                  {{
                    currentLanguage === 'ar'
                      ? 'إدارة شاملة لجلساتك العلاجية'
                      : 'Comprehensive management of your sessions'
                  }}
                </span>
              </h1>
              <p class="text-gray-600 text-lg leading-relaxed mb-6">
                <span v-if="!isAuthenticated">
                  {{
                    currentLanguage === 'ar'
                      ? 'قم بتسجيل الدخول أو إنشاء حساب جديد للبدء في حجز الجلسات العلاجية ومتابعة جلساتك.'
                      : 'Log in or create a new account to start booking and tracking your therapy sessions.'
                  }}
                </span>
                <span v-else-if="isTherapist">
                  <template v-if="currentLanguage === 'ar'">
                    إدارة جلساتك مع المرضى، تتبع الجلسات القادمة، ومراجعة الجلسات السابقة في مكان واحد.
                  </template>
                  <template v-else>
                    Manage your sessions with patients, track upcoming appointments, and review past sessions in one
                    place.
                  </template>
                </span>
                <span v-else>
                  <template v-if="currentLanguage === 'ar'">
                    تتبع جلساتك القادمة، راجع جلساتك السابقة، وابقَ على اتصال مع معالجك وفريق الدعم بسهولة في لوحة
                    واحدة.
                  </template>
                  <template v-else>
                    Track upcoming sessions, review previous ones, and stay connected with your therapist and support
                    team in a single dashboard.
                  </template>
                </span>
              </p>
              <div class="flex flex-wrap gap-3">
                <router-link v-if="isAuthenticated && !isTherapist" to="/booking"
                  class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white bg-brand-500 hover:bg-brand-600 transition shadow-lg shadow-brand-500/30">
                  {{ currentLanguage === 'ar' ? 'احجز جلسة جديدة' : 'Book a new session' }}
                  <i class="fas fa-arrow-left text-sm"></i>
                </router-link>
                <router-link v-if="!isAuthenticated" to="/register"
                  class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white bg-brand-500 hover:bg-brand-600 transition shadow-lg shadow-brand-500/30">
                  <i class="fas fa-user-plus text-sm"></i>
                  {{ currentLanguage === 'ar' ? 'إنشاء حساب جديد' : 'Create a new account' }}
                </router-link>
                <router-link v-if="!isAuthenticated" to="/login"
                  class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-brand-300 text-brand-600 hover:bg-white transition">
                  <i class="fas fa-sign-in-alt text-sm"></i>
                  {{ currentLanguage === 'ar' ? 'تسجيل الدخول' : 'Log in' }}
                </router-link>
                <button v-if="isAuthenticated" @click="showSupportModal = true"
                  class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-brand-300 text-brand-600 hover:bg-white transition">
                  {{ currentLanguage === 'ar' ? 'تواصل مع الدعم' : 'Contact support' }}
                  <i class="fas fa-headset text-sm"></i>
                </button>
              </div>
            </div>
            <div class="relative">
              <div class="glass-card p-6 rounded-3xl shadow-xl backdrop-blur bg-white/80 border border-white/40">
                <div class="flex items-center gap-4 mb-6">
                  <div class="avatar-frame">
                    <img v-if="patientAvatar" :src="patientAvatar" alt="Patient"
                      class="rounded-full w-16 h-16 object-cover">
                    <div v-else class="default-avatar large patient">
                      <i class="fas fa-user"></i>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">
                      {{ currentLanguage === 'ar' ? 'مرحباً' : 'Welcome' }}
                    </p>
                    <p class="text-xl font-semibold text-gray-900">
                      {{
                        isAuthenticated
                          ? (patientName || (currentLanguage === 'ar' ? 'ضيف' : 'Guest'))
                          : currentLanguage === 'ar'
                            ? 'ضيف'
                            : 'Guest'
                      }}
                    </p>
                    <p v-if="isAuthenticated" class="text-sm text-gray-500 flex items-center gap-2">
                      <i class="fas fa-calendar-check text-brand-500"></i>
                      <span v-if="isTherapist">
                        {{
                          currentLanguage === 'ar'
                            ? `${therapist.totalSessions} جلسة`
                            : `${therapist.totalSessions} sessions`
                        }}
                      </span>
                      <span v-else>
                        {{
                          currentLanguage === 'ar'
                            ? `${patient.totalSessions} جلسات مكتملة`
                            : `${patient.totalSessions} completed sessions`
                        }}
                      </span>
                    </p>
                    <p v-else class="text-sm text-gray-500">
                      <i class="fas fa-info-circle text-brand-500"></i>
                      {{
                        currentLanguage === 'ar'
                          ? 'يرجى تسجيل الدخول لعرض جلساتك'
                          : 'Please log in to view your sessions'
                      }}
                    </p>
                    <p v-if="isTherapist && therapist.specialization && isAuthenticated"
                      class="text-sm text-gray-500 mt-1">
                      {{ therapist.specialization }}
                    </p>
                  </div>
                </div>
                <div v-if="isAuthenticated" class="grid grid-cols-3 gap-3 text-center">
                  <div class="stat-card">
                    <p class="text-sm text-gray-500">{{ isTherapist ? 'المرضى' : 'الحضور' }}</p>
                    <p class="text-2xl font-bold text-gray-900">
                      <span v-if="isTherapist">{{ therapist.totalPatients }}</span>
                      <span v-else>{{ patient.attendanceRate }}%</span>
                    </p>
                  </div>
                  <div class="stat-card">
                    <p class="text-sm text-gray-500">{{ isTherapist ? 'معدل الحضور' : 'التقدم' }}</p>
                    <p class="text-2xl font-bold text-gray-900">
                      <span v-if="isTherapist">{{ therapist.attendanceRate }}%</span>
                      <span v-else>{{ patient.progress }}%</span>
                    </p>
                  </div>
                  <div class="stat-card">
                    <p class="text-sm text-gray-500">الجلسة القادمة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ upcomingSessions[0]?.time || '--' }}</p>
                  </div>
                </div>
                <div v-else class="text-center py-4">
                  <p class="text-sm text-gray-500">قم بتسجيل الدخول لعرض إحصائياتك</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto px-4 py-10">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-16">
          <i class="fas fa-spinner fa-spin text-4xl text-brand-500 mb-4"></i>
          <p class="text-gray-600">
            {{ currentLanguage === 'ar' ? 'جاري تحميل البيانات...' : 'Loading data...' }}
          </p>
        </div>

        <!-- Guest View (Not Logged In) -->
        <div v-else-if="!isAuthenticated" class="text-center py-16">
          <div class="max-w-2xl mx-auto">
            <div class="mb-8">
              <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100 mb-6">
                <i class="fas fa-user text-4xl text-gray-400"></i>
              </div>
              <h2 class="text-3xl font-bold text-gray-900 mb-4">
                {{ currentLanguage === 'ar' ? 'مرحباً، أنت ضيف' : 'Welcome, you are a guest' }}
              </h2>
              <p class="text-lg text-gray-600 mb-8">
                {{
                  currentLanguage === 'ar'
                    ? 'لتتمكن من حجز الجلسات ومتابعة جلساتك العلاجية، يرجى تسجيل الدخول أو إنشاء حساب جديد.'
                    : 'To book and track your therapy sessions, please log in or create a new account.'
                }}
              </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <router-link to="/login"
                class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-white bg-brand-500 hover:bg-brand-600 transition shadow-lg shadow-brand-500/30 font-semibold">
                <i class="fas fa-sign-in-alt"></i>
                {{ currentLanguage === 'ar' ? 'تسجيل الدخول' : 'Log in' }}
              </router-link>
              <router-link to="/register"
                class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl border-2 border-brand-500 text-brand-600 hover:bg-brand-50 transition font-semibold">
                <i class="fas fa-user-plus"></i>
                {{ currentLanguage === 'ar' ? 'إنشاء حساب جديد' : 'Create a new account' }}
              </router-link>
            </div>

            <div class="mt-12 p-6 bg-gray-50 rounded-xl border border-gray-200">
              <h3 class="text-xl font-semibold text-gray-900 mb-4">
                {{
                  currentLanguage === 'ar'
                    ? 'ما يمكنك فعله بعد التسجيل:'
                    : 'What you can do after registering:'
                }}
              </h3>
              <ul class="text-right space-y-3 text-gray-700"
                :class="currentLanguage === 'ar' ? 'text-right' : 'text-left'">
                <li class="flex items-center gap-3" :class="currentLanguage === 'ar' ? 'flex-row-reverse' : ''">
                  <i class="fas fa-check-circle text-brand-500"></i>
                  <span>
                    {{
                      currentLanguage === 'ar'
                        ? 'حجز جلسات علاجية مع معالجين متخصصين'
                        : 'Book therapy sessions with specialized therapists'
                    }}
                  </span>
                </li>
                <li class="flex items-center gap-3" :class="currentLanguage === 'ar' ? 'flex-row-reverse' : ''">
                  <i class="fas fa-check-circle text-brand-500"></i>
                  <span>
                    {{
                      currentLanguage === 'ar'
                        ? 'متابعة جلساتك القادمة والسابقة'
                        : 'Track your upcoming and past sessions'
                    }}
                  </span>
                </li>
                <li class="flex items-center gap-3" :class="currentLanguage === 'ar' ? 'flex-row-reverse' : ''">
                  <i class="fas fa-check-circle text-brand-500"></i>
                  <span>
                    {{
                      currentLanguage === 'ar'
                        ? 'إجراء اختبارات نفسية وتقييمات'
                        : 'Take psychological tests and assessments'
                    }}
                  </span>
                </li>
                <li class="flex items-center gap-3" :class="currentLanguage === 'ar' ? 'flex-row-reverse' : ''">
                  <i class="fas fa-check-circle text-brand-500"></i>
                  <span>
                    {{
                      currentLanguage === 'ar'
                        ? 'الوصول إلى مكتبة الموارد والمقالات'
                        : 'Access the resources and articles library'
                    }}
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-6">
          <!-- Profile Section -->
          <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-4">
              <!-- Profile Card -->
              <div class="glass-panel p-6 rounded-2xl shadow-lg space-y-6">
                <!-- Profile Header -->
                <div class="text-center mb-4">
                  <div class="avatar-container large mb-4">
                    <img v-if="isTherapist && therapist.avatar" :src="therapist.avatar" alt="Therapist"
                      class="w-24 h-24 rounded-full mx-auto border-4 border-white shadow-lg object-cover">
                    <img v-else-if="!isTherapist && patient.avatar" :src="patient.avatar" alt="Patient"
                      class="w-24 h-24 rounded-full mx-auto border-4 border-white shadow-lg object-cover">
                    <div v-else class="default-avatar large" :class="isTherapist ? 'therapist' : 'patient'">
                      <i :class="isTherapist ? 'fas fa-user-md' : 'fas fa-user'"></i>
                    </div>
                  </div>
                  <h2 class="font-semibold text-gray-900 mb-2">
                    <span v-if="isTherapist">{{ therapist.name }}</span>
                    <span v-else>{{ patient.name }}</span>
                  </h2>
                  <p v-if="!isTherapist && patient.age" class="text-gray-500 text-sm">{{ patient.age }} سنة</p>
                  <p v-if="isTherapist && therapist.specialization" class="text-gray-500 text-sm">{{
                    therapist.specialization }}</p>
                </div>

                <!-- Profile Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                  <div class="mini-stat">
                    <p class="stat-value">
                      <span v-if="isTherapist">{{ therapist.totalSessions }}</span>
                      <span v-else>{{ patient.totalSessions }}</span>
                    </p>
                    <p class="stat-label">الجلسات</p>
                  </div>
                  <div class="mini-stat">
                    <p class="stat-value">
                      <span v-if="isTherapist">{{ therapist.attendanceRate }}%</span>
                      <span v-else>{{ patient.attendanceRate }}%</span>
                    </p>
                    <p class="stat-label">الحضور</p>
                  </div>
                </div>

                <!-- Progress -->
                <div v-if="!isTherapist" class="mb-4">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">تقدم العلاج</span>
                    <span class="text-sm text-brand-500">{{ patient.progress }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-brand-500 h-2 rounded-full transition-all duration-300"
                      :style="{ width: patient.progress + '%' }"></div>
                  </div>
                </div>

                <!-- Therapist Stats -->
                <div v-if="isTherapist" class="mb-4">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">عدد المرضى</span>
                    <span class="text-sm text-brand-500">{{ therapist.totalPatients }}</span>
                  </div>
                </div>

                <!-- Current Therapist -->
                <div class="border-t border-gray-200 pt-4">
                  <h4 class="font-medium text-gray-900 mb-4 flex items-center gap-2 justify-center">
                    <i class="fas fa-user-md text-brand-500"></i>
                    المعالج الحالي
                  </h4>
                  <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="avatar-container">
                      <img v-if="patient.therapist.avatar" :src="patient.therapist.avatar" :alt="patient.therapist.name"
                        class="w-14 h-14 rounded-full object-cover border-2 border-white shadow">
                      <div v-else class="default-avatar therapist">
                        <i class="fas fa-user-md"></i>
                      </div>
                    </div>
                    <div class="flex-1 mr-3">
                      <p class="font-medium text-gray-900 text-sm mb-2">
                        {{ patient.therapist.name || 'غير محدد' }}
                      </p>
                      <p class="text-gray-500 text-xs">
                        {{ patient.therapist.specialization || 'لم يتم تحديد التخصص' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Actions -->
              <div class="space-y-3">
                <button @click="showEditProfileModal = true" class="action-button">
                  <span class="flex items-center gap-2">
                    <i class="fas fa-edit text-brand-500"></i>
                    تعديل الملف الشخصي
                  </span>
                  <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
                </button>
                <button @click="openProgressModal" class="action-button">
                  <span class="flex items-center gap-2">
                    <i class="fas fa-chart-line text-brand-500"></i>
                    عرض التقدم الكامل
                  </span>
                  <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
                </button>
                <button @click="showSupportModal = true" class="action-button">
                  <span class="flex items-center gap-2">
                    <i class="fas fa-headset text-brand-500"></i>
                    التواصل مع الدعم
                  </span>
                  <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Sessions Section -->
          <div class="lg:col-span-3">
            <!-- Page Header -->
            <div class="mb-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-3">
                {{ currentLanguage === 'ar' ? 'الجلسات القادمة' : 'Upcoming sessions' }}
              </h2>
              <p class="text-gray-600">
                {{
                  currentLanguage === 'ar'
                    ? 'إدارة وتتبع جلساتك العلاجية'
                    : 'Manage and track your therapy sessions'
                }}
              </p>
            </div>

            <!-- Upcoming Sessions -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-calendar-alt ml-3 text-brand-500"></i>
                {{ currentLanguage === 'ar' ? 'الجلسات القادمة' : 'Upcoming sessions' }}
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="session in upcomingSessions" :key="session.id"
                  class="session-card border border-white/60 rounded-2xl p-6 hover:shadow-2xl transition-all"
                  :class="{ 'active': session.status === 'active' }">
                  <!-- Therapist/Patient Info -->
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4 space-x-reverse">
                      <div class="avatar-container">
                        <img v-if="isTherapist && session.patient?.avatar" :src="session.patient.avatar"
                          :alt="session.patient?.name"
                          class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
                        <img v-else-if="!isTherapist && session.therapist?.avatar" :src="session.therapist.avatar"
                          :alt="session.therapist?.name"
                          class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
                        <div v-else class="default-avatar" :class="isTherapist ? 'patient' : 'therapist'">
                          <i :class="isTherapist ? 'fas fa-user' : 'fas fa-user-md'"></i>
                        </div>
                      </div>
                      <div class="mr-3">
                        <h4 class="font-semibold text-gray-900 mb-1">
                          <span v-if="isTherapist">{{ session.patient?.name || 'غير محدد' }}</span>
                          <span v-else>{{ session.therapist?.name || 'غير محدد' }}</span>
                        </h4>
                        <span class="text-gray-500 text-sm">
                          <span v-if="isTherapist">{{ session.type || 'جلسة علاجية' }}</span>
                          <span v-else>{{ session.therapist?.specialization || session.type }}</span>
                        </span>
                      </div>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full font-medium" :class="{
                      'bg-brand-50 text-brand-700': session.status === 'active',
                      'bg-accent-50 text-accent-700': session.status === 'pending'
                    }">
                      {{ getStatusText(session.status) }}
                    </span>
                  </div>

                  <!-- Session Details -->
                  <div class="space-y-4 mb-6">
                    <div class="flex items-center text-gray-600 text-sm">
                      <i class="fas fa-clock ml-3 text-brand-500 w-4"></i>
                      <span>{{ formatDate(session.date) }} - {{ session.time }}</span>
                    </div>
                    <div class="flex items-center text-gray-600 text-sm">
                      <i class="fas fa-stopwatch ml-3 text-brand-500 w-4"></i>
                      <span>
                        {{
                          currentLanguage === 'ar'
                            ? `${session.duration} دقيقة`
                            : `${session.duration} minutes`
                        }}
                      </span>
                    </div>
                  </div>

                  <!-- Description -->
                  <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-white/60 rounded-xl p-3">{{
                    session.description }}</p>

                  <!-- Action Buttons for Therapist -->
                  <div v-if="isTherapist" class="space-y-3">
                    <!-- View Patient Report Button -->
                    <button @click="viewPatientReportBeforeSession(session)"
                      class="secondary-button w-full flex items-center justify-center space-x-2 space-x-reverse">
                      <i class="fas fa-file-medical"></i>
                      <span>
                        {{ currentLanguage === 'ar' ? 'عرض تقرير المريض' : 'View Patient Report' }}
                      </span>
                    </button>

                    <!-- Join Button -->
                    <button v-if="session.status === 'active'" @click="joinSession(session)"
                      class="primary-button w-full flex items-center justify-center space-x-2 space-x-reverse">
                      <i class="fas fa-video"></i>
                      <span>
                        {{ currentLanguage === 'ar' ? 'انضم إلى الجلسة' : 'Join session' }}
                      </span>
                    </button>

                    <!-- Countdown -->
                    <div v-else class="countdown-card text-center py-3 rounded-lg border border-accent-200">
                      <div class="text-accent-700 text-sm flex items-center justify-center space-x-2 space-x-reverse">
                        <i class="fas fa-hourglass-half"></i>
                        <span>
                          {{
                            currentLanguage === 'ar'
                              ? `تبدأ بعد: ${getTimeRemaining(session)}`
                              : `Starts in: ${getTimeRemaining(session)}`
                          }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Action Buttons for Patient -->
                  <div v-else>
                    <!-- Join Button -->
                    <button v-if="session.status === 'active'" @click="joinSession(session)"
                      class="primary-button w-full flex items-center justify-center space-x-2 space-x-reverse">
                      <i class="fas fa-video"></i>
                      <span>
                        {{ currentLanguage === 'ar' ? 'انضم إلى الجلسة' : 'Join session' }}
                      </span>
                    </button>

                    <!-- Countdown -->
                    <div v-else class="countdown-card text-center py-3 rounded-lg border border-accent-200">
                      <div class="text-accent-700 text-sm flex items-center justify-center space-x-2 space-x-reverse">
                        <i class="fas fa-hourglass-half"></i>
                        <span>
                          {{
                            currentLanguage === 'ar'
                              ? `تبدأ بعد: ${getTimeRemaining(session)}`
                              : `Starts in: ${getTimeRemaining(session)}`
                          }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Previous Sessions -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-history ml-3 text-gray-600"></i>
                {{ currentLanguage === 'ar' ? 'الجلسات السابقة' : 'Previous sessions' }}
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="session in previousSessions" :key="session.id"
                  class="session-card faded border border-white/50 rounded-2xl p-6">
                  <!-- Therapist Info -->
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4 space-x-reverse">
                      <div class="avatar-container">
                        <img v-if="isTherapist && session.patient?.avatar" :src="session.patient.avatar"
                          :alt="session.patient.name"
                          class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
                        <img v-else-if="!isTherapist && session.therapist?.avatar" :src="session.therapist.avatar"
                          :alt="session.therapist.name"
                          class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
                        <div v-else class="default-avatar" :class="isTherapist ? 'patient' : 'therapist'">
                          <i :class="isTherapist ? 'fas fa-user' : 'fas fa-user-md'"></i>
                        </div>
                      </div>
                      <div class="mr-3">
                        <h4 class="font-semibold text-gray-900 mb-1">
                          <span v-if="isTherapist">{{ session.patient?.name || 'غير محدد' }}</span>
                          <span v-else>{{ session.therapist?.name || 'غير محدد' }}</span>
                        </h4>
                        <span class="text-gray-500 text-sm">
                          <span v-if="isTherapist">{{ session.type || 'جلسة علاجية' }}</span>
                          <span v-else>{{ session.therapist?.specialization || session.type }}</span>
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Session Details -->
                  <div class="space-y-4 mb-6">
                    <div class="flex items-center text-gray-600 text-sm">
                      <i class="fas fa-calendar ml-3 text-brand-500 w-4"></i>
                      <span>{{ formatDate(session.date) }}</span>
                    </div>
                  </div>

                  <!-- Rating / Review -->
                  <div v-if="!isTherapist" class="mb-4">
                    <div v-if="session.review" class="bg-white/70 rounded-xl p-4 border border-white/40">
                      <div class="flex items-center space-x-1 space-x-reverse mb-2">
                        <i v-for="star in 5" :key="star" class="fas fa-star text-sm"
                          :class="star <= session.review.rating ? 'text-yellow-400' : 'text-gray-300'"></i>
                        <span class="text-xs text-gray-500 mr-2">
                          {{
                            currentLanguage === 'ar'
                              ? 'تم تقييم هذه الجلسة'
                              : 'This session has been rated'
                          }}
                        </span>
                      </div>
                      <p v-if="session.review.comment" class="text-sm text-gray-600 leading-relaxed">
                        "{{ session.review.comment }}"
                      </p>
                      <p class="text-xs text-gray-400 mt-2">
                        {{ formatDate(session.review.createdAt) }}
                      </p>
                    </div>
                    <button v-else-if="session.canRate" @click="openRatingModal(session)"
                      class="primary-button w-full flex items-center justify-center space-x-2 space-x-reverse mb-4">
                      <i class="fas fa-star"></i>
                      <span>
                        {{ currentLanguage === 'ar' ? 'قيّم المعالج' : 'Rate therapist' }}
                      </span>
                    </button>
                    <p v-else class="text-xs text-gray-400 text-center">
                      {{
                        currentLanguage === 'ar'
                          ? 'بانتظار إنهاء الجلسة للتقييم'
                          : 'Waiting for the session to finish before rating'
                      }}
                    </p>
                  </div>

                  <div v-if="isTherapist" class="mb-4">
                    <div v-if="session.review" class="bg-white/70 rounded-xl p-4 border border-white/40">
                      <div class="flex items-center space-x-1 space-x-reverse mb-2">
                        <i v-for="star in 5" :key="star" class="fas fa-star text-sm"
                          :class="star <= session.review.rating ? 'text-yellow-400' : 'text-gray-300'"></i>
                        <span class="text-xs text-gray-500 mr-2">
                          {{ currentLanguage === 'ar' ? 'تقييم المريض' : 'Patient rating' }}
                        </span>
                      </div>
                      <p v-if="session.review.comment" class="text-sm text-gray-600 leading-relaxed">
                        "{{ session.review.comment }}"
                      </p>
                      <p class="text-xs text-gray-400 mt-2">
                        {{ formatDate(session.review.createdAt) }}
                      </p>
                    </div>
                    <div v-else
                      class="text-xs text-gray-400 text-center border border-dashed border-gray-200 rounded-xl py-3">
                      {{
                        currentLanguage === 'ar'
                          ? 'لم يتم تقييم هذه الجلسة بعد'
                          : 'This session has not been rated yet'
                      }}
                    </div>
                  </div>

                  <!-- Actions Row -->
                  <div class="flex items-center justify-end gap-2">
                    <button @click="viewSessionNotes(session)"
                      class="p-2 text-gray-500 hover:text-brand-500 hover:bg-gray-100 rounded-lg transition-colors"
                      title="عرض الملاحظات">
                      <i class="fas fa-eye text-sm"></i>
                    </button>
                    <button @click="hideSession(session)"
                      class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-lg transition-colors"
                      title="إخفاء الجلسة">
                      <i class="fas fa-trash text-sm"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Patient Assessments Summary (Only for Patients) -->
            <div v-if="!isTherapist" class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-clipboard-check ml-3 text-brand-500"></i>
                {{ currentLanguage === 'ar' ? 'المقاييس النفسية' : 'Psychological Assessments' }}
              </h3>

              <div v-if="patientAssessments.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="assessment in patientAssessments" :key="assessment.id"
                  class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                  <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                      <h4 class="font-semibold text-gray-900 mb-1">
                        {{ assessment.psychological_scale?.name_ar || assessment.psychological_scale?.name_en || 'مقياس غير معروف' }}
                      </h4>
                      <p v-if="assessment.psychological_scale?.category" class="text-xs text-gray-500">
                        {{ assessment.psychological_scale.category.name_ar || assessment.psychological_scale.category.name_en }}
                      </p>
                    </div>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                      {{ formatDate(assessment.completed_at || assessment.created_at) }}
                    </span>
                  </div>

                  <div class="mb-3">
                    <div class="flex justify-between items-center mb-2">
                      <span class="text-sm text-gray-600">النتيجة</span>
                      <span class="font-bold text-brand-500 text-lg">
                        {{ assessment.total_score }} / {{ assessment.psychological_scale?.max_score || 100 }}
                      </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                      <div class="bg-gradient-to-r from-brand-500 to-brand-600 h-3 rounded-full transition-all"
                        :style="{ width: ((assessment.total_score / (assessment.psychological_scale?.max_score || 100)) * 100) + '%' }">
                      </div>
                    </div>
                  </div>

                  <div v-if="assessment.interpretation_level" class="flex items-center gap-2">
                    <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">
                      {{ assessment.interpretation_level }}
                    </span>
                  </div>
                </div>
              </div>

              <div v-else class="bg-gray-50 rounded-xl p-8 border border-gray-200 text-center">
                <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-600 font-medium mb-1">
                  {{ currentLanguage === 'ar' ? 'لا توجد مقاييس مسجلة' : 'No assessments recorded' }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ currentLanguage === 'ar' ? 'ابدأ بإجراء مقاييس نفسية لمعرفة حالتك' : 'Start taking psychological assessments to know your status' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Session Notes Modal -->
          <div v-if="showSessionNotesModal"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl w-full max-w-xl border border-gray-100 shadow-2xl">
              <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                  <h3 class="font-semibold text-gray-900 text-lg">
                    {{ currentLanguage === 'ar' ? 'ملاحظات الجلسة' : 'Session notes' }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    <span v-if="isTherapist">
                      {{ selectedSession?.patient?.name || (currentLanguage === 'ar' ? 'المريض' : 'Patient') }}
                    </span>
                    <span v-else>
                      {{ selectedSession?.therapist?.name || (currentLanguage === 'ar' ? 'المعالج' : 'Therapist') }}
                    </span>
                  </p>
                </div>
                <button @click="showSessionNotesModal = false" class="text-gray-400 hover:text-gray-600 transition">
                  <i class="fas fa-times text-lg"></i>
                </button>
              </div>

              <div class="p-5 space-y-4 text-gray-700">
                <div class="flex items-center gap-3 text-sm text-gray-500">
                  <i class="fas fa-calendar text-brand-500"></i>
                  <span>{{ formatDate(selectedSession?.date) }}</span>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                  <p class="text-sm text-gray-500 mb-2">
                    {{ currentLanguage === 'ar' ? 'وصف الجلسة' : 'Session description' }}
                  </p>
                  <p class="text-gray-700 leading-relaxed">
                    {{
                      selectedSession?.notes ||
                      (currentLanguage === 'ar'
                        ? 'لا توجد ملاحظات متاحة لهذه الجلسة.'
                        : 'No notes are available for this session.')
                    }}
                  </p>
                </div>
              </div>

              <div class="p-5 border-t border-gray-100 text-right">
                <button @click="showSessionNotesModal = false"
                  class="px-5 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                  {{ currentLanguage === 'ar' ? 'إغلاق' : 'Close' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Rating Modal -->
          <div v-if="showRatingModal"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl w-full max-w-md border border-gray-100 shadow-2xl">
              <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                  <h3 class="font-semibold text-gray-900 text-lg">
                    {{ currentLanguage === 'ar' ? 'تقييم المعالج' : 'Rate therapist' }}
                  </h3>
                  <p class="text-sm text-gray-500">{{ ratingForm.therapistName }}</p>
                </div>
                <button @click="closeRatingModal" class="text-gray-400 hover:text-gray-600 transition">
                  <i class="fas fa-times text-lg"></i>
                </button>
              </div>

              <div class="p-5 space-y-4">
                <div class="flex items-center justify-center gap-2">
                  <button v-for="star in 5" :key="star" type="button" @click="setRatingValue(star)"
                    class="text-2xl focus:outline-none">
                    <i :class="[
                      star <= ratingForm.rating ? 'fas' : 'far',
                      'fa-star',
                      star <= ratingForm.rating ? 'text-yellow-400' : 'text-gray-300'
                    ]"></i>
                  </button>
                </div>
                <textarea v-model="ratingForm.comment" rows="4"
                  class="w-full border border-gray-200 rounded-xl p-3 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 text-sm"
                  :placeholder="currentLanguage === 'ar' ? 'اكتب ملاحظاتك عن الجلسة (اختياري)' : 'Write your notes about the session (optional)'"></textarea>
              </div>

              <div class="p-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button"
                  class="px-4 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition"
                  @click="closeRatingModal">
                  {{ currentLanguage === 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button type="button"
                  class="px-5 py-2 rounded-xl bg-brand-500 text-white hover:bg-brand-600 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="ratingSubmitting" @click="submitRating">
                  <i v-if="ratingSubmitting" class="fas fa-spinner fa-spin"></i>
                  <i v-else class="fas fa-check"></i>
                  <span>
                    {{
                      ratingSubmitting
                        ? currentLanguage === 'ar'
                          ? 'جاري الحفظ...'
                          : 'Saving...'
                        : currentLanguage === 'ar'
                          ? 'حفظ التقييم'
                          : 'Save rating'
                    }}
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <Footer />

    <!-- Edit Profile Modal -->
    <div v-if="showEditProfileModal"
      class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
      <div class="bg-white rounded-2xl w-full max-w-3xl my-8 border border-gray-200 shadow-xl">
        <div class="p-5 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
          <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
            <i class="fas fa-user-edit text-brand-500"></i>
            تعديل الملف الشخصي
          </h3>
          <button @click="showEditProfileModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <div class="p-5 max-h-[80vh] overflow-y-auto">
          <div class="space-y-6">
            <!-- Basic Information Section -->
            <div>
              <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-brand-500"></i>
                المعلومات الأساسية
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل *</label>
                  <input v-model="editProfileData.name" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                  <input v-model="editProfileData.email" type="email"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                  <input v-model="editProfileData.phone" type="tel"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                  <select v-model="editProfileData.gender"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                    <option value="">اختر النوع</option>
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الميلاد</label>
                  <input v-model="editProfileData.date_of_birth" type="date"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>
              </div>
            </div>

            <!-- Address Information Section -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-brand-500"></i>
                معلومات العنوان
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">البلد</label>
                  <input v-model="editProfileData.country" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                  <input v-model="editProfileData.city" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">المديرية</label>
                  <input v-model="editProfileData.governorate" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">المنطقة/الحارة/القرية</label>
                  <input v-model="editProfileData.district" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                </div>
              </div>
            </div>

            <!-- Personal Information Section -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-brand-500"></i>
                المعلومات الشخصية
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">الحالة الاجتماعية</label>
                  <select v-model="editProfileData.marital_status"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                    <option value="">اختر الحالة</option>
                    <option value="single">عازب</option>
                    <option value="married">متزوج</option>
                    <option value="divorced">مطلق</option>
                    <option value="widowed">أرمل</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">المستوى التعليمي</label>
                  <select v-model="editProfileData.education_level"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                    <option value="">اختر المستوى</option>
                    <option value="elementary">ابتدائية</option>
                    <option value="middle">إعدادية</option>
                    <option value="high_school">ثانوية</option>
                    <option value="diploma">دبلوم</option>
                    <option value="bachelor">بكالوريوس</option>
                    <option value="graduate">دراسات عليا</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">الوضع الوظيفي</label>
                  <select v-model="editProfileData.employment_status"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                    <option value="">اختر الوضع</option>
                    <option value="student">طالب</option>
                    <option value="government_employee">موظف حكومي</option>
                    <option value="private_employee">موظف خاص</option>
                    <option value="unemployed">عاطل عن العمل</option>
                    <option value="housewife">ربة بيت</option>
                    <option value="retired">متقاعد</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">المهنة/مجال العمل</label>
                  <input v-model="editProfileData.profession" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm"
                    placeholder="اختياري">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">الدخل الشهري</label>
                  <select v-model="editProfileData.monthly_income"
                    class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm">
                    <option value="">اختر الدخل</option>
                    <option value="less_than_60k">أقل من 60 ألف</option>
                    <option value="61k_to_120k">من 61 ألف وحتى 120 ألف</option>
                    <option value="121k_to_200k">من 121 ألف وحتى 200 ألف</option>
                    <option value="201k_to_350k">من 201 ألف وحتى 350 ألف</option>
                    <option value="more_than_351k">ما فوق 351 ألف</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="flex space-x-3 space-x-reverse pt-4 border-t border-gray-200">
              <button @click="saveProfile"
                class="flex-1 bg-brand-500 text-white py-3 rounded-lg hover:bg-brand-600 font-medium transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-save"></i>
                حفظ التغييرات
              </button>
              <button @click="showEditProfileModal = false"
                class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 font-medium transition-colors">
                إلغاء
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Progress Modal -->
    <div v-if="showProgressModal"
      class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl w-full max-w-4xl border border-gray-200 shadow-xl">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
            <i class="fas fa-chart-line text-brand-500"></i>
            سجل التقدم
          </h3>
          <button @click="showProgressModal = false" 
            class="w-8 h-8 rounded-full hover:bg-gray-100 transition flex items-center justify-center text-gray-500">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="p-5">
          <div class="grid md:grid-cols-2 gap-5">
            <!-- Progress History -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
              <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-history text-brand-500"></i>
                التقييمات السابقة
              </h4>
              <div v-if="progressEntries.length" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                <div v-for="entry in progressEntries" :key="entry.id" 
                  class="bg-white rounded-lg p-3 border border-gray-200">
                  <div class="flex justify-between items-start mb-2">
                    <div>
                      <p class="font-semibold text-sm text-gray-900">{{ entry.counterpart }}</p>
                      <p class="text-xs text-gray-500">{{ formatDate(entry.date) }}</p>
                    </div>
                    <span class="bg-brand-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                      {{ entry.score }}%
                    </span>
                  </div>
                  <p class="text-sm text-gray-700">{{ entry.notes }}</p>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500 text-sm">
                <i class="fas fa-clipboard-list text-3xl mb-2 block"></i>
                لا توجد تقييمات
              </div>
            </div>

            <!-- Add Progress (Therapist) -->
            <div v-if="isTherapist" class="bg-white rounded-xl p-4 border border-gray-200">
              <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-plus-circle text-brand-500"></i>
                إضافة تقييم جديد
              </h4>

              <div v-if="therapistPatients.length > 0" class="space-y-4">
                <!-- Select Patient -->
                <div v-if="!selectedPatientForProgress">
                  <label class="block text-sm font-medium text-gray-700 mb-2">اختر المريض</label>
                  <div class="space-y-2 max-h-48 overflow-y-auto">
                    <button
                      v-for="patient in therapistPatients"
                      :key="patient.id"
                      @click="selectPatientForProgress(patient)"
                      class="w-full text-right p-3 border rounded-lg hover:bg-gray-50 hover:border-brand-400 transition flex items-center justify-between"
                      :class="patient.id === lastPatientId ? 'bg-brand-50 border-brand-400' : 'border-gray-200'">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-500 flex items-center justify-center text-white font-semibold">
                          {{ patient.name.charAt(0) }}
                        </div>
                        <div class="text-right">
                          <div class="font-semibold text-sm text-gray-900">{{ patient.name }}</div>
                          <div class="text-xs text-gray-500">{{ patient.total_sessions }} جلسة</div>
                        </div>
                      </div>
                      <div class="flex items-center gap-2">
                        <span v-if="patient.id === lastPatientId" 
                          class="text-xs bg-blue-500 text-white px-2 py-1 rounded-full">
                          آخر عميل
                        </span>
                        <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Form -->
                <div v-else class="space-y-4">
                  <!-- Patient Info -->
                  <div class="bg-brand-50 p-3 rounded-lg border border-brand-200">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-brand-500 flex items-center justify-center text-white font-semibold">
                          {{ selectedPatientForProgress.name.charAt(0) }}
                        </div>
                        <div>
                          <div class="font-semibold text-sm text-gray-900">{{ selectedPatientForProgress.name }}</div>
                          <div class="text-xs text-gray-600">{{ selectedPatientForProgress.email }}</div>
                        </div>
                      </div>
                      <button @click="showPatientReport = true" 
                        class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-file-medical mr-1"></i>
                        التقرير
                      </button>
                    </div>
                    <button @click="selectedPatientForProgress = null" 
                      class="text-xs text-gray-600 hover:text-gray-800 mt-2">
                      <i class="fas fa-times mr-1"></i>
                      تغيير المريض
                    </button>
                  </div>

                  <!-- Session -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الجلسة</label>
                    <select v-model="progressForm.sessionId" 
                      class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 text-sm">
                      <option value="">اختر الجلسة</option>
                      <option v-for="session in progressSessionsOptions" :key="session.id" :value="session.id">
                        {{ session.label }}
                      </option>
                    </select>
                  </div>

                  <!-- Progress Score -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      نسبة التقدم: <span class="text-brand-600 font-bold">{{ progressForm.progressScore }}%</span>
                    </label>
                    <input type="range" min="0" max="100" v-model.number="progressForm.progressScore"
                      class="w-full accent-brand-500">
                  </div>

                  <!-- Notes -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                    <textarea v-model="progressForm.notes" rows="3" 
                      class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 text-sm resize-none"
                      placeholder="اكتب ملاحظاتك..."></textarea>
                  </div>

                  <!-- Submit -->
                  <button @click="submitProgressUpdate" 
                    :disabled="progressSubmitting || !progressForm.sessionId"
                    class="w-full py-2.5 rounded-lg text-white font-semibold bg-brand-500 hover:bg-brand-600 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                    <i v-if="!progressSubmitting" class="fas fa-save"></i>
                    <i v-else class="fas fa-spinner fa-spin"></i>
                    <span>{{ progressSubmitting ? 'جاري الحفظ...' : 'حفظ التقييم' }}</span>
                  </button>
                </div>
              </div>

              <div v-else class="text-center py-8 text-gray-500 text-sm">
                <i class="fas fa-user-friends text-3xl mb-2 block"></i>
                لا توجد مرضى
              </div>
            </div>

            <!-- Info (Patient) -->
            <div v-else class="bg-blue-50 rounded-xl p-4 border border-blue-200">
              <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                معلومات
              </h4>
              <p class="text-sm text-gray-700 leading-relaxed">
                يتم تسجيل التقييمات من قبل المعالج بعد كل جلسة لتتبع التحسن ومراقبة التطور.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Patient Report Modal -->
    <div v-if="showPatientReport" 
      class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
      <div class="bg-white rounded-2xl w-full max-w-4xl my-8 border border-gray-200 shadow-xl">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
          <h3 class="font-semibold text-gray-900 text-lg sm:text-xl flex items-center gap-2">
            <i class="fas fa-user-md text-brand-500"></i>
            تقرير المريض الكامل
          </h3>
          <button @click="showPatientReport = false" class="text-gray-400 hover:text-gray-600 transition-colors p-2">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        
        <div v-if="loadingPatientReport" class="p-8 text-center">
          <i class="fas fa-spinner fa-spin text-3xl text-brand-500 mb-4"></i>
          <p class="text-gray-600">جاري تحميل التقرير...</p>
        </div>
        
        <div v-else-if="patientReport" class="p-4 sm:p-6 max-h-[80vh] overflow-y-auto">
          <!-- Patient Basic Info -->
          <div class="mb-6">
            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <i class="fas fa-user text-brand-500"></i>
              المعلومات الأساسية
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm text-gray-600">الاسم الكامل</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.name }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">البريد الإلكتروني</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.email || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">رقم الهاتف</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.phone || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">النوع</label>
                <p class="font-semibold text-gray-900">
                  {{ patientReport.patient.gender === 'male' ? 'ذكر' : patientReport.patient.gender === 'female' ? 'أنثى' : '-' }}
                </p>
              </div>
              <div>
                <label class="text-sm text-gray-600">تاريخ الميلاد</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.date_of_birth || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">تاريخ الانضمام</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.joined_at || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Patient Detailed Info -->
          <div class="mb-6 border-t border-gray-200 pt-6">
            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <i class="fas fa-info-circle text-brand-500"></i>
              المعلومات التفصيلية
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm text-gray-600">البلد</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.country || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">المدينة</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.city || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">المديرية</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.governorate || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">المنطقة/الحارة/القرية</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.district || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">الحالة الاجتماعية</label>
                <p class="font-semibold text-gray-900">
                  {{ getMaritalStatusText(patientReport.patient.marital_status) }}
                </p>
              </div>
              <div>
                <label class="text-sm text-gray-600">المستوى التعليمي</label>
                <p class="font-semibold text-gray-900">
                  {{ getEducationLevelText(patientReport.patient.education_level) }}
                </p>
              </div>
              <div>
                <label class="text-sm text-gray-600">الوضع الوظيفي</label>
                <p class="font-semibold text-gray-900">
                  {{ getEmploymentStatusText(patientReport.patient.employment_status) }}
                </p>
              </div>
              <div>
                <label class="text-sm text-gray-600">المهنة/مجال العمل</label>
                <p class="font-semibold text-gray-900">{{ patientReport.patient.profession || '-' }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600">الدخل الشهري</label>
                <p class="font-semibold text-gray-900">
                  {{ getMonthlyIncomeText(patientReport.patient.monthly_income) }}
                </p>
              </div>
              <div>
                <label class="text-sm text-gray-600">الغرض من استخدام المنصة</label>
                <p class="font-semibold text-gray-900">
                  {{ getPlatformPurposesText(patientReport.patient.platform_purposes) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Patient Assessments -->
          <div class="mb-6 border-t border-gray-200 pt-6">
            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <i class="fas fa-clipboard-check text-brand-500"></i>
              المقاييس النفسية التي اختبرها المريض
            </h4>
            <div v-if="patientReport.assessments && patientReport.assessments.length > 0" class="space-y-4">
              <div v-for="assessment in patientReport.assessments" :key="assessment.id"
                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h5 class="font-semibold text-gray-900">{{ assessment.scale_name_ar || assessment.scale_name_en }}</h5>
                    <p v-if="assessment.category" class="text-xs text-gray-500 mt-1">
                      {{ assessment.category.name_ar || assessment.category.name_en }}
                    </p>
                  </div>
                  <span class="text-xs text-gray-500">{{ formatDate(assessment.completed_at) }}</span>
                </div>
                <div class="flex items-center gap-4 mt-3">
                  <div class="flex-1">
                    <div class="flex justify-between items-center mb-1">
                      <span class="text-sm text-gray-600">النتيجة</span>
                      <span class="font-bold text-brand-500">{{ assessment.total_score }} / {{ assessment.max_score }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-brand-500 h-2 rounded-full transition-all"
                        :style="{ width: (assessment.total_score / assessment.max_score * 100) + '%' }">
                      </div>
                    </div>
                  </div>
                </div>
                <div v-if="assessment.interpretation_level" class="mt-3">
                  <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                    {{ assessment.interpretation_level }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="text-center text-gray-500 py-8">
              <i class="fas fa-clipboard-list text-4xl mb-2"></i>
              <p>لا توجد مقاييس مسجلة للمريض</p>
            </div>
          </div>

          <!-- Recent Sessions -->
          <div class="border-t border-gray-200 pt-6">
            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <i class="fas fa-history text-brand-500"></i>
              آخر الجلسات
            </h4>
            <div v-if="patientReport.recent_sessions && patientReport.recent_sessions.length > 0" class="space-y-2">
              <div v-for="session in patientReport.recent_sessions" :key="session.id"
                class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <div>
                  <p class="font-semibold text-gray-900">{{ formatDate(session.date) }}</p>
                  <p class="text-xs text-gray-500">{{ getStatusText(session.status) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-brand-500">{{ session.progress_score }}%</p>
                  <p class="text-xs text-gray-500">نسبة التقدم</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center text-gray-500 py-4">
              <p class="text-sm">لا توجد جلسات مسجلة</p>
            </div>
          </div>
        </div>

        <div v-if="patientReport" class="p-4 sm:p-6 border-t border-gray-200 flex justify-end">
          <button @click="showPatientReport = false"
            class="px-6 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
            إغلاق
          </button>
        </div>
        <div v-else-if="!loadingPatientReport" class="p-4 sm:p-6 border-t border-gray-200 flex justify-end">
          <button @click="showPatientReport = false"
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            إغلاق
          </button>
        </div>
      </div>
    </div>

    <!-- Support Modal -->
    <div v-if="showSupportModal"
      class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="support-modal-simple">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-brand-500 mb-1">فريق الدعم</p>
            <h3 class="text-xl font-semibold text-gray-900">اختر وسيلة الاتصال المناسبة</h3>
          </div>
          <button type="button" @click="closeSupportModal" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <p class="text-sm text-gray-500 mb-5">نستجيب عادة خلال 10 دقائق في أوقات العمل.</p>

        <div class="space-y-3">
          <button type="button" @click="contactSupport('whatsapp')" class="support-quick-option whatsapp">
            <div class="flex items-center gap-3">
              <span class="icon"><i class="fab fa-whatsapp"></i></span>
              <div>
                <p class="font-semibold">محادثة واتساب</p>
                <p class="text-xs text-white/80">إجابة فورية</p>
              </div>
            </div>
            <i class="fas fa-arrow-left text-xs"></i>
          </button>

          <button type="button" @click="contactSupport('phone')" class="support-quick-option phone">
            <div class="flex items-center gap-3">
              <span class="icon"><i class="fas fa-phone-alt"></i></span>
              <div>
                <p class="font-semibold">اتصال هاتفي</p>
                <p class="text-xs text-gray-800">خلال ساعات العمل</p>
              </div>
            </div>
            <span class="text-xs text-gray-600"> {{ supportContact.phone }} </span>
          </button>

          <button type="button" @click="contactSupport('email')" class="support-quick-option email">
            <div class="flex items-center gap-3">
              <span class="icon"><i class="fas fa-envelope"></i></span>
              <div>
                <p class="font-semibold">بريد إلكتروني</p>
                <p class="text-xs text-gray-800">سنرد خلال نفس اليوم</p>
              </div>
            </div>
            <span class="text-xs text-gray-600">{{ supportContact.email }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import { useProfile } from '@/composables/useProfile'
import { useTranslations } from '@/composables/useTranslations'
import api from '@/utils/api'
import Header from '@/components/frontend/layouts/header.vue'
import Footer from '@/components/frontend/layouts/footer.vue'

export default {
  name: 'PatientSessions',
  components: {
    Header,
    Footer
  },
  setup() {
    const authStore = useAuthStore()
    const { user: profileUser } = useProfile()
    // نستخدم حالة الترجمة العامة للغة
    const { currentLanguage } = useTranslations()
    return { authStore, profileUser, currentLanguage }
  },
  data() {
    return {
      loading: false,
      isTherapist: false,
      currentUser: null,
      patientName: '',
      patientAvatar: null,
      isAuthenticated: false,
      showEditProfileModal: false,
      showProgressModal: false,
      showSupportModal: false,
      showSessionNotesModal: false,
      editProfileData: {
        name: '',
        age: null,
        email: '',
        phone: '',
        gender: '',
        date_of_birth: '',
        country: '',
        city: '',
        governorate: '',
        district: '',
        marital_status: '',
        education_level: '',
        employment_status: '',
        profession: '',
        monthly_income: ''
      },
      patientAssessments: [],
      loadingAssessments: false,
      patient: {
        name: '',
        age: null,
        avatar: null,
        totalSessions: 0,
        attendanceRate: 0,
        progress: 0,
        therapist: {
          id: null,
          name: '',
          avatar: null,
          specialization: ''
        }
      },
      // بيانات المعالج
      therapist: {
        name: '',
        avatar: null,
        specialization: '',
        totalPatients: 0,
        totalSessions: 0,
        attendanceRate: 0
      },
      upcomingSessions: [],
      previousSessions: [],
      appointments: [],
      sessions: [],
      selectedSession: null,
      supportContact: {
        phone: '+966920012345',
        email: 'support@therapy.com',
        whatsapp: '966920012345'
      },
      progressForm: {
        sessionId: null,
        progressScore: 70,
        notes: ''
      },
      progressSubmitting: false,
      therapistPatients: [],
      lastPatientId: null,
      selectedPatientForProgress: null,
      showPatientReport: false,
      patientReport: null,
      loadingPatientReport: false,
      showRatingModal: false,
      ratingSubmitting: false,
      ratingTargetSession: null,
      ratingForm: {
        sessionId: null,
        therapistName: '',
        rating: 5,
        comment: ''
      },
      storageCheckInterval: null,
      lastFrontendToken: null,
      lastAdminToken: null,
      hiddenSessions: [] // قائمة الجلسات المخفية من الواجهة
    }
  },
  computed: {
    isLoggedIn() {
      const frontendToken = localStorage.getItem('frontend_token')
      const adminToken = localStorage.getItem('admin_token')
      return !!(frontendToken || adminToken) && this.isAuthenticated
    },
    progressEntries() {
      return (this.previousSessions || [])
        .filter(session => {
          const score = session.progressScore ?? session.progress_score ?? 0
          const notes = session.therapistNotes || session.therapist_notes || session.notes
          return score > 0 || (notes && notes.trim().length > 0)
        })
        .map(session => {
          const score = session.progressScore ?? session.progress_score ?? 0
          const notes = session.therapistNotes || session.therapist_notes || session.notes || ''
          const counterpart = this.isTherapist
            ? (session.patient?.name || 'المريض')
            : (session.therapist?.name || 'المعالج')
          return {
            id: session.id,
            date: session.date,
            score,
            notes,
            counterpart
          }
        })
        .sort((a, b) => new Date(b.date) - new Date(a.date))
    },
    progressSessionsOptions() {
      if (!this.isTherapist || !this.selectedPatientForProgress) {
        return []
      }
      return (this.previousSessions || [])
        .filter(session => session.patient?.name === this.selectedPatientForProgress.name)
        .map(session => ({
          id: session.id,
          label: `${this.formatDate(session.date)} • ${session.type || 'جلسة علاجية'}`
        }))
    }
  },
  async mounted() {
    // تحميل الجلسات المخفية من localStorage
    const savedHiddenSessions = localStorage.getItem('hiddenSessions')
    if (savedHiddenSessions) {
      try {
        this.hiddenSessions = JSON.parse(savedHiddenSessions)
      } catch (e) {
        console.warn('Failed to parse hiddenSessions from localStorage:', e)
        this.hiddenSessions = []
      }
    }

    // التحقق الفوري من حالة تسجيل الدخول قبل تحميل البيانات
    const frontendToken = localStorage.getItem('frontend_token')
    const adminToken = localStorage.getItem('admin_token')

    if (!frontendToken && !adminToken) {
      // لا يوجد token - مسح جميع البيانات فوراً
      this.clearUserData()
      this.loading = false
    } else {
      // يوجد token - تحميل البيانات
      await this.loadData()
    }

    // حفظ القيم الحالية للـ tokens للمقارنة
    this.lastFrontendToken = frontendToken
    this.lastAdminToken = adminToken

    // مراقبة تغييرات localStorage لتحديث البيانات عند تسجيل الخروج
    window.addEventListener('storage', this.handleStorageChange)

    // مراقبة تغييرات localStorage في نفس النافذة (لأن storage event لا يعمل في نفس النافذة)
    this.storageCheckInterval = setInterval(() => {
      this.checkAuthStatus()
    }, 300) // التحقق كل 300ms للاستجابة السريعة
  },

  beforeUnmount() {
    // تنظيف event listeners
    window.removeEventListener('storage', this.handleStorageChange)
    if (this.storageCheckInterval) {
      clearInterval(this.storageCheckInterval)
    }
  },
  methods: {
    handleStorageChange(event) {
      // عند تغيير localStorage (مثل تسجيل الخروج من نافذة أخرى)
      if (event.key === 'frontend_token' || event.key === 'admin_token') {
        console.log('Token changed in localStorage:', event.key, 'old:', event.oldValue, 'new:', event.newValue)
        if (!event.newValue) {
          // تم حذف token - مسح البيانات
          this.clearUserData()
        } else {
          // تم إضافة token - تحميل البيانات
          this.loadData()
        }
      }
    },

    checkAuthStatus() {
      const frontendToken = localStorage.getItem('frontend_token')
      const adminToken = localStorage.getItem('admin_token')
      const hasToken = !!(frontendToken || adminToken)

      // التحقق من التغييرات في tokens
      const frontendTokenChanged = this.lastFrontendToken !== frontendToken
      const adminTokenChanged = this.lastAdminToken !== adminToken

      if (frontendTokenChanged || adminTokenChanged) {
        console.log('Token changed detected:', {
          frontend: { old: this.lastFrontendToken, new: frontendToken },
          admin: { old: this.lastAdminToken, new: adminToken }
        })

        // تحديث القيم المحفوظة
        this.lastFrontendToken = frontendToken
        this.lastAdminToken = adminToken

        // إذا تم حذف جميع tokens، مسح البيانات
        if (!hasToken) {
          console.log('All tokens removed, clearing user data...')
          this.clearUserData()
        }
        // إذا تم إضافة token جديد، تحميل البيانات
        else if (!this.isAuthenticated) {
          console.log('Token added, loading data...')
          this.loadData()
        }
        // إذا كان المستخدم مسجل دخول لكن token تغير، إعادة تحميل البيانات
        else {
          console.log('Token changed for authenticated user, reloading data...')
          this.loadData()
        }
      }
      // إذا كان المستخدم يعتقد أنه مسجل دخول لكن لا يوجد token، مسح البيانات
      else if (this.isAuthenticated && !hasToken) {
        console.log('User authenticated but no token found, clearing user data...')
        this.clearUserData()
      }
      // إذا كان المستخدم يعتقد أنه غير مسجل دخول لكن يوجد token، إعادة تحميل البيانات
      else if (!this.isAuthenticated && hasToken) {
        console.log('User not authenticated but token found, loading data...')
        this.loadData()
      }
    },

    clearUserData() {
      // مسح جميع بيانات المستخدم
      this.isAuthenticated = false
      this.isTherapist = false
      this.currentUser = null
      this.patientName = 'ضيف'
      this.patientAvatar = null
      this.lastFrontendToken = null
      this.lastAdminToken = null

      // مسح بيانات المستخدم من localStorage أيضاً
      localStorage.removeItem('currentUser')
      this.patient = {
        name: '',
        age: null,
        avatar: null,
        totalSessions: 0,
        attendanceRate: 0,
        progress: 0,
        therapist: {
          id: null,
          name: '',
          avatar: null,
          specialization: ''
        }
      }
      this.therapist = {
        name: '',
        avatar: null,
        specialization: '',
        totalPatients: 0,
        totalSessions: 0,
        attendanceRate: 0
      }
      this.upcomingSessions = []
      this.previousSessions = []
      this.appointments = []
      this.sessions = []
      this.selectedSession = null
      this.editProfileData = {
        name: '',
        age: null,
        email: '',
        phone: '',
        gender: '',
        date_of_birth: '',
        country: '',
        city: '',
        governorate: '',
        district: '',
        marital_status: '',
        education_level: '',
        employment_status: '',
        profession: '',
        monthly_income: ''
      }
      this.patientAssessments = []
    },

    async loadData() {
      this.loading = true
      try {
        // التحقق من تسجيل الدخول أولاً
        const frontendToken = localStorage.getItem('frontend_token')
        const adminToken = localStorage.getItem('admin_token')

        if (!frontendToken && !adminToken) {
          // المستخدم غير مسجل دخول - عرض واجهة ضيف ومسح جميع البيانات
          this.clearUserData()
          this.loading = false
          return
        }

        // المستخدم مسجل دخول - جلب البيانات
        this.isAuthenticated = true

        // جلب بيانات المستخدم أولاً للتحقق من نوعه
        await this.fetchUserData()

        // جلب المواعيد (فقط للعميل)
        if (!this.isTherapist && this.isAuthenticated) {
          await this.fetchAppointments()
        }

        // جلب الجلسات (يختلف حسب نوع المستخدم)
        if (this.isAuthenticated) {
          await this.fetchSessions()

          // تجميع البيانات حسب نوع المستخدم
          if (this.isTherapist) {
            this.processTherapistSessionsData()
          } else {
            this.processSessionsData()
            // جلب المقاييس للمريض
            await this.fetchPatientAssessments()
          }
        }
      } catch (error) {
        console.error('Error loading data:', error)
        console.error('Error stack:', error.stack)

        // إذا كان الخطأ 401 (غير مصرح)، اعتباره غير مسجل دخول ومسح جميع البيانات والـ tokens
        if (error.response?.status === 401) {
          console.log('401 Unauthorized in loadData - clearing user data and tokens')
          // مسح tokens من localStorage
          localStorage.removeItem('frontend_token')
          localStorage.removeItem('admin_token')
          this.clearUserData()
        } else {
          // في حالة الخطأ الآخر، نعرض قوائم فارغة بدلاً من إيقاف التحميل
          this.sessions = []
          this.upcomingSessions = []
          this.previousSessions = []
        }
      } finally {
        this.loading = false
      }
    },

    async fetchUserData() {
      try {
        // التحقق من تسجيل الدخول قبل محاولة جلب البيانات
        const frontendToken = localStorage.getItem('frontend_token')
        const adminToken = localStorage.getItem('admin_token')

        if (!frontendToken && !adminToken) {
          console.log('No token found, user is not authenticated')
          this.isAuthenticated = false
          return
        }

        const response = await api.get('/user')

        const user = response.data?.data?.user || response.data?.user
        console.log('User data fetched:', user)

        if (user) {
          this.currentUser = user
          this.isAuthenticated = true
          // التحقق من نوع المستخدم
          this.isTherapist = user.role === 'Therapist'
          console.log('User role:', user.role, 'isTherapist:', this.isTherapist)

          if (this.isTherapist) {
            // بيانات المعالج
            this.therapist.name = user.name || ''
            this.therapist.avatar = user.avatar
            this.patientName = user.name || ''
            this.patientAvatar = user.avatar

            // جلب بيانات المعالج الكاملة من API (غير متزامن - لا ننتظره)
            this.fetchTherapistData().catch(err => {
              console.warn('Failed to fetch therapist details (non-critical):', err)
            })
          } else {
            // بيانات العميل/المريض
            this.patientName = user.name || ''
            this.patientAvatar = user.avatar
            this.patient.name = user.name || ''
            this.patient.avatar = user.avatar
            this.editProfileData = {
              name: user.name || '',
              age: null,
              email: user.email || '',
              phone: user.phone || '',
              gender: user.gender || '',
              date_of_birth: user.date_of_birth || '',
              country: user.country || '',
              city: user.city || '',
              governorate: user.governorate || '',
              district: user.district || '',
              marital_status: user.marital_status || '',
              education_level: user.education_level || '',
              employment_status: user.employment_status || '',
              profession: user.profession || '',
              monthly_income: user.monthly_income || ''
            }
          }
        } else {
          this.isAuthenticated = false
        }
      } catch (error) {
        console.error('Error fetching user data:', error)
        console.error('Error response:', error.response?.data)

        // إذا كان الخطأ 401 (غير مصرح)، اعتباره غير مسجل دخول ومسح جميع البيانات والـ tokens
        if (error.response?.status === 401) {
          console.log('401 Unauthorized - clearing user data and tokens')
          // مسح tokens من localStorage
          localStorage.removeItem('frontend_token')
          localStorage.removeItem('admin_token')
          this.clearUserData()
        } else {
          this.isAuthenticated = false
          this.patientName = 'ضيف'
          this.patientAvatar = null
        }
      }
    },

    async fetchTherapistData() {
      try {
        // محاولة جلب بيانات المعالج من API
        // أولاً: البحث في قائمة المعالجين
        const response = await api.get('/therapists', {
          params: {
            per_page: 100
          }
        })

        const therapists = response.data?.data || response.data || []

        // البحث عن المعالج الحالي
        const therapistData = therapists.find(t => {
          const userId = t.user?.id || t.user_id
          return userId === this.currentUser?.id
        })

        if (therapistData) {
          this.therapist.specialization = therapistData.specialty_ar || therapistData.specialty_en || ''
          this.therapist.avatar = therapistData.user?.avatar || this.currentUser.avatar
          this.therapist.session_duration = therapistData.session_duration || 45
        } else {
          // إذا لم يتم العثور عليه، استخدم البيانات الأساسية
          console.warn('Therapist data not found in list, using basic info')
        }
      } catch (error) {
        console.error('Error fetching therapist data:', error)
        // لا نوقف التحميل إذا فشل جلب بيانات المعالج
      }
    },

    async fetchAppointments() {
      // التحقق من تسجيل الدخول قبل محاولة جلب البيانات
      if (!this.isAuthenticated) {
        this.appointments = []
        return
      }

      try {
        const response = await api.get('/appointments', {
          params: {
            status: 'confirmed',
            per_page: 100
          }
        })

        if (response.data?.data) {
          this.appointments = response.data.data
        }
      } catch (error) {
        console.error('Error fetching appointments:', error)
        // إذا كان الخطأ 401، اعتباره غير مسجل دخول ومسح token
        if (error.response?.status === 401) {
          localStorage.removeItem('frontend_token')
          localStorage.removeItem('admin_token')
          this.clearUserData()
        }
        this.appointments = []
      }
    },

    async fetchSessions() {
      // التحقق من تسجيل الدخول قبل محاولة جلب البيانات
      if (!this.isAuthenticated) {
        this.sessions = []
        this.upcomingSessions = []
        this.previousSessions = []
        return
      }

      try {
        // API يقوم تلقائياً بفلترة الجلسات حسب نوع المستخدم
        // العميل: يرى جلساته فقط
        // المعالج: يرى الجلسات التي هو معالجها
        const response = await api.get('/sessions', {
          params: {
            per_page: 100
          }
        })

        console.log('Sessions API response:', response.data)

        // معالجة الاستجابة المختلفة
        if (response.data?.data) {
          // إذا كانت paginated
          this.sessions = Array.isArray(response.data.data) ? response.data.data : []
        } else if (Array.isArray(response.data)) {
          // إذا كانت array مباشرة
          this.sessions = response.data
        } else {
          this.sessions = []
        }

        console.log('Parsed sessions:', this.sessions.length, this.sessions)
      } catch (error) {
        console.error('Error fetching sessions:', error)
        console.error('Error details:', error.response?.data)

        // إذا كان الخطأ 401، اعتباره غير مسجل دخول ومسح token
        if (error.response?.status === 401) {
          localStorage.removeItem('frontend_token')
          localStorage.removeItem('admin_token')
          this.clearUserData()
        } else {
          // في حالة الخطأ الآخر، نعرض قائمة فارغة
          this.sessions = []
          this.upcomingSessions = []
          this.previousSessions = []
        }
      }
    },

    // معالجة بيانات الجلسات للمعالج
    processTherapistSessionsData() {
      if (!this.isAuthenticated) {
        this.upcomingSessions = []
        this.previousSessions = []
        return
      }

      try {
        const now = new Date()

        // معالجة الجلسات القادمة والنشطة (من منظور المعالج)
        this.upcomingSessions = (this.sessions || [])
          .filter(session => {
            // استبعاد الجلسات المخفية
            if (this.hiddenSessions.includes(session.id)) return false
            // استخدام start_time إذا كان موجوداً، وإلا استخدام appointment.starts_at
            const sessionDateTime = session.start_time || session.appointment?.starts_at
            if (!sessionDateTime) return false
            try {
              const sessionDate = new Date(sessionDateTime)
              // تضمين الجلسات النشطة حتى لو كانت في الماضي (لأنها نشطة الآن)
              if (session.status === 'active') return true
              return (session.status === 'scheduled' || session.status === 'active') &&
                sessionDate >= now
            } catch (e) {
              console.warn('Invalid session date:', sessionDateTime)
              return false
            }
          })
          .map(session => {
            const client = session.appointment?.client || {}
            const clientName = client?.name || 'غير محدد'

            // استخدام start_time إذا كان موجوداً، وإلا استخدام appointment.starts_at
            const sessionDateTime = session.start_time || session.appointment?.starts_at
            
            return {
              id: session.id,
              room_id: session.room_id,
              patient: {
                name: clientName,
                avatar: client?.avatar || null,
                email: client?.email || ''
              },
              type: session.type || 'جلسة فردية',
              date: sessionDateTime,
              time: this.formatTime(sessionDateTime),
              duration: this.therapist.session_duration || 45,
              status: session.status === 'active' ? 'active' : 'pending',
              description: session.appointment?.notes || 'جلسة علاجية',
              appointment: session.appointment
            }
          })
          .sort((a, b) => {
            if (!a.date || !b.date) return 0
            return new Date(a.date) - new Date(b.date)
          })

        // معالجة الجلسات السابقة (من منظور المعالج)
        this.previousSessions = (this.sessions || [])
          .filter(session => {
            // استبعاد الجلسات المخفية
            if (this.hiddenSessions.includes(session.id)) return false
            return session.status === 'ended' || session.status === 'completed'
          })
          .map(session => {
            const client = session.appointment?.client || {}
            const clientName = client?.name || 'غير محدد'
            const review = session.review || null

            return {
              id: session.id,
              room_id: session.room_id,
              patient: {
                name: clientName,
                avatar: client?.avatar || null,
                email: client?.email || ''
              },
              type: session.type || 'جلسة علاجية',
              date: session.end_time || session.start_time || session.appointment?.starts_at,
              review: review ? {
                rating: review.rating,
                comment: review.comment,
                createdAt: review.created_at,
                client: review.client
              } : null,
              progressScore: session.progress_score || 0,
              therapistNotes: session.therapist_notes || session.notes || session.appointment?.notes || '',
              appointment: session.appointment
            }
          })
          .sort((a, b) => {
            if (!a.date || !b.date) return 0
            return new Date(b.date) - new Date(a.date)
          })

        // تحديث إحصائيات المعالج
        this.therapist.totalSessions = this.sessions?.length || 0
        this.therapist.attendanceRate = this.calculateAttendanceRate()

        // حساب عدد المرضى الفريدين
        const uniquePatients = new Set()
          ; (this.sessions || []).forEach(session => {
            if (session.appointment?.client_id) {
              uniquePatients.add(session.appointment.client_id)
            }
          })
        this.therapist.totalPatients = uniquePatients.size

        console.log('Therapist sessions processed:', {
          upcoming: this.upcomingSessions.length,
          previous: this.previousSessions.length,
          total: this.sessions?.length || 0
        })

        if (this.isTherapist) {
          this.fetchTherapistPatients().then(() => {
            if (this.showProgressModal) {
              this.initializeProgressForm()
            }
          })
        }
      } catch (error) {
        console.error('Error processing therapist sessions data:', error)
        // في حالة الخطأ، نعرض قوائم فارغة بدلاً من إيقاف التحميل
        this.upcomingSessions = []
        this.previousSessions = []
      }
    },

    processSessionsData() {
      if (!this.isAuthenticated) {
        this.upcomingSessions = []
        this.previousSessions = []
        return
      }

      const now = new Date()

      // معالجة الجلسات القادمة والنشطة
      this.upcomingSessions = this.sessions
        .filter(session => {
          // استبعاد الجلسات المخفية
          if (this.hiddenSessions.includes(session.id)) return false
          // استخدام start_time إذا كان موجوداً، وإلا استخدام appointment.starts_at
          const sessionDateTime = session.start_time || session.appointment?.starts_at
          if (!sessionDateTime) return false
          const sessionDate = new Date(sessionDateTime)
          // تضمين الجلسات النشطة حتى لو كانت في الماضي (لأنها نشطة الآن)
          if (session.status === 'active') return true
          return (session.status === 'scheduled' || session.status === 'active') &&
            sessionDate >= now
        })
        .map(session => {
          const therapist = session.appointment?.therapist
          const therapistName = therapist?.name_ar || therapist?.name_en || 'غير محدد'
          const therapistSpecialization = therapist?.specialty_ar || therapist?.specialty_en || ''

          // استخدام start_time إذا كان موجوداً، وإلا استخدام appointment.starts_at
          const sessionDateTime = session.start_time || session.appointment?.starts_at
          
          return {
            id: session.id,
            room_id: session.room_id,
            therapist: {
              name: therapistName,
              avatar: therapist?.user?.avatar || null,
              specialization: therapistSpecialization
            },
            type: session.type || 'جلسة فردية',
            date: sessionDateTime,
            time: this.formatTime(sessionDateTime),
            duration: therapist?.session_duration || 45,
            status: session.status === 'active' ? 'active' : 'pending',
            description: session.appointment?.notes || 'جلسة علاجية',
            appointment: session.appointment
          }
        })
        .sort((a, b) => new Date(a.date) - new Date(b.date))

      // معالجة الجلسات السابقة
      this.previousSessions = this.sessions
        .filter(session => {
          // استبعاد الجلسات المخفية
          if (this.hiddenSessions.includes(session.id)) return false
          return session.status === 'ended' || session.status === 'completed'
        })
        .map(session => {
          const therapist = session.appointment?.therapist
          const therapistName = therapist?.name_ar || therapist?.name_en || 'غير محدد'
          const therapistSpecialization = therapist?.specialty_ar || therapist?.specialty_en || ''
          const review = session.review || null

          return {
            id: session.id,
            therapist: {
              name: therapistName,
              avatar: therapist?.user?.avatar || null,
              specialization: therapistSpecialization
            },
            type: session.type || 'جلسة علاجية',
            date: session.end_time || session.start_time || session.appointment?.starts_at,
            review: review ? {
              rating: review.rating,
              comment: review.comment,
              createdAt: review.created_at
            } : null,
            canRate: !review && session.status === 'ended',
            progressScore: session.progress_score || 0,
            therapistNotes: session.therapist_notes || session.notes || session.appointment?.notes || '',
            appointment: session.appointment
          }
        })
        .sort((a, b) => new Date(b.date) - new Date(a.date))

      // تحديث إحصائيات المريض
      this.patient.totalSessions = this.sessions.length
      this.patient.attendanceRate = this.calculateAttendanceRate()
      this.patient.progress = this.calculateProgress()

      // تحديث بيانات المعالج من آخر موعد
      if (this.appointments.length > 0) {
        const lastAppointment = this.appointments[0]
        this.patient.therapist = {
          id: lastAppointment.therapist?.id || lastAppointment.therapist_id || null,
          name: lastAppointment.therapist?.name_ar ||
            lastAppointment.therapist?.name_en ||
            'غير محدد',
          avatar: lastAppointment.therapist?.user?.avatar || null,
          specialization: lastAppointment.therapist?.specialty_ar ||
            lastAppointment.therapist?.specialty_en ||
            ''
        }
      }
    },

    calculateAttendanceRate() {
      if (this.sessions.length === 0) return 0
      const attended = this.sessions.filter(s => s.status === 'ended').length
      return Math.round((attended / this.sessions.length) * 100)
    },

    calculateProgress() {
      const ratedSessions = (this.sessions || []).filter(session => (session.progress_score ?? 0) > 0)
      if (ratedSessions.length > 0) {
        const total = ratedSessions.reduce((sum, session) => sum + (session.progress_score || 0), 0)
        return Math.round(total / ratedSessions.length)
      }
      if (this.sessions.length === 0) return 0
      const completed = this.sessions.filter(s => s.status === 'ended').length
      return Math.min(Math.round((completed / Math.max(this.sessions.length, 1)) * 100), 100)
    },

    async joinSession(session) {
      if (session.room_id) {
        this.$router.push(`/session/${session.room_id}`)
      } else {
        console.error('No room_id for session:', session.id)
        alert('لا يمكن الانضمام للجلسة: معرف الجلسة غير موجود')
      }
    },

    viewSessionNotes(session) {
      if (session?.id) {
        const fullSession = this.previousSessions.find(s => s.id === session.id) || session
        this.selectedSession = fullSession
        this.showSessionNotesModal = true
      } else {
        this.selectedSession = session
        this.showSessionNotesModal = true
      }
    },

    openRatingModal(session) {
      if (!session?.id) return
      this.ratingTargetSession = session
      this.ratingForm = {
        sessionId: session.id,
        therapistName: session.therapist?.name || '',
        rating: 5,
        comment: ''
      }
      this.showRatingModal = true
    },

    closeRatingModal() {
      this.showRatingModal = false
      this.ratingSubmitting = false
      this.ratingTargetSession = null
      this.ratingForm = {
        sessionId: null,
        therapistName: '',
        rating: 5,
        comment: ''
      }
    },

    setRatingValue(value) {
      this.ratingForm.rating = value
    },

    async submitRating() {
      if (!this.ratingForm.sessionId || this.ratingSubmitting) return
      this.ratingSubmitting = true
      try {
        await api.post(`/sessions/${this.ratingForm.sessionId}/review`, {
          rating: this.ratingForm.rating,
          comment: this.ratingForm.comment?.trim() || null
        })
        alert('شكراً لمشاركتك رأيك في المعالج.')
        this.closeRatingModal()
        await this.fetchSessions()
        this.processSessionsData()
      } catch (error) {
        console.error('Error submitting rating:', error)
        const message = error.response?.data?.message || 'فشل حفظ التقييم. حاول مجدداً.'
        alert(message)
      } finally {
        this.ratingSubmitting = false
      }
    },

    async fetchPatientAssessments() {
      if (this.isTherapist || !this.isAuthenticated) return

      this.loadingAssessments = true
      try {
        const response = await api.get('/assessments')
        const data = response.data?.data || response.data || []
        this.patientAssessments = Array.isArray(data) ? data : []
      } catch (error) {
        console.error('Error fetching assessments:', error)
        this.patientAssessments = []
      } finally {
        this.loadingAssessments = false
      }
    },

    async saveProfile() {
      try {
        const payload = {
          name: this.editProfileData.name,
          email: this.editProfileData.email,
          phone: this.editProfileData.phone,
          gender: this.editProfileData.gender || null,
          date_of_birth: this.editProfileData.date_of_birth || null,
          country: this.editProfileData.country || null,
          city: this.editProfileData.city || null,
          governorate: this.editProfileData.governorate || null,
          district: this.editProfileData.district || null,
          marital_status: this.editProfileData.marital_status || null,
          education_level: this.editProfileData.education_level || null,
          employment_status: this.editProfileData.employment_status || null,
          profession: this.editProfileData.profession || null,
          monthly_income: this.editProfileData.monthly_income || null
        }
        await api.put('/user', payload)
        await this.fetchUserData()
        this.showEditProfileModal = false
        alert('تم حفظ التغييرات بنجاح')
      } catch (error) {
        console.error('Error saving profile:', error)
        const message = error.response?.data?.message || 'فشل حفظ التغييرات'
        alert(message)
      }
    },

    contactSupport(method) {
      const phone = this.supportContact.phone.replace(/\s+/g, '')
      if (method === 'whatsapp') {
        window.open(`https://wa.me/${this.supportContact.whatsapp}`, '_blank')
      } else if (method === 'phone') {
        window.location.href = `tel:${phone}`
      } else if (method === 'email') {
        window.location.href = `mailto:${this.supportContact.email}`
      }
      this.showSupportModal = false
    },

    closeSupportModal() {
      this.showSupportModal = false
    },

    openProgressModal() {
      this.showProgressModal = true
      if (this.isTherapist) {
        this.initializeProgressForm()
      }
    },

    formatDate(dateString) {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },

    formatTime(dateString) {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleTimeString('ar-SA', {
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    getStatusText(status) {
      const statusMap = {
        'active': 'نشطة الآن',
        'scheduled': 'مجدولة',
        'pending': 'قيد الانتظار',
        'ended': 'منتهية',
        'completed': 'مكتملة'
      }
      return statusMap[status] || status
    },

    getMaritalStatusText(status) {
      const map = {
        'single': 'عازب',
        'married': 'متزوج',
        'divorced': 'مطلق',
        'widowed': 'أرمل'
      }
      return map[status] || status || '-'
    },

    getEducationLevelText(level) {
      const map = {
        'elementary': 'ابتدائية',
        'middle': 'إعدادية',
        'high_school': 'ثانوية',
        'diploma': 'دبلوم',
        'bachelor': 'بكالوريوس',
        'graduate': 'دراسات عليا'
      }
      return map[level] || level || '-'
    },

    getEmploymentStatusText(status) {
      const map = {
        'student': 'طالب',
        'government_employee': 'موظف حكومي',
        'private_employee': 'موظف خاص',
        'unemployed': 'عاطل عن العمل',
        'housewife': 'ربة بيت',
        'retired': 'متقاعد'
      }
      return map[status] || status || '-'
    },

    getMonthlyIncomeText(income) {
      const map = {
        'less_than_60k': 'أقل من 60 ألف',
        '61k_to_120k': 'من 61 ألف وحتى 120 ألف',
        '121k_to_200k': 'من 121 ألف وحتى 200 ألف',
        '201k_to_350k': 'من 201 ألف وحتى 350 ألف',
        'more_than_351k': 'ما فوق 351 ألف'
      }
      return map[income] || income || '-'
    },

    getPlatformPurposesText(purposes) {
      if (!purposes || !Array.isArray(purposes) || purposes.length === 0) return '-'
      const map = {
        'information_resources': 'الحصول على معلومات وموارد',
        'self_assessment': 'التقييم الذاتي للمشاعر',
        'psychological_consultation': 'البحث عن استشارة نفسية',
        'electronic_programs': 'المشاركة في برامج إلكترونية',
        'other': 'أخرى'
      }
      return purposes.map(p => map[p] || p).join('، ')
    },

    getTimeRemaining(session) {
      if (!session.date) return '-'
      const sessionDate = new Date(session.date)
      const now = new Date()
      const diff = sessionDate - now

      if (diff <= 0) return 'بدأت'

      const days = Math.floor(diff / (1000 * 60 * 60 * 24))
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))

      if (days > 0) {
        return `${days} يوم ${hours} ساعة`
      } else if (hours > 0) {
        return `${hours} ساعة ${minutes} دقيقة`
      } else {
        return `${minutes} دقيقة`
      }
    },

    initializeProgressForm() {
      if (!this.isTherapist) return
      const options = this.progressSessionsOptions
      this.progressForm.sessionId = options.length ? options[0].id : null
      this.progressForm.progressScore = 70
      this.progressForm.notes = ''
    },

    async fetchTherapistPatients() {
      if (!this.isTherapist) return

      try {
        const response = await api.get('/sessions/therapist/patients')
        if (response.data?.success) {
          this.therapistPatients = response.data.data || []
          this.lastPatientId = response.data.last_patient || null
        }
      } catch (error) {
        console.error('Error fetching therapist patients:', error)
        this.therapistPatients = []
      }
    },

    async selectPatientForProgress(patient) {
      this.selectedPatientForProgress = patient
      this.progressForm.sessionId = null
      this.progressForm.progressScore = 70
      this.progressForm.notes = ''
      
      // Load patient report
      await this.loadPatientReport(patient.id)
    },

    async loadPatientReport(patientId) {
      this.loadingPatientReport = true
      try {
        const response = await api.get(`/sessions/patient/${patientId}/report`)
        console.log('Patient Report Response:', response.data)
        if (response.data?.success) {
          this.patientReport = response.data.data
          console.log('Patient Report Data:', this.patientReport)
          console.log('Assessments:', this.patientReport?.assessments)
          console.log('Assessments Count:', this.patientReport?.assessments?.length)
        }
      } catch (error) {
        console.error('Error loading patient report:', error)
        console.error('Error response:', error.response?.data)
        alert('فشل تحميل تقرير المريض')
      } finally {
        this.loadingPatientReport = false
      }
    },

    async viewPatientReportBeforeSession(session) {
      if (!session || !session.appointment) {
        alert('معلومات الجلسة غير متوفرة')
        return
      }

      // Get patient ID from session
      const patientId = session.appointment?.client_id || session.appointment?.client?.id
      if (!patientId) {
        alert('معرف المريض غير متوفر')
        return
      }

      // Load and show patient report
      await this.loadPatientReport(patientId)
      if (this.patientReport) {
        this.showPatientReport = true
      }
    },

    async submitProgressUpdate() {
      if (!this.isTherapist) return
      if (!this.selectedPatientForProgress) {
        alert('يرجى اختيار المريض أولاً.')
        return
      }
      if (!this.progressForm.sessionId) {
        alert('يرجى اختيار الجلسة التي تريد تقييمها.')
        return
      }
      if (!this.progressForm.notes.trim()) {
        alert('يرجى كتابة ملاحظات حول حالة المريض.')
        return
      }

      this.progressSubmitting = true
      const cleanNotes = this.progressForm.notes.trim()
      try {
        await api.post(`/sessions/${this.progressForm.sessionId}/progress`, {
          progress_score: this.progressForm.progressScore,
          therapist_notes: cleanNotes
        })
        alert('تم حفظ تقييم الجلسة بنجاح.')
        await this.fetchSessions()
        this.processTherapistSessionsData()
        await this.fetchTherapistPatients()
        this.initializeProgressForm()
        this.selectedPatientForProgress = null
        this.patientReport = null
      } catch (error) {
        console.error('Error saving progress:', error)
        const message = error.response?.data?.message || 'فشل حفظ التقييم. حاول مجدداً.'
        alert(message)
      } finally {
        this.progressSubmitting = false
      }
    },

    hideSession(session) {
      if (!session?.id) return
      if (confirm('هل تريد إخفاء هذه الجلسة من الواجهة؟ (ستبقى في التقرير والقاعدة)')) {
        if (!this.hiddenSessions.includes(session.id)) {
          this.hiddenSessions.push(session.id)
          // حفظ في localStorage
          localStorage.setItem('hiddenSessions', JSON.stringify(this.hiddenSessions))
          // إعادة معالجة البيانات
          if (this.isTherapist) {
            this.processTherapistSessionsData()
          } else {
            this.processSessionsData()
          }
        }
      }
    },
  }
}
</script>

<style>
.hero-shape {
  position: absolute;
  border-radius: 999px;
  opacity: 0.35;
  filter: blur(60px);
}

.hero-shape-left {
  width: 320px;
  height: 320px;
  left: -120px;
  top: -80px;
  background: radial-gradient(circle, #9EBF3B 0%, transparent 70%);
}

.hero-shape-right {
  width: 360px;
  height: 360px;
  right: -160px;
  bottom: -100px;
  background: radial-gradient(circle, #D6A29A 0%, transparent 75%);
}

.glass-card {
  background: rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow: 0 20px 50px rgba(158, 191, 59, 0.1);
}

.glass-panel {
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
  border: 1px solid rgba(255, 255, 255, 0.6);
  box-shadow: 0 15px 35px rgba(214, 162, 154, 0.15);
}

.stat-card,
.mini-stat {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 1rem;
  padding: 0.75rem;
  border: 1px solid rgba(255, 255, 255, 0.6);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
}

.mini-stat .stat-value {
  font-size: 1.4rem;
  font-weight: 700;
  color: #111827;
}

.mini-stat .stat-label {
  font-size: 0.8rem;
  color: #6b7280;
}

.action-button {
  width: 100%;
  background: white;
  border: 1px solid rgba(229, 231, 235, 0.9);
  border-radius: 0.9rem;
  padding: 0.85rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.9rem;
  color: #374151;
  transition: all 0.2s ease;
}

.action-button:hover {
  background: rgba(158, 191, 59, 0.08);
  border-color: rgba(158, 191, 59, 0.4);
}

.session-card {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 1.5rem;
  backdrop-filter: blur(6px);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.session-card.active {
  border-color: #9EBF3B;
  box-shadow: 0 20px 35px rgba(158, 191, 59, 0.15);
}

.session-card.faded {
  opacity: 0.85;
}

.primary-button {
  background: linear-gradient(135deg, #9EBF3B, #8cad35);
  color: white;
  padding: 0.85rem 1rem;
  border-radius: 0.9rem;
  font-weight: 600;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: none;
}

.primary-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px rgba(158, 191, 59, 0.25);
}

.secondary-button {
  background: rgba(17, 24, 39, 0.04);
  color: #374151;
  padding: 0.85rem 1rem;
  border-radius: 0.9rem;
  font-weight: 600;
  border: 1px solid rgba(17, 24, 39, 0.08);
  transition: background 0.2s ease, color 0.2s ease;
}

.secondary-button:hover {
  background: rgba(17, 24, 39, 0.08);
}

.countdown-card {
  background: rgba(214, 162, 154, 0.08);
}

.avatar-frame {
  background: white;
  padding: 0.25rem;
  border-radius: 999px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

/* Custom CSS for the color variables */
:root {
  --brand-500: #9EBF3B;
  --brand-600: #8cad35;
  --brand-50: #f0fdf4;
  --brand-700: #166534;
  --accent-500: #D6A29A;
  --accent-600: #c99188;
  --accent-50: #fdf2f2;
  --accent-200: #fbcfe8;
  --accent-700: #be185d;
}

.bg-brand-500 {
  background-color: var(--brand-500);
}

.hover\:bg-brand-600:hover {
  background-color: var(--brand-600);
}

.text-brand-500 {
  color: var(--brand-500);
}

.border-brand-500 {
  border-color: var(--brand-500);
}

.bg-brand-50 {
  background-color: var(--brand-50);
}

.text-brand-700 {
  color: var(--brand-700);
}

.bg-accent-50 {
  background-color: var(--accent-50);
}

.text-accent-700 {
  color: var(--accent-700);
}

.border-accent-200 {
  border-color: var(--accent-200);
}

/* Default Avatar Styles */
.default-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  color: white;
  font-weight: bold;
}

.default-avatar.patient {
  background: linear-gradient(135deg, var(--brand-500), var(--brand-600));
}

.default-avatar.therapist {
  background: linear-gradient(135deg, var(--accent-500), var(--accent-600));
}

.default-avatar.support {
  background: linear-gradient(135deg, #3B82F6, #1D4ED8);
}

.default-avatar.large {
  width: 80px;
  height: 80px;
  font-size: 2rem;
}

.default-avatar:not(.large) {
  width: 40px;
  height: 40px;
  font-size: 1.2rem;
}

.default-avatar.empty-state {
  width: 100px;
  height: 100px;
  font-size: 3rem;
  background: #e5e7eb;
  color: #9ca3af;
  margin: 0 auto 1rem;
}

.avatar-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-container.large .default-avatar {
  width: 80px;
  height: 80px;
  font-size: 2rem;
}

.support-modal-simple {
  width: 100%;
  max-width: 420px;
  background: #fff;
  border-radius: 22px;
  padding: 1.5rem;
  box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
  border: 1px solid #f1f5f9;
}

.support-quick-option {
  width: 100%;
  border-radius: 16px;
  padding: 12px 14px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all 0.2s ease;
}

.support-quick-option .icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  color: #fff;
}

.support-quick-option.whatsapp {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: #fff;
  border-color: transparent;
}

.support-quick-option.whatsapp .icon {
  background: rgba(255, 255, 255, 0.2);
}

.support-quick-option.phone .icon {
  background: #e0f2fe;
  color: #0ea5e9;
}

.support-quick-option.email .icon {
  background: #fef3c7;
  color: #d97706;
}

.support-quick-option:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
}

.progress-entry {
  background: #fff;
  border-radius: 1rem;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
}

.score-badge {
  min-width: 48px;
  padding: 6px 10px;
  border-radius: 999px;
  background: rgba(158, 191, 59, 0.12);
  color: #4d5d1f;
  font-weight: 700;
  text-align: center;
}

.form-label {
  display: block;
  font-size: 0.9rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  padding: 12px 14px;
  font-size: 0.95rem;
  color: #0f172a;
  background: #fdfdfd;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input:focus {
  border-color: #9EBF3B;
  box-shadow: 0 0 0 3px rgba(158, 191, 59, 0.15);
  outline: none;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #9EBF3B;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #8cad35;
}

/* Range Slider Styles */
input[type="range"].slider {
  -webkit-appearance: none;
  appearance: none;
  background: transparent;
  cursor: pointer;
}

input[type="range"].slider::-webkit-slider-track {
  background: #e5e7eb;
  height: 8px;
  border-radius: 10px;
}

input[type="range"].slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  background: linear-gradient(135deg, #9EBF3B, #8cad35);
  height: 20px;
  width: 20px;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 2px 6px rgba(158, 191, 59, 0.4);
  cursor: pointer;
  transition: all 0.2s ease;
}

input[type="range"].slider::-webkit-slider-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 3px 8px rgba(158, 191, 59, 0.6);
}

input[type="range"].slider::-moz-range-track {
  background: #e5e7eb;
  height: 8px;
  border-radius: 10px;
}

input[type="range"].slider::-moz-range-thumb {
  background: linear-gradient(135deg, #9EBF3B, #8cad35);
  height: 20px;
  width: 20px;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 2px 6px rgba(158, 191, 59, 0.4);
  cursor: pointer;
  transition: all 0.2s ease;
}

input[type="range"].slider::-moz-range-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 3px 8px rgba(158, 191, 59, 0.6);
}
</style>