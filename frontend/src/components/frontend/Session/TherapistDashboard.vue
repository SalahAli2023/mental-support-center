<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
          <div class="flex items-center space-x-4 space-x-reverse">
            <h1 class="text-lg font-semibold text-gray-900">لوحة المعالج</h1>
          </div>
          <div class="flex items-center space-x-3 space-x-reverse">
            <span class="text-gray-600 text-sm">{{ therapist.name }}</span>
            <div class="avatar-container">
              <div class="default-avatar therapist">
                <i class="fas fa-user-md"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex space-x-8 space-x-reverse">
          <router-link 
            to="/" 
            class="py-3 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300"
          >
            الرئيسية
          </router-link>
          <router-link 
            to="/sessions" 
            class="py-3 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300"
          >
            الجلسات
          </router-link>
          <router-link 
            to="/patients" 
            class="py-3 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300"
          >
            المرضى
          </router-link>
          <router-link 
            to="/profile" 
            class="py-3 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300"
          >
            الملف الشخصي
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Profile Section -->
        <div class="lg:col-span-1">
          <div class="sticky top-24 space-y-4">
            <!-- Profile Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
              <!-- Profile Header -->
              <div class="text-center mb-4">
                <div class="avatar-container large mb-4">
                  <div class="default-avatar therapist large">
                    <i class="fas fa-user-md"></i>
                  </div>
                </div>
                <h2 class="font-semibold text-gray-900 mb-2">{{ therapist.name }}</h2>
                <p class="text-gray-500 text-sm">{{ therapist.specialization }}</p>
              </div>

              <!-- Profile Stats -->
              <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                  <p class="text-xl font-bold text-brand-500 mb-1">{{ stats.totalPatients }}</p>
                  <p class="text-gray-600 text-xs">المرضى</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                  <p class="text-xl font-bold text-brand-500 mb-1">{{ stats.totalSessions }}</p>
                  <p class="text-gray-600 text-xs">الجلسات</p>
                </div>
              </div>

              <!-- Progress -->
              <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium text-gray-700">معدل الحضور</span>
                  <span class="text-sm text-brand-500">{{ stats.attendanceRate }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-brand-500 h-2 rounded-full transition-all duration-300"
                    :style="{ width: stats.attendanceRate + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Current Patients -->
              <div class="border-t border-gray-200 pt-4">
                <h4 class="font-medium text-gray-900 mb-4">المرضى النشطين</h4>
                <div class="space-y-3">
                  <div v-for="patient in activePatients.slice(0, 3)" :key="patient.id" class="flex items-center space-x-3 space-x-reverse">
                    <div class="avatar-container">
                      <div class="default-avatar patient">
                        <i class="fas fa-user"></i>
                      </div>
                    </div>
                    <div class="flex-1 mr-3">
                      <p class="font-medium text-gray-900 text-sm">{{ patient.name }}</p>
                      <p class="text-gray-500 text-xs">{{ patient.sessionsCount }} جلسة</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-2">
              <button @click="showPatientsModal = true" class="w-full bg-white border border-gray-200 rounded-lg p-3 text-right hover:bg-gray-50 text-sm transition-colors flex items-center justify-between">
                <span class="flex items-center">
                  <i class="fas fa-users ml-3 text-brand-500"></i>
                  عرض جميع المرضى
                </span>
                <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
              </button>
              <button @click="showReportsModal = true" class="w-full bg-white border border-gray-200 rounded-lg p-3 text-right hover:bg-gray-50 text-sm transition-colors flex items-center justify-between">
                <span class="flex items-center">
                  <i class="fas fa-chart-bar ml-3 text-brand-500"></i>
                  التقارير
                </span>
                <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
              </button>
              <button @click="showSupportModal = true" class="w-full bg-white border border-gray-200 rounded-lg p-3 text-right hover:bg-gray-50 text-sm transition-colors flex items-center justify-between">
                <span class="flex items-center">
                  <i class="fas fa-headset ml-3 text-brand-500"></i>
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
            <h2 class="text-2xl font-bold text-gray-900 mb-3">جلسات اليوم</h2>
            <p class="text-gray-600">إدارة وتتبع الجلسات العلاجية</p>
          </div>

          <!-- Today's Sessions -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-calendar-day ml-3 text-brand-500"></i>
              جلسات اليوم ({{ todaysSessions.length }})
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div 
                v-for="session in todaysSessions" 
                :key="session.id"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                :class="{ 'border-brand-500 border-2': session.status === 'active' }"
              >
                <!-- Patient Info -->
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="avatar-container">
                      <div class="default-avatar patient">
                        <i class="fas fa-user"></i>
                      </div>
                    </div>
                    <div class="mr-3">
                      <h4 class="font-semibold text-gray-900 mb-2">{{ session.patient.name }}</h4>
                      <span class="text-gray-500 text-sm">{{ session.type }}</span>
                    </div>
                  </div>
                  <span 
                    class="text-xs px-3 py-1 rounded-full font-medium"
                    :class="{
                      'bg-brand-50 text-brand-700': session.status === 'active',
                      'bg-accent-50 text-accent-700': session.status === 'upcoming'
                    }"
                  >
                    {{ getStatusText(session.status) }}
                  </span>
                </div>

                <!-- Session Details -->
                <div class="space-y-4 mb-6">
                  <div class="flex items-center text-gray-600 text-sm">
                    <i class="fas fa-clock ml-3 text-brand-500 w-4"></i>
                    <span>{{ session.time }} - {{ session.duration }} دقيقة</span>
                  </div>
                  <div class="flex items-center text-gray-600 text-sm">
                    <i class="fas fa-stopwatch ml-3 text-brand-500 w-4"></i>
                    <span>متبقي: {{ getTimeRemaining(session) }}</span>
                  </div>
                </div>

                <!-- Notes -->
                <p class="text-gray-700 text-sm mb-6 leading-relaxed">{{ session.notes }}</p>

                <!-- Action Buttons -->
                <button 
                  v-if="session.status === 'active'"
                  @click="startSession(session)"
                  class="w-full bg-brand-500 text-white py-3 rounded-lg hover:bg-brand-600 font-medium transition-colors flex items-center justify-center space-x-2 space-x-reverse"
                >
                  <i class="fas fa-play"></i>
                  <span>بدء الجلسة</span>
                </button>

                <div v-else class="text-center py-3 bg-accent-50 rounded-lg border border-accent-200">
                  <div class="text-accent-700 text-sm flex items-center justify-center space-x-2 space-x-reverse">
                    <i class="fas fa-hourglass-half"></i>
                    <span>تبدأ بعد: {{ getTimeRemaining(session) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Upcoming Sessions -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-calendar-alt ml-3 text-brand-500"></i>
              الجلسات القادمة
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div 
                v-for="session in upcomingSessions" 
                :key="session.id"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <!-- Patient Info -->
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="avatar-container">
                      <div class="default-avatar patient">
                        <i class="fas fa-user"></i>
                      </div>
                    </div>
                    <div class="mr-3">
                      <h4 class="font-semibold text-gray-900 mb-2">{{ session.patient.name }}</h4>
                      <span class="text-gray-500 text-sm">{{ session.type }}</span>
                    </div>
                  </div>
                  <div class="text-center bg-gray-100 rounded p-2">
                    <p class="text-xs text-gray-600">{{ formatDay(session.date) }}</p>
                    <p class="font-semibold text-gray-900">{{ formatDateNumber(session.date) }}</p>
                  </div>
                </div>

                <!-- Session Details -->
                <div class="space-y-4 mb-6">
                  <div class="flex items-center text-gray-600 text-sm">
                    <i class="fas fa-clock ml-3 text-brand-500 w-4"></i>
                    <span>{{ session.time }} - {{ session.duration }} دقيقة</span>
                  </div>
                </div>

                <div class="flex space-x-3 space-x-reverse">
                  <button 
                    @click="viewPatientProfile(session.patient)"
                    class="flex-1 bg-brand-500 text-white py-3 rounded-lg hover:bg-brand-600 font-medium transition-colors flex items-center justify-center space-x-2 space-x-reverse"
                  >
                    <i class="fas fa-user"></i>
                    <span>ملف المريض</span>
                  </button>
                  
                  <button 
                    @click="viewSessionDetails(session)"
                    class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 font-medium transition-colors flex items-center justify-center space-x-2 space-x-reverse"
                  >
                    <i class="fas fa-eye"></i>
                    <span>التفاصيل</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Previous Sessions -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-history ml-3 text-gray-600"></i>
              الجلسات السابقة
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div 
                v-for="session in previousSessions" 
                :key="session.id"
                class="bg-white border border-gray-200 rounded-lg p-6 opacity-75 hover:opacity-100 transition-all"
              >
                <!-- Patient Info -->
                <div class="flex items-center justify-between mb-6">
                  <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="avatar-container">
                      <div class="default-avatar patient">
                        <i class="fas fa-user"></i>
                      </div>
                    </div>
                    <div class="mr-3">
                      <h4 class="font-semibold text-gray-900 mb-2">{{ session.patient.name }}</h4>
                      <span class="text-gray-500 text-sm">{{ session.type }}</span>
                    </div>
                  </div>
                  <div class="flex items-center space-x-1 space-x-reverse">
                    <i 
                      v-for="star in 5" 
                      :key="star"
                      class="fas fa-star text-sm"
                      :class="star <= session.rating ? 'text-yellow-400' : 'text-gray-300'"
                    ></i>
                  </div>
                </div>

                <!-- Session Details -->
                <div class="space-y-4 mb-6">
                  <div class="flex items-center text-gray-600 text-sm">
                    <i class="fas fa-calendar ml-3 text-brand-500 w-4"></i>
                    <span>{{ formatDate(session.date) }}</span>
                  </div>
                </div>

                <button 
                  @click="viewSessionNotes(session)"
                  class="w-full bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 font-medium transition-colors flex items-center justify-center space-x-2 space-x-reverse"
                >
                  <i class="fas fa-eye"></i>
                  <span>عرض الملاحظات</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- No Sessions Message -->
    <div v-if="todaysSessions.length === 0 && upcomingSessions.length === 0" 
         class="text-center py-16">
      <div class="default-avatar large empty-state mb-6">
        <i class="fas fa-calendar-times"></i>
      </div>
      <h3 class="text-gray-600 text-lg mb-3">لا توجد جلسات</h3>
      <p class="text-gray-500">لا توجد جلسات مسجلة حالياً</p>
    </div>

    <!-- Patients Modal -->
    <div v-if="showPatientsModal" class="fixed inset-0 bg-white/20 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-4xl border border-gray-200 shadow-xl">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="font-semibold text-gray-900">جميع المرضى</h3>
          <button @click="showPatientsModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
              v-for="patient in activePatients" 
              :key="patient.id"
              class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all"
            >
              <div class="flex items-center mb-3">
                <div class="avatar-container">
                  <div class="default-avatar patient">
                    <i class="fas fa-user"></i>
                  </div>
                </div>
                <div class="mr-3">
                  <h4 class="font-semibold text-gray-900">{{ patient.name }}</h4>
                  <p class="text-gray-500 text-sm">{{ patient.age }} سنة</p>
                </div>
              </div>
              
              <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                  <span>الجلسات</span>
                  <span class="font-medium">{{ patient.sessionsCount }}</span>
                </div>
                <div class="flex justify-between">
                  <span>آخر جلسة</span>
                  <span class="font-medium">{{ patient.lastSession }}</span>
                </div>
                <div class="flex justify-between">
                  <span>التقدم</span>
                  <span class="font-medium text-brand-500">{{ patient.progress }}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reports Modal -->
    <div v-if="showReportsModal" class="fixed inset-0 bg-white/20 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-4xl border border-gray-200 shadow-xl">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="font-semibold text-gray-900">التقارير والإحصائيات</h3>
          <button @click="showReportsModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              <h4 class="font-medium text-gray-900 mb-3">توزيع الجلسات</h4>
              <div class="h-40 bg-white rounded border border-gray-300 flex items-center justify-center text-gray-400">
                رسم بياني للجلسات
              </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              <h4 class="font-medium text-gray-900 mb-3">معدل الحضور</h4>
              <div class="h-40 bg-white rounded border border-gray-300 flex items-center justify-center text-gray-400">
                رسم بياني للحضور
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h4 class="font-medium text-gray-900 mb-3">ملخص الأداء</h4>
            <div class="space-y-2 text-sm text-gray-600">
              <p>• إجمالي الجلسات هذا الشهر: {{ stats.monthlySessions }}</p>
              <p>• عدد المرضى النشطين: {{ stats.totalPatients }}</p>
              <p>• متوسط تقييم الجلسات: {{ stats.averageRating }}/5</p>
              <p>• أعلى معدل تقدم: {{ stats.bestProgress }}%</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Support Modal -->
    <div v-if="showSupportModal" class="fixed inset-0 bg-white/20 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-md border border-gray-200 shadow-xl">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="font-semibold text-gray-900">فريق الدعم الفني</h3>
          <button @click="showSupportModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="p-4 space-y-4">
          <div class="text-center">
            <div class="default-avatar support large mb-3 mx-auto">
              <i class="fas fa-headset"></i>
            </div>
            <h4 class="font-medium text-gray-900 mb-2">فريق الدعم متاح 24/7</h4>
            <p class="text-gray-600 text-sm">نحن هنا لمساعدتك في أي وقت</p>
          </div>
          
          <div class="space-y-3">
            <button @click="contactSupport('whatsapp')" class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 font-medium flex items-center justify-center space-x-2 space-x-reverse transition-colors">
              <i class="fab fa-whatsapp"></i>
              <span>واتساب</span>
            </button>
            
            <button @click="contactSupport('phone')" class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 font-medium flex items-center justify-center space-x-2 space-x-reverse transition-colors">
              <i class="fas fa-phone"></i>
              <span>اتصال هاتفي</span>
            </button>
            
            <button @click="contactSupport('email')" class="w-full bg-brand-500 text-white py-3 rounded-lg hover:bg-brand-600 font-medium flex items-center justify-center space-x-2 space-x-reverse transition-colors">
              <i class="fas fa-envelope"></i>
              <span>بريد إلكتروني</span>
            </button>
          </div>
          
          <div class="text-center text-sm text-gray-500 border-t border-gray-200 pt-4">
            <p>رقم الدعم: <span class="font-medium">9200XXXXX</span></p>
            <p>البريد الإلكتروني: <span class="font-medium">support@therapy.com</span></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TherapistDashboard',
  data() {
    return {
      showPatientsModal: false,
      showReportsModal: false,
      showSupportModal: false,
      therapist: {
        name: 'عمرو عادل',
        specialization: 'استشاري الصحة النفسية'
      },
      stats: {
        totalPatients: 24,
        totalSessions: 156,
        attendanceRate: 94,
        monthlySessions: 18,
        averageRating: 4.7,
        bestProgress: 85
      },
      todaysSessions: [
        {
          id: 1,
          patient: {
            id: 101,
            name: 'أحمد محمد'
          },
          type: 'جلسة فردية',
          date: '2024-01-15',
          time: '14:00',
          duration: 45,
          status: 'active',
          notes: 'جلسة متابعة للعلاج المعرفي السلوكي'
        },
        {
          id: 2,
          patient: {
            id: 102,
            name: 'فاطمة أحمد'
          },
          type: 'جلسة جماعية',
          date: '2024-01-15',
          time: '16:00',
          duration: 60,
          status: 'upcoming',
          notes: 'جلسة دعم جماعي لإدارة القلق'
        }
      ],
      upcomingSessions: [
        {
          id: 3,
          patient: {
            id: 103,
            name: 'محمد خالد'
          },
          type: 'جلسة تقييم',
          date: '2024-01-16',
          time: '11:00',
          duration: 60
        },
        {
          id: 4,
          patient: {
            id: 104,
            name: 'سارة عبدالله'
          },
          type: 'جلسة فردية',
          date: '2024-01-17',
          time: '15:00',
          duration: 45
        }
      ],
      previousSessions: [
        {
          id: 5,
          patient: {
            id: 101,
            name: 'أحمد محمد'
          },
          type: 'جلسة تقييم',
          date: '2024-01-08',
          rating: 4
        },
        {
          id: 6,
          patient: {
            id: 102,
            name: 'فاطمة أحمد'
          },
          type: 'جلسة فردية',
          date: '2024-01-01',
          rating: 5
        }
      ],
      activePatients: [
        {
          id: 101,
          name: 'أحمد محمد',
          age: 32,
          sessionsCount: 12,
          lastSession: 'أمس',
          progress: 75
        },
        {
          id: 102,
          name: 'فاطمة أحمد',
          age: 28,
          sessionsCount: 8,
          lastSession: '3 أيام',
          progress: 60
        },
        {
          id: 103,
          name: 'محمد خالد',
          age: 35,
          sessionsCount: 5,
          lastSession: 'أسبوع',
          progress: 40
        },
        {
          id: 104,
          name: 'سارة عبدالله',
          age: 29,
          sessionsCount: 3,
          lastSession: 'أسبوعين',
          progress: 25
        },
        {
          id: 105,
          name: 'خالد سعيد',
          age: 41,
          sessionsCount: 15,
          lastSession: 'يومين',
          progress: 80
        },
        {
          id: 106,
          name: 'نورة محمد',
          age: 26,
          sessionsCount: 6,
          lastSession: '4 أيام',
          progress: 45
        }
      ]
    }
  },
  methods: {
    startSession(session) {
      console.log('Starting session:', session.id)
      // بدء الجلسة - يمكن إضافة منطق الانتقال لجلسة الفيديو
    },
    
    viewSessionNotes(session) {
      this.$router.push(`/therapist/session-notes/${session.id}`)
    },
    
    viewSessionDetails(session) {
      this.$router.push(`/therapist/sessions/${session.id}`)
    },
    
    viewPatientProfile(patient) {
      this.$router.push(`/therapist/patients/${patient.id}`)
    },
    
    contactSupport(method) {
      console.log('Contacting support via:', method)
      this.showSupportModal = false
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('ar-SA')
    },
    
    formatDay(dateString) {
      const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت']
      return days[new Date(dateString).getDay()]
    },
    
    formatDateNumber(dateString) {
      return new Date(dateString).getDate()
    },
    
    getStatusText(status) {
      const statusMap = {
        'active': 'نشطة الآن',
        'upcoming': 'قيد الانتظار'
      }
      return statusMap[status]
    },
    
    getTimeRemaining(session) {
      return '2 ساعة 15 دقيقة'
    }
  }
}
</script>

<style>
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

.bg-brand-500 { background-color: var(--brand-500); }
.hover\:bg-brand-600:hover { background-color: var(--brand-600); }
.text-brand-500 { color: var(--brand-500); }
.border-brand-500 { border-color: var(--brand-500); }
.bg-brand-50 { background-color: var(--brand-50); }
.text-brand-700 { color: var(--brand-700); }

.bg-accent-50 { background-color: var(--accent-50); }
.text-accent-700 { color: var(--accent-700); }
.border-accent-200 { border-color: var(--accent-200); }

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
</style>