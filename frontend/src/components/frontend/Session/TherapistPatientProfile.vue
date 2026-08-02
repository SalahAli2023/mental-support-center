<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
      <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3">
        <div class="flex justify-between items-center">
          <div class="flex items-center space-x-2 sm:space-x-4 space-x-reverse">
            <button @click="$router.go(-1)" class="text-gray-600 hover:text-gray-800 p-1 sm:p-0">
              <i class="fas fa-arrow-right text-sm sm:text-base ml-1 sm:ml-2"></i>
              <span class="text-xs sm:text-sm mr-1">رجوع</span>
            </button>
            <h1 class="text-base sm:text-lg font-semibold text-gray-900">ملف المريض</h1>
          </div>
          <button 
            v-if="upcomingSession" 
            @click="startSession"
            class="bg-brand-500 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-lg hover:bg-brand-600 text-xs sm:text-sm transition-colors flex items-center space-x-1 sm:space-x-2 space-x-reverse"
          >
            <i class="fas fa-play text-xs sm:text-sm"></i>
            <span>بدء الجلسة</span>
          </button>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-3 sm:px-4 pt-16 sm:pt-20 pb-4 sm:pb-6">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Sidebar - Fixed on left -->
        <div class="lg:col-span-1">
          <div class="sticky top-20 sm:top-24 space-y-4">
            <!-- Patient Profile -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4">
              <div class="text-center mb-3 sm:mb-4">
                <div class="avatar-container large mb-3 sm:mb-4">
                  <div class="default-avatar patient large">
                    <i class="fas fa-user"></i>
                  </div>
                </div>
                <h2 class="font-semibold text-gray-900 text-sm sm:text-base mb-1 sm:mb-2">{{ patient.name }}</h2>
                <p class="text-gray-500 text-xs sm:text-sm">{{ patient.age }} سنة - {{ patient.gender }}</p>
              </div>
              
              <!-- Profile Stats -->
              <div class="grid grid-cols-2 gap-2 sm:gap-3 mb-3 sm:mb-4">
                <div class="text-center p-2 sm:p-3 bg-gray-50 rounded-lg">
                  <p class="text-lg sm:text-xl font-bold text-brand-500 mb-1">{{ patient.totalSessions }}</p>
                  <p class="text-gray-600 text-xs">الجلسات</p>
                </div>
                <div class="text-center p-2 sm:p-3 bg-gray-50 rounded-lg">
                  <p class="text-lg sm:text-xl font-bold text-brand-500 mb-1">{{ patient.attendanceRate }}%</p>
                  <p class="text-gray-600 text-xs">الحضور</p>
                </div>
              </div>

              <!-- Progress -->
              <div class="mb-3 sm:mb-4">
                <div class="flex justify-between items-center mb-1 sm:mb-2">
                  <span class="text-xs sm:text-sm font-medium text-gray-700">تقدم العلاج</span>
                  <span class="text-xs sm:text-sm text-brand-500">{{ patient.progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2">
                  <div 
                    class="bg-brand-500 h-1.5 sm:h-2 rounded-full transition-all duration-300"
                    :style="{ width: patient.progress + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Special Notes -->
              <div class="border-t border-gray-200 pt-3 sm:pt-4">
                <h4 class="font-medium text-gray-900 text-sm sm:text-base mb-1 sm:mb-2">ملاحظات خاصة</h4>
                <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">{{ patient.specialNotes }}</p>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-2">
              <button @click="showNotesModal = true" class="w-full bg-white border border-gray-200 rounded-lg p-2 sm:p-3 text-right hover:bg-gray-50 text-xs sm:text-sm transition-colors flex items-center justify-between">
                <span class="flex items-center">
                  <i class="fas fa-edit ml-2 sm:ml-3 text-brand-500 text-xs sm:text-sm"></i>
                  <span class="text-xs sm:text-sm">ملاحظات الجلسة</span>
                </span>
                <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
              </button>
              <button @click="showAssessmentModal = true" class="w-full bg-white border border-gray-200 rounded-lg p-2 sm:p-3 text-right hover:bg-gray-50 text-xs sm:text-sm transition-colors flex items-center justify-between">
                <span class="flex items-center">
                  <i class="fas fa-clipboard-check ml-2 sm:ml-3 text-brand-500 text-xs sm:text-sm"></i>
                  <span class="text-xs sm:text-sm">تقييم المريض</span>
                </span>
                <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
              </button>
              <button @click="showProgressModal = true" class="w-full bg-white border border-gray-200 rounded-lg p-2 sm:p-3 text-right hover:bg-gray-50 text-xs sm:text-sm transition-colors flex items-center justify-between">
                <span class="flex items-center">
                  <i class="fas fa-chart-line ml-2 sm:ml-3 text-brand-500 text-xs sm:text-sm"></i>
                  <span class="text-xs sm:text-sm">التقدم الكامل</span>
                </span>
                <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <main class="lg:col-span-3 space-y-4 sm:space-y-6">
          <!-- Upcoming Session -->
          <div v-if="upcomingSession" class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-center mb-4 sm:mb-6">
              <h3 class="font-semibold text-gray-900 text-base sm:text-lg flex items-center">
                <i class="fas fa-calendar-alt ml-2 sm:ml-3 text-brand-500 text-sm sm:text-base"></i>
                <span class="text-sm sm:text-base">الجلسة القادمة</span>
              </h3>
              <span class="bg-brand-50 text-brand-700 px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs font-medium">نشطة الآن</span>
            </div>
            
            <div class="flex flex-col gap-3 sm:gap-4">
              <div class="flex flex-wrap items-center gap-3 sm:gap-6 text-xs sm:text-sm">
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-calendar ml-1 sm:ml-2 text-brand-500 text-xs sm:text-sm"></i>
                  <span class="mr-1 sm:mr-2">{{ formatDate(upcomingSession.date) }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-clock ml-1 sm:ml-2 text-brand-500 text-xs sm:text-sm"></i>
                  <span class="mr-1 sm:mr-2">{{ upcomingSession.time }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-stopwatch ml-1 sm:ml-2 text-brand-500 text-xs sm:text-sm"></i>
                  <span class="mr-1 sm:mr-2">{{ upcomingSession.duration }} دقيقة</span>
                </div>
              </div>
              
              <div class="flex flex-wrap gap-2 sm:gap-3">
                <button @click="startSession" class="bg-brand-500 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg hover:bg-brand-600 transition-colors flex items-center space-x-1 sm:space-x-2 space-x-reverse text-xs sm:text-sm">
                  <i class="fas fa-play text-xs sm:text-sm"></i>
                  <span>بدء الجلسة</span>
                </button>
                <button @click="rescheduleSession" class="bg-gray-100 text-gray-700 px-3 py-2 sm:px-4 sm:py-3 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-1 sm:space-x-2 space-x-reverse text-xs sm:text-sm">
                  <i class="fas fa-edit text-xs sm:text-sm"></i>
                  <span>إعادة جدولة</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Sessions History -->
          <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 sm:p-6 border-b border-gray-200">
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-0">
                <h3 class="font-semibold text-gray-900 text-base sm:text-lg flex items-center">
                  <i class="fas fa-history ml-2 sm:ml-3 text-gray-600 text-sm sm:text-base"></i>
                  <span class="text-sm sm:text-base">سجل الجلسات</span>
                </h3>
                <select v-model="sessionFilter" class="border border-gray-300 rounded-lg px-2 py-1 sm:px-3 sm:py-2 text-xs sm:text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                  <option value="all">الكل</option>
                  <option value="completed">المكتملة</option>
                  <option value="cancelled">الملغاة</option>
                </select>
              </div>
            </div>
            
            <div class="p-4 sm:p-6">
              <div v-if="filteredSessions.length === 0" class="text-center py-6 sm:py-8 text-gray-500">
                <div class="default-avatar empty-state mb-3 sm:mb-4 mx-auto">
                  <i class="fas fa-calendar-times"></i>
                </div>
                <p class="text-sm sm:text-base">لا توجد جلسات</p>
              </div>
              
              <div v-else class="space-y-3 sm:space-y-4">
                <div 
                  v-for="session in filteredSessions" 
                  :key="session.id"
                  class="border border-gray-200 rounded-lg p-3 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 hover:shadow-md transition-all"
                >
                  <div class="flex items-center gap-3 sm:gap-4 space-x-reverse">
                    <div class="text-center bg-gray-50 rounded-lg p-2 sm:p-4 min-w-16 sm:min-w-20">
                      <p class="font-semibold text-gray-900 text-base sm:text-lg">{{ formatDateNumber(session.date) }}</p>
                      <p class="text-gray-600 text-xs">{{ formatDay(session.date) }}</p>
                    </div>
                    
                    <div class="flex-1">
                      <p class="font-medium text-gray-900 text-sm sm:text-lg mb-1 sm:mb-2">{{ session.type }}</p>
                      <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-600">
                        <span>{{ session.time }}</span>
                        <span class="text-gray-300 hidden sm:inline">•</span>
                        <span>{{ session.duration }} دقيقة</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center justify-between sm:justify-normal gap-2 sm:gap-3 space-x-reverse">
                    <span class="text-xs px-2 py-1 sm:px-3 sm:py-1 rounded-full font-medium"
                          :class="{
                            'bg-green-100 text-green-800': session.status === 'completed',
                            'bg-gray-100 text-gray-800': session.status === 'cancelled',
                            'bg-brand-50 text-brand-700': session.status === 'active'
                          }">
                      {{ getStatusText(session.status) }}
                    </span>
                    
                    <div class="flex gap-1 sm:gap-2 space-x-reverse">
                      <button @click="viewSessionDetails(session)" class="text-gray-400 hover:text-gray-600 p-1 sm:p-2 rounded-lg hover:bg-gray-100 transition-colors" title="عرض التفاصيل">
                        <i class="fas fa-eye text-xs sm:text-sm"></i>
                      </button>
                      <button 
                        v-if="session.recordingAvailable"
                        @click="viewRecording(session)" 
                        class="text-gray-400 hover:text-brand-500 p-1 sm:p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        title="عرض التسجيل"
                      >
                        <i class="fas fa-play-circle text-xs sm:text-sm"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Patient Progress -->
          <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h3 class="font-semibold text-gray-900 text-base sm:text-lg mb-4 sm:mb-6 flex items-center">
              <i class="fas fa-chart-line ml-2 sm:ml-3 text-brand-500 text-sm sm:text-base"></i>
              <span class="text-sm sm:text-base">تقدم العلاج</span>
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
              <div class="border border-gray-200 rounded-lg p-3 sm:p-4">
                <div class="flex justify-between items-center mb-3 sm:mb-4">
                  <h4 class="font-medium text-gray-900 text-sm sm:text-base">مستوى القلق</h4>
                  <span class="bg-green-100 text-green-800 px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs font-medium flex items-center">
                    <i class="fas fa-arrow-down ml-1 text-xs"></i>
                    <span class="text-xs">تحسن 15%</span>
                  </span>
                </div>
                <div class="h-32 sm:h-40 bg-gray-50 rounded border border-gray-200 flex items-center justify-center text-gray-400">
                  <i class="fas fa-chart-bar text-xl sm:text-3xl"></i>
                </div>
              </div>
              
              <div class="border border-gray-200 rounded-lg p-3 sm:p-4">
                <div class="flex justify-between items-center mb-3 sm:mb-4">
                  <h4 class="font-medium text-gray-900 text-sm sm:text-base">مستوى الاكتئاب</h4>
                  <span class="bg-green-100 text-green-800 px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs font-medium flex items-center">
                    <i class="fas fa-arrow-down ml-1 text-xs"></i>
                    <span class="text-xs">تحسن 20%</span>
                  </span>
                </div>
                <div class="h-32 sm:h-40 bg-gray-50 rounded border border-gray-200 flex items-center justify-center text-gray-400">
                  <i class="fas fa-chart-line text-xl sm:text-3xl"></i>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- Session Notes Modal -->
    <div v-if="showNotesModal" class="fixed inset-0 bg-white/20 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl border border-gray-200 shadow-xl mx-2 sm:mx-0">
        <div class="p-3 sm:p-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="font-semibold text-gray-900 text-sm sm:text-base">ملاحظات الجلسة</h3>
          <button @click="showNotesModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
            <i class="fas fa-times text-sm sm:text-base"></i>
          </button>
        </div>
        
        <div class="p-4 sm:p-6">
          <textarea 
            v-model="sessionNotes"
            placeholder="اكتب ملاحظاتك عن الجلسة..."
            class="w-full border border-gray-300 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6 h-40 sm:h-48 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm sm:text-base"
          ></textarea>
          
          <div class="flex justify-end gap-2 sm:gap-3 space-x-reverse">
            <button @click="showNotesModal = false" class="px-4 py-2 sm:px-6 sm:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-xs sm:text-sm">
              إلغاء
            </button>
            <button @click="saveSessionNotes" class="px-4 py-2 sm:px-6 sm:py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-xs sm:text-sm">
              حفظ الملاحظات
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Assessment Modal -->
    <div v-if="showAssessmentModal" class="fixed inset-0 bg-white/20 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl border border-gray-200 shadow-xl mx-2 sm:mx-0">
        <div class="p-3 sm:p-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="font-semibold text-gray-900 text-sm sm:text-base">تقييم المريض</h3>
          <button @click="showAssessmentModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
            <i class="fas fa-times text-sm sm:text-base"></i>
          </button>
        </div>
        
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3 sm:mb-4 text-xs sm:text-sm">مستوى التقدم</label>
            <div class="flex gap-1 sm:gap-2 space-x-reverse justify-center">
              <i 
                v-for="star in 5" 
                :key="star"
                class="fas fa-star text-xl sm:text-2xl cursor-pointer transition-transform hover:scale-110"
                :class="star <= assessment.rating ? 'text-yellow-400' : 'text-gray-300'"
                @click="assessment.rating = star"
              ></i>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 text-xs sm:text-sm">ملاحظات التقييم</label>
            <textarea 
              v-model="assessment.notes"
              class="w-full border border-gray-300 rounded-lg p-3 sm:p-4 h-28 sm:h-32 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors text-sm sm:text-base"
              placeholder="اكتب ملاحظاتك الإضافية عن تقدم المريض..."
            ></textarea>
          </div>
          
          <div class="flex justify-end gap-2 sm:gap-3 space-x-reverse">
            <button @click="showAssessmentModal = false" class="px-4 py-2 sm:px-6 sm:py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-xs sm:text-sm">
              إلغاء
            </button>
            <button @click="saveAssessment" class="px-4 py-2 sm:px-6 sm:py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-xs sm:text-sm">
              حفظ التقييم
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Progress Modal -->
    <div v-if="showProgressModal" class="fixed inset-0 bg-white/20 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-4xl border border-gray-200 shadow-xl mx-2 sm:mx-0 max-h-[90vh] overflow-y-auto">
        <div class="p-3 sm:p-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="font-semibold text-gray-900 text-sm sm:text-base">التقدم الكامل في العلاج</h3>
          <button @click="showProgressModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
            <i class="fas fa-times text-sm sm:text-base"></i>
          </button>
        </div>
        
        <div class="p-4 sm:p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
            <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
              <h4 class="font-medium text-gray-900 mb-2 sm:mb-3 text-sm sm:text-base">مستوى القلق</h4>
              <div class="h-32 sm:h-40 bg-white rounded border border-gray-300 flex items-center justify-center text-gray-400">
                <i class="fas fa-chart-bar text-2xl sm:text-4xl"></i>
              </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
              <h4 class="font-medium text-gray-900 mb-2 sm:mb-3 text-sm sm:text-base">مستوى الاكتئاب</h4>
              <div class="h-32 sm:h-40 bg-white rounded border border-gray-300 flex items-center justify-center text-gray-400">
                <i class="fas fa-chart-line text-2xl sm:text-4xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
            <h4 class="font-medium text-gray-900 mb-2 sm:mb-3 text-sm sm:text-base">ملخص التقدم</h4>
            <div class="space-y-1 sm:space-y-2 text-xs sm:text-sm text-gray-600">
              <p>• تحسن ملحوظ في إدارة القلق بنسبة 40%</p>
              <p>• انخفاض في أعراض الاكتئاب بنسبة 25%</p>
              <p>• زيادة في معدل الحضور للجلسات</p>
              <p>• تحسن في جودة النوم والحياة اليومية</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TherapistPatientView',
  data() {
    return {
      showNotesModal: false,
      showAssessmentModal: false,
      showProgressModal: false,
      sessionFilter: 'all',
      sessionNotes: '',
      assessment: {
        rating: 0,
        notes: ''
      },
      patient: {
        name: 'أحمد محمد',
        age: 32,
        gender: 'ذكر',
        totalSessions: 12,
        attendanceRate: 92,
        progress: 65,
        specialNotes: 'المريض يستجيب جيدًا للعلاج المعرفي السلوكي ويظهر تحسنًا ملحوظًا في إدارة القلق.'
      },
      upcomingSession: {
        id: 1,
        date: '2024-01-15',
        time: '14:00',
        duration: 45,
        type: 'جلسة فردية',
        status: 'active'
      },
      sessionsHistory: [
        {
          id: 2,
          date: '2024-01-08',
          time: '14:00',
          duration: 45,
          type: 'جلسة فردية',
          status: 'completed',
          recordingAvailable: true
        },
        {
          id: 3,
          date: '2024-01-01',
          time: '14:00',
          duration: 45,
          type: 'جلسة جماعية',
          status: 'completed',
          recordingAvailable: false
        },
        {
          id: 4,
          date: '2023-12-25',
          time: '15:00',
          duration: 60,
          type: 'جلسة تقييم',
          status: 'completed',
          recordingAvailable: true
        }
      ]
    }
  },
  computed: {
    filteredSessions() {
      if (this.sessionFilter === 'all') {
        return this.sessionsHistory
      }
      return this.sessionsHistory.filter(session => session.status === this.sessionFilter)
    }
  },
  methods: {
    startSession() {
      // بدء جلسة Zoom
      console.log('Starting session...')
    },
    
    rescheduleSession() {
      console.log('Reschedule session')
    },
    
    viewSessionDetails(session) {
      console.log('View session details', session.id)
    },
    
    viewRecording(session) {
      console.log('View recording', session.id)
    },
    
    saveSessionNotes() {
      this.showNotesModal = false
      this.sessionNotes = ''
    },
    
    saveAssessment() {
      this.showAssessmentModal = false
      this.assessment = { rating: 0, notes: '' }
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
        'completed': 'مكتملة',
        'active': 'نشطة',
        'cancelled': 'ملغاة'
      }
      return statusMap[status]
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

.default-avatar.large {
  width: 60px;
  height: 60px;
  font-size: 1.5rem;
}

@media (min-width: 640px) {
  .default-avatar.large {
    width: 80px;
    height: 80px;
    font-size: 2rem;
  }
}

.default-avatar:not(.large) {
  width: 32px;
  height: 32px;
  font-size: 1rem;
}

@media (min-width: 640px) {
  .default-avatar:not(.large) {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }
}

.default-avatar.empty-state {
  width: 60px;
  height: 60px;
  font-size: 2rem;
  background: #e5e7eb;
  color: #9ca3af;
  margin: 0 auto 1rem;
}

@media (min-width: 640px) {
  .default-avatar.empty-state {
    width: 80px;
    height: 80px;
    font-size: 2.5rem;
  }
}

.avatar-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-container.large .default-avatar {
  width: 60px;
  height: 60px;
  font-size: 1.5rem;
}

@media (min-width: 640px) {
  .avatar-container.large .default-avatar {
    width: 80px;
    height: 80px;
    font-size: 2rem;
  }
}

/* Improved mobile responsiveness */
@media (max-width: 640px) {
  .space-x-reverse > :not([hidden]) ~ :not([hidden]) {
    --tw-space-x-reverse: 1;
  }
  
  .flex-wrap {
    flex-wrap: wrap;
  }
}

/* Better touch targets for mobile */
@media (max-width: 640px) {
  button, select {
    min-height: 44px;
    min-width: 44px;
  }
  
  .cursor-pointer {
    cursor: pointer;
  }
}

/* Smooth scrolling */
html {
  scroll-behavior: smooth;
}

/* Ensure sidebar doesn't overlap with header */
.sticky {
  position: sticky;
  z-index: 30;
}
</style>