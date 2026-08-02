<template>
  <div :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'" :lang="currentLanguage">
    <!-- Header مع زر تغيير اللغة -->
    <Header>
      <template #extra>
        <button 
          @click="toggleLanguage"
          class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-primary-green to-secondary-green hover:from-green-600 hover:to-emerald-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
          :aria-label="currentLanguage === 'ar' ? 'Switch to English' : 'التبديل إلى العربية'"
        >
          <i class="fas fa-globe text-white"></i>
          <span class="text-white font-semibold">
            {{ currentLanguage === 'ar' ? 'English' : 'العربية' }}
          </span>
        </button>
      </template>
    </Header>

    <!-- Hero Section -->
    <Hero
      :title="translate('programs.hero.title')"
      :highlight="translate('programs.hero.highlight')"
      :subtitle="translate('programs.hero.subtitle')"
      :button="[
        { text: translate('button.startJourney'), icon: 'fas fa-play-circle', primary: true, action: startJourney },
        { text: translate('button.learnMore'), icon: 'fas fa-info-circle', primary: false, action: scrollToPrograms }
      ]"
    />

    <!-- Programs Section -->
    <section class="py-16 bg-gradient-to-b from-gray-50 to-white" id="programs" ref="programsSection">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->

        <!-- Filters -->
      <!-- القسم المعدل: Filters & Search Section -->
<!-- أضف هذا بدلاً من قسم "Filters and Sort" الحالي -->
<div class="mb-8 bg-white rounded-xl p-4 shadow-sm border border-gray-200">
  <!-- Mobile View: Vertical Layout -->
  <div class="lg:hidden space-y-4">
    <!-- Search Box -->
    <div class="relative w-full">
      <i class="fas fa-search absolute top-1/2 transform -translate-y-1/2" 
         :class="currentLanguage === 'ar' ? 'right-3' : 'left-3'"></i>
      <input 
        type="text" 
        v-model="searchQuery"
        :placeholder="getPlaceholder()"
        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent outline-none text-sm"
        :class="{ 'text-right pr-10 pl-4': currentLanguage === 'ar' }"
        @keyup.enter="searchPrograms"
      />
    </div>

    <!-- Filters Row -->
    <div class="flex flex-wrap gap-2 justify-center">
      <button 
        v-for="filter in filters"
        :key="filter.id"
        @click="setActiveFilter(filter.id)"
        class="px-3 py-2 rounded-lg text-xs font-medium transition-all flex items-center gap-1"
        :class="activeFilter === filter.id 
          ? 'bg-gradient-to-r from-primary-green to-secondary-green text-white shadow-md' 
          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
      >
        <i :class="filter.icon" class="text-xs"></i>
        <span>{{ getFilterLabel(filter.id) }}</span>
        <span v-if="filter.id === 'ongoing'" class="bg-white/20 px-1 py-0.5 rounded-full text-[10px] ml-1">
          {{ ongoingCount }}
        </span>
        <span v-if="filter.id === 'upcoming'" class="bg-white/20 px-1 py-0.5 rounded-full text-[10px] ml-1">
          {{ upcomingCount }}
        </span>
        <span v-if="filter.id === 'enrolled'" class="bg-white/20 px-1 py-0.5 rounded-full text-[10px] ml-1">
          {{ enrolledCount }}
        </span>
      </button>
    </div>

    <!-- Sort Dropdown -->
    <div class="relative w-full">
      <select 
        v-model="sortBy"
        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent outline-none appearance-none bg-white text-sm"
        :class="{ 'text-right pr-10 pl-4': currentLanguage === 'ar' }"
      >
        <option value="newest">{{ getSortLabel('newest') }}</option>
        <option value="oldest">{{ getSortLabel('oldest') }}</option>
        <option value="popular">{{ getSortLabel('popular') }}</option>
        <option value="name_asc">{{ getSortLabel('name_asc') }}</option>
        <option value="name_desc">{{ getSortLabel('name_desc') }}</option>
      </select>
      <i class="fas fa-sort absolute top-1/2 transform -translate-y-1/2" 
         :class="currentLanguage === 'ar' ? 'right-3' : 'left-3'"></i>
    </div>
  </div>

  <!-- Desktop View: Horizontal Layout -->
  <div class="hidden lg:flex items-center justify-between" :class="{ 'flex-row-reverse': currentLanguage === 'ar' }">
    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-center">
      <button 
        v-for="filter in filters"
        :key="filter.id"
        @click="setActiveFilter(filter.id)"
        class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2"
        :class="activeFilter === filter.id 
          ? 'bg-gradient-to-r from-primary-green to-secondary-green text-white shadow-md' 
          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
      >
        <i :class="filter.icon"></i>
        <span>{{ getFilterLabel(filter.id) }}</span>
        <span v-if="filter.id === 'ongoing'" class="bg-white/20 px-2 py-0.5 rounded-full text-xs">
          {{ ongoingCount }}
        </span>
        <span v-if="filter.id === 'upcoming'" class="bg-white/20 px-2 py-0.5 rounded-full text-xs">
          {{ upcomingCount }}
        </span>
        <span v-if="filter.id === 'enrolled'" class="bg-white/20 px-2 py-0.5 rounded-full text-xs">
          {{ enrolledCount }}
        </span>
      </button>
    </div>
    
    <!-- Search and Sort -->
    <div class="flex items-center gap-3">
      <!-- Sort -->
      <div class="relative">
        <select 
          v-model="sortBy"
          class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent outline-none appearance-none bg-white"
          :class="{ 'text-right pr-10 pl-4': currentLanguage === 'ar' }"
        >
          <option value="newest">{{ getSortLabel('newest') }}</option>
          <option value="oldest">{{ getSortLabel('oldest') }}</option>
          <option value="popular">{{ getSortLabel('popular') }}</option>
          <option value="name_asc">{{ getSortLabel('name_asc') }}</option>
          <option value="name_desc">{{ getSortLabel('name_desc') }}</option>
        </select>
        <i class="fas fa-sort absolute top-1/2 transform -translate-y-1/2" :class="currentLanguage === 'ar' ? 'right-3' : 'left-3'"></i>
      </div>
      
      <!-- Search -->
      <div class="relative">
        <i class="fas fa-search absolute top-1/2 transform -translate-y-1/2" :class="currentLanguage === 'ar' ? 'right-3' : 'left-3'"></i>
        <input 
          type="text" 
          v-model="searchQuery"
          :placeholder="getPlaceholder()"
          class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent outline-none w-64"
          :class="{ 'text-right pr-10 pl-4': currentLanguage === 'ar' }"
          @keyup.enter="searchPrograms"
        />
      </div>
    </div>
  </div>
</div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="n in 6" :key="n" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-pulse">
            <div class="h-48 bg-gradient-to-r from-gray-200 to-gray-300"></div>
            <div class="p-6">
              <div class="h-6 bg-gray-200 rounded w-3/4 mb-3"></div>
              <div class="space-y-2 mb-4">
                <div class="h-3 bg-gray-200 rounded w-full"></div>
                <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                <div class="h-3 bg-gray-200 rounded w-4/6"></div>
              </div>
              <div class="h-10 bg-gray-200 rounded"></div>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-16">
          <div class="w-32 h-32 mx-auto mb-6">
            <i class="fas fa-exclamation-circle text-8xl text-red-500"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-3">
            {{ getErrorMessage('oops') }}
          </h3>
          <p class="text-gray-600 text-lg mb-6 max-w-md mx-auto">
            {{ error }}
          </p>
          <button 
            @click="loadPrograms"
            class="px-8 py-3 bg-gradient-to-r from-primary-green to-secondary-green text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300"
          >
            <i class="fas fa-redo-alt mr-2"></i>
            {{ getButtonLabel('retry') }}
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredPrograms.length === 0" class="text-center py-16">
          <div class="w-40 h-40 mx-auto mb-6 text-gray-300">
            <i class="fas fa-calendar-alt text-9xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-3">
            {{ getProgramText('noPrograms.title') }}
          </h3>
          <p class="text-gray-600 text-lg mb-6 max-w-md mx-auto">
            {{ getProgramText('noPrograms.subtitle') }}
          </p>
          <button 
            @click="resetFilters"
            class="px-6 py-3 border-2 border-primary-green text-primary-green font-semibold rounded-xl hover:bg-primary-green hover:text-white transition-all"
          >
            <i class="fas fa-filter mr-2"></i>
            {{ getButtonLabel('resetFilters') }}
          </button>
        </div>

        <!-- Programs Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="program in paginatedPrograms"
            :key="program.id"
            class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-500 overflow-hidden hover:-translate-y-2"
          >
            <!-- Program Header with Image -->
            <div 
              class="relative h-48 overflow-hidden cursor-pointer bg-gradient-to-br from-gray-100 to-gray-200"
              @click="viewProgramDetails(program)"
            >
              <!-- Program Image -->
              <div v-if="program.image_url" class="absolute inset-0">
                <img 
                  :src="getFullImageUrl(program.image_url)" 
                  :alt="getProgramTitle(program)"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                  @load="handleImageLoad(program)"
                  @error="handleImageError($event, program)"
                  loading="lazy"
                />
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-60"></div>
              </div>
              
              <!-- Fallback Image if no image_url -->
              <div v-else class="absolute inset-0 bg-gradient-to-br from-primary-green/20 to-secondary-green/20 flex items-center justify-center">
                <i :class="[getProgramIcon(program.id), 'text-5xl text-primary-green/50']"></i>
              </div>
              
              <!-- Program Icon -->
              <div class="absolute bottom-4 left-6 transform group-hover:scale-110 transition-transform duration-300">
                <i :class="[getProgramIcon(program.id), 'text-3xl text-white drop-shadow-lg']"></i>
              </div>
              
              <!-- Program Badge -->
              <div class="absolute top-4 right-4">
                <span class="px-3 py-1.5 bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-bold rounded-full shadow-sm">
                  {{ getProgramBadge(program) }}
                </span>
              </div>
              
              <!-- Overlay on Hover -->
              <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <span class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-semibold transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                  {{ getButtonLabel('viewDetails') }}
                </span>
              </div>
              
              <!-- Quick Actions -->
              <div class="absolute top-4 left-4 flex gap-2">
                <button 
                  v-if="program.is_enrolled"
                  @click.stop="continueProgram(program)"
                  class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-colors shadow-sm"
                  :title="getButtonLabel('continue')"
                >
                  <i class="fas fa-play text-primary-green text-sm"></i>
                </button>
                <button 
                  v-if="!program.is_enrolled && canEnroll(program)"
                  @click.stop="quickEnroll(program)"
                  class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-colors shadow-sm"
                  :title="getButtonLabel('enroll')"
                >
                  <i class="fas fa-plus text-primary-green text-sm"></i>
                </button>
              </div>
            </div>
            
            <!-- Program Content -->
            <div class="p-6">
              <!-- Title and Description -->
              <div @click="viewProgramDetails(program)" class="cursor-pointer">
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-green transition-colors line-clamp-1">
                  {{ getProgramTitle(program) }}
                </h3>
                <div class="flex items-center gap-2 mb-3">
                  <span v-if="program.scale" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                    {{ getProgramScale(program) }}
                  </span>
                  <span v-if="program.sessions_count" class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-full">
                    {{ program.sessions_count }} {{ getProgramText('sessions') }}
                  </span>
                </div>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed line-clamp-3 min-h-[4rem]">
                  {{ getProgramDescription(program) }}
                </p>
              </div>
              
              <!-- Program Details -->
              <div class="space-y-3 mb-6">
                <!-- Duration -->
                <div class="flex items-center text-gray-500 text-sm">
                  <i class="fas fa-clock mr-3 text-primary-green"></i>
                  <span class="font-medium">{{ getProgramText('duration') }}:</span>
                  <span class="mr-2" v-if="currentLanguage === 'ar'">:</span>
                  <span class="font-semibold text-gray-900 mr-auto">{{ getDurationText(program) }}</span>
                </div>
                
                <!-- Sessions Count -->
                <div class="flex items-center text-gray-500 text-sm">
                  <i class="fas fa-layer-group mr-3 text-primary-green"></i>
                  <span class="font-medium">{{ getProgramText('sessions') }}:</span>
                  <span class="mr-2" v-if="currentLanguage === 'ar'">:</span>
                  <span class="font-semibold text-gray-900 mr-auto">
                    {{ getTotalSessions(program) }}
                  </span>
                </div>

                <!-- Session Duration -->
                <div v-if="program.session_duration_minutes" class="flex items-center text-gray-500 text-sm">
                  <i class="fas fa-hourglass mr-3 text-primary-green"></i>
                  <span class="font-medium">{{ getProgramText('sessionDuration') }}:</span>
                  <span class="mr-2" v-if="currentLanguage === 'ar'">:</span>
                  <span class="font-semibold text-gray-900 mr-auto">
                    {{ program.session_duration_minutes }} {{ getProgramText('minutes') }}
                  </span>
                </div>
              </div>
              
              <!-- Progress Bar (if user is enrolled) -->
              <div v-if="program.user_progress" class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-700 font-medium">{{ getProgramText('progress') }}</span>
                  <span class="font-bold text-primary-green">
                    {{ program.user_progress.percentage }}%
                  </span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div 
                    class="h-full rounded-full transition-all duration-500 bg-gradient-to-r from-primary-green to-secondary-green"
                    :style="{ width: `${program.user_progress.percentage}%` }"
                  ></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ program.user_progress.completed_sessions }}/{{ program.user_progress.total_sessions }} {{ getProgramText('sessions') }}
                </div>
              </div>
              
              <!-- Action button -->
              <div class="flex gap-3">
                <button 
                  @click="viewProgramDetails(program)"
                  class="flex-1 px-4 py-3 rounded-xl text-white font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 shadow-md text-sm flex items-center justify-center gap-2 bg-gradient-to-r from-primary-green to-secondary-green"
                >
                  <i class="fas fa-eye"></i>
                  {{ getButtonLabel('viewDetails') }}
                </button>
                
                <button 
                  v-if="!program.is_enrolled && canEnroll(program)"
                  @click="enrollProgram(program)"
                  class="px-4 py-3 border-2 border-primary-green text-primary-green rounded-xl hover:bg-primary-green hover:text-white transition-all duration-300 flex items-center justify-center gap-2"
                  :title="getButtonLabel('enroll')"
                >
                  <i class="fas fa-user-plus"></i>
                  <span class="hidden sm:inline">{{ getButtonLabel('enroll') }}</span>
                </button>
                
                <button 
                  v-if="program.is_enrolled"
                  @click="continueProgram(program)"
                  class="px-4 py-3 border-2 border-emerald-500 text-emerald-500 rounded-xl hover:bg-emerald-500 hover:text-white transition-all duration-300 flex items-center justify-center gap-2"
                  :title="getButtonLabel('continue')"
                >
                  <i class="fas fa-play"></i>
                  <span class="hidden sm:inline">{{ getButtonLabel('continue') }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.total > pagination.per_page" class="mt-12 flex justify-center">
          <div class="flex items-center gap-2 bg-white rounded-xl p-2 shadow-sm border border-gray-200">
            <button 
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="w-10 h-10 flex items-center justify-center rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors"
              :title="getButtonLabel('previous')"
            >
              <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
            </button>
            
            <div class="flex items-center gap-1">
              <button 
                v-for="page in getPaginationPages()"
                :key="page"
                @click="changePage(page)"
                class="w-10 h-10 flex items-center justify-center rounded-lg transition-all font-medium"
                :class="page === pagination.current_page 
                  ? 'bg-primary-green text-white shadow-md' 
                  : 'hover:bg-gray-100 text-gray-700'"
              >
                {{ page }}
              </button>
            </div>
            
            <button 
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="w-10 h-10 flex items-center justify-center rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors"
              :title="getButtonLabel('next')"
            >
              <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"></i>
            </button>
          </div>
        </div>

        <!-- Quick Enrollment Modal -->
        <div v-if="showQuickEnrollModal" class="fixed inset-0 z-170 overflow-y-auto">
          <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-opacity"></div>
          <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
              <div class="p-6">
                <div class="text-center mb-6">
                  <i class="fas fa-user-plus text-5xl text-primary-green mb-4"></i>
                  <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ getProgramText('enroll.title') }}
                  </h3>
                  <p class="text-gray-600">
                    {{ getProgramText('enroll.message') }}
                  </p>
                </div>
                <div class="flex justify-center gap-3">
                  <button 
                    @click="confirmEnroll"
                    class="px-6 py-3 bg-gradient-to-r from-primary-green to-secondary-green text-white font-semibold rounded-xl hover:shadow-lg transition-all"
                  >
                    {{ getButtonLabel('confirm') }}
                  </button>
                  <button 
                    @click="showQuickEnrollModal = false"
                    class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all"
                  >
                    {{ getButtonLabel('cancel') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Free Services Banner -->
        <div class="mt-16 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6 lg:p-8 overflow-hidden">
          <div class="relative">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-emerald-100 rounded-full translate-y-12 -translate-x-12"></div>
            
            <div class="relative flex flex-col lg:flex-row items-center gap-6">
              <div class="w-20 h-20 bg-gradient-to-br from-primary-green to-secondary-green rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-gift text-white text-3xl"></i>
              </div>
              <div class="flex-1 text-center lg:text-start">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ getProgramText('freeServices.title') }}</h3>
                <p class="text-gray-600 text-lg">{{ getProgramText('freeServices.subtitle') }}</p>
              </div>
              <button class="px-8 py-3 bg-white text-primary-green font-semibold rounded-xl hover:bg-primary-green hover:text-white transition-all duration-300 shadow-md hover:shadow-lg">
                {{ getButtonLabel('learnMore') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />

    <!-- Program Details Modal -->
    <div v-if="selectedProgram" class="fixed inset-0 z-150 overflow-y-auto">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-opacity-80 backdrop-blur-sm transition-opacity"></div>
      
      <!-- Modal Container -->
      <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
          <!-- Modal Header -->
          <div class="sticky top-0 z-160 bg-white border-b border-gray-200 p-6">
            <div class="flex items-start justify-between">
              <div>
                <div class="flex items-center gap-3 mb-2">
                  <span class="px-3 py-1 rounded-full text-xs font-bold" :class="getBadgeClass(selectedProgram)">
                    {{ getProgramBadge(selectedProgram) }}
                  </span>
                  <span class="text-sm text-gray-500">
                    <i class="fas fa-users mr-1"></i>
                    {{ getTotalSessions(selectedProgram) }} {{ getProgramText('sessions') }}
                  </span>
                  <span v-if="selectedProgram.scale" class="text-sm text-gray-500">
                    <i class="fas fa-tag mr-1"></i>
                    {{ getProgramScale(selectedProgram) }}
                  </span>
                  <span v-if="selectedProgram.enrollment_count !== undefined" class="text-sm text-gray-500">
                    <i class="fas fa-user-plus mr-1"></i>
                    {{ selectedProgram.enrollment_count }} {{ currentLanguage === 'ar' ? 'مشترك' : 'enrolled' }}
                  </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">
                  {{ getProgramTitle(selectedProgram) }}
                </h3>
              </div>
              <button 
                @click="selectedProgram = null"
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors"
                :aria-label="getButtonLabel('close')"
              >
                <i class="fas fa-times text-xl text-gray-500"></i>
              </button>
            </div>
          </div>

          <!-- Modal Content -->
          <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
            <!-- Program Header with Image -->
            <div class="h-64 relative overflow-hidden">
              <div v-if="selectedProgram.image_url" class="absolute inset-0">
                <img 
                  :src="getFullImageUrl(selectedProgram.image_url)" 
                  :alt="getProgramTitle(selectedProgram)"
                  class="w-full h-full object-cover"
                  @load="handleImageLoad(selectedProgram)"
                  @error="handleImageError($event, selectedProgram)"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
              </div>
              <div v-else class="absolute inset-0 bg-gradient-to-br from-primary-green/20 to-secondary-green/20 flex items-center justify-center">
                <i :class="[getProgramIcon(selectedProgram.id), 'text-7xl text-primary-green/50']"></i>
              </div>
            </div>

            <!-- Program Info -->
            <div class="p-6">
              <!-- Description -->
              <div class="mb-8">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <i class="fas fa-info-circle text-primary-green"></i>
                  {{ getProgramText('description') }}
                </h4>
                <p class="text-gray-600 leading-relaxed whitespace-pre-line">
                  {{ getProgramDescription(selectedProgram) }}
                </p>
              </div>

              <!-- Details Grid -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- Duration -->
                <div class="bg-gray-50 p-4 rounded-xl">
                  <div class="flex items-center text-gray-600 mb-2">
                    <i class="fas fa-clock mr-3 text-primary-green"></i>
                    <span class="font-medium">{{ getProgramText('duration') }}</span>
                  </div>
                  <div class="font-bold text-lg text-gray-900">
                    {{ getDurationText(selectedProgram) }}
                  </div>
                </div>

                <!-- Sessions -->
                <div class="bg-gray-50 p-4 rounded-xl">
                  <div class="flex items-center text-gray-600 mb-2">
                    <i class="fas fa-layer-group mr-3 text-primary-green"></i>
                    <span class="font-medium">{{ getProgramText('sessions') }}</span>
                  </div>
                  <div class="font-bold text-lg text-gray-900">
                    {{ getTotalSessions(selectedProgram) }}
                  </div>
                </div>

                <!-- Session Duration -->
                <div v-if="selectedProgram.session_duration_minutes" class="bg-gray-50 p-4 rounded-xl">
                  <div class="flex items-center text-gray-600 mb-2">
                    <i class="fas fa-hourglass-half mr-3 text-primary-green"></i>
                    <span class="font-medium">{{ getProgramText('sessionDuration') }}</span>
                  </div>
                  <div class="font-bold text-lg text-gray-900">
                    {{ selectedProgram.session_duration_minutes }} {{ getProgramText('minutes') }}
                  </div>
                </div>

                <!-- Activity Gap -->
                <div v-if="selectedProgram.activity_gap_hours" class="bg-gray-50 p-4 rounded-xl">
                  <div class="flex items-center text-gray-600 mb-2">
                    <i class="fas fa-exchange-alt mr-3 text-primary-green"></i>
                    <span class="font-medium">{{ getProgramText('activityGap') }}</span>
                  </div>
                  <div class="font-bold text-lg text-gray-900">
                    {{ selectedProgram.activity_gap_hours }} {{ getProgramText('hours') }}
                  </div>
                </div>

                <!-- Session Gap -->
                <div v-if="selectedProgram.session_gap_hours" class="bg-gray-50 p-4 rounded-xl">
                  <div class="flex items-center text-gray-600 mb-2">
                    <i class="fas fa-calendar-alt mr-3 text-primary-green"></i>
                    <span class="font-medium">{{ getProgramText('sessionGap') }}</span>
                  </div>
                  <div class="font-bold text-lg text-gray-900">
                    {{ selectedProgram.session_gap_hours }} {{ getProgramText('hours') }}
                  </div>
                </div>

                <!-- Enrollment Count -->
                <div v-if="selectedProgram.enrollment_count !== undefined" class="bg-gray-50 p-4 rounded-xl">
                  <div class="flex items-center text-gray-600 mb-2">
                    <i class="fas fa-users mr-3 text-primary-green"></i>
                    <span class="font-medium">{{ getProgramText('enrolledCount') }}</span>
                  </div>
                  <div class="font-bold text-lg text-gray-900">
                    {{ selectedProgram.enrollment_count }}
                  </div>
                </div>
              </div>

              <!-- Progress (if enrolled) -->
              <div v-if="selectedProgram.user_progress" class="mb-8">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <i class="fas fa-chart-line text-primary-green"></i>
                  {{ getProgramText('progress') }}
                </h4>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl">
                  <div class="flex justify-between items-center mb-3">
                    <span class="font-bold text-gray-900">{{ selectedProgram.user_progress.percentage }}%</span>
                    <span class="text-sm text-gray-600">
                      {{ selectedProgram.user_progress.completed_sessions }}/{{ selectedProgram.user_progress.total_sessions }} {{ getProgramText('sessions') }}
                    </span>
                  </div>
                  <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                      class="h-full rounded-full transition-all duration-1000 bg-gradient-to-r from-primary-green to-secondary-green"
                      :style="{ width: `${selectedProgram.user_progress.percentage}%` }"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Sessions List -->
              <div v-if="programSessions.length > 0" class="mb-8">
                <div class="flex items-center justify-between mb-6">
                  <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-primary-green"></i>
                    {{ getProgramText('sessionsList') }}
                    <span class="text-sm font-normal text-gray-500">
                      ({{ programSessions.length }} {{ getProgramText('sessions') }})
                    </span>
                  </h4>
                  
                  <!-- Progress indicator -->
                  <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">
                      {{ completedSessionsCount }} / {{ programSessions.length }} {{ getProgramText('completed') }}
                    </span>
                  </div>
                </div>
                
                <!-- Loading sessions -->
                <div v-if="loadingSessions" class="space-y-4">
                  <div v-for="n in 3" :key="n" class="bg-gray-50 p-4 rounded-xl animate-pulse">
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                      <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Sessions list -->
                <div v-else class="space-y-4">
                  <div 
                    v-for="session in programSessions"
                    :key="session.id"
                    class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-all"
                    :class="{
                      'border-green-200': getSessionStatus(session) === 'completed',
                      'border-blue-200': getSessionStatus(session) === 'in-progress',
                      'border-gray-100 opacity-75': getSessionStatus(session) === 'locked'
                    }"
                  >
                    <!-- Session Header -->
                    <div 
                      class="p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                      @click="toggleSessionExpanded(session.id)"
                    >
                      <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3 flex-1">
                          <!-- Session Number and Info -->
                          <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                              <div class="w-10 h-10 flex items-center justify-center rounded-full" 
                                   :class="{
                                     'bg-green-100 text-green-800': getSessionStatus(session) === 'completed',
                                     'bg-blue-100 text-blue-800': getSessionStatus(session) === 'in-progress',
                                     'bg-gray-100 text-gray-800': getSessionStatus(session) === 'locked',
                                     'bg-primary-green/10 text-primary-green': getSessionStatus(session) === 'available'
                                   }">
                                <span class="font-bold">{{ session.order }}</span>
                              </div>
                              
                              <div class="flex-1">
                                <h5 class="font-bold text-gray-900">
                                  {{ getSessionTitle(session) }}
                                </h5>
                                <p class="text-sm text-gray-600 line-clamp-1">
                                  {{ getSessionDescription(session) }}
                                </p>
                              </div>
                            </div>
                            
                            <!-- Session Details -->
                            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                              <!-- Activities Count -->
                              <div class="flex items-center gap-1">
                                <i class="fas fa-tasks"></i>
                                <span>{{ session.activities_count || 0 }} {{ getProgramText('activities') }}</span>
                              </div>
                              
                              <!-- Progress -->
                              <div v-if="getSessionStatus(session) !== 'locked'" class="flex items-center gap-1">
                                <i class="fas fa-chart-line"></i>
                                <span>{{ getSessionProgress(session) }}% {{ getProgramText('complete') }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Session Status and Actions -->
                        <div class="flex flex-col items-end gap-2 ml-4">
                          <!-- Expand/Collapse Icon -->
                          <button 
                            @click.stop="toggleSessionExpanded(session.id)"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                          >
                            <i :class="expandedSessions.includes(session.id) ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                          </button>
                          
                          <!-- Session Status Badge -->
                          <span class="px-2 py-1 text-xs font-bold rounded-full whitespace-nowrap" 
                                :class="{
                                  'bg-green-100 text-green-800': getSessionStatus(session) === 'completed',
                                  'bg-blue-100 text-blue-800': getSessionStatus(session) === 'in-progress',
                                  'bg-gray-100 text-gray-800': getSessionStatus(session) === 'locked',
                                  'bg-primary-green/10 text-primary-green': getSessionStatus(session) === 'available'
                                }">
                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                            {{ getSessionStatusLabel(getSessionStatus(session)) }}
                          </span>
                          
                          <!-- Start/Continue button -->
                          <button 
                            v-if="getSessionStatus(session) !== 'locked'"
                            @click.stop="startSession(session)"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all whitespace-nowrap"
                            :class="{
                              'bg-primary-green text-white hover:bg-green-600': getSessionStatus(session) !== 'completed',
                              'bg-green-100 text-green-800 hover:bg-green-200': getSessionStatus(session) === 'completed'
                            }"
                            :disabled="!canStartSession(session)"
                          >
                            {{ getSessionButtonLabel(session) }}
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Expanded Content (Activities) -->
                    <div 
                      v-if="expandedSessions.includes(session.id)"
                      class="border-t border-gray-100 bg-gray-50 p-4"
                    >
                      <!-- Activities Header -->
                      <div class="flex items-center justify-between mb-3">
                        <h6 class="text-sm font-semibold text-gray-700">
                          <i class="fas fa-tasks mr-2"></i>
                          {{ getProgramText('activities') }} ({{ session.activities?.length || 0 }})
                        </h6>
                        <span class="text-xs text-gray-500">
                          {{ completedActivitiesCount(session) }} / {{ session.activities?.length || 0 }} {{ getProgramText('completed') }}
                        </span>
                      </div>
                      
                      <!-- Activities List -->
                      <div class="space-y-2" v-if="session.activities && session.activities.length > 0">
                        <div 
                          v-for="activity in session.activities"
                          :key="activity.id"
                          class="bg-white rounded-lg p-3 border border-gray-200 hover:border-primary-green/50 transition-colors"
                          @click="viewActivityDetails(activity)"
                        >
                          <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3 flex-1">
                              <!-- Activity Info -->
                              <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                  <i :class="[getActivityTypeIcon(activity.activity_type), getActivityTypeColor(activity.activity_type), 'text-sm']"></i>
                                  <span class="font-medium text-sm text-gray-900">
                                    {{ getActivityTitle(activity) }}
                                  </span>
                                  <span v-if="activity.user_submission?.is_completed" class="text-green-500 text-xs">
                                    <i class="fas fa-check-circle"></i>
                                  </span>
                                  <span v-if="activity.is_mandatory" class="text-red-500 text-xs">
                                    <i class="fas fa-exclamation-circle"></i>
                                  </span>
                                </div>
                                <p class="text-xs text-gray-600 line-clamp-2">
                                  {{ getActivityDescription(activity) }}
                                </p>
                                
                                <!-- Activity Details -->
                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                  <span v-if="activity.is_mandatory" class="text-red-500">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ currentLanguage === 'ar' ? 'إلزامي' : 'Mandatory' }}
                                  </span>
                                </div>
                              </div>
                            </div>
                            
                            <!-- Activity Action -->
                            <button 
                              v-if="selectedProgram.is_enrolled"
                              @click.stop="startActivity(activity)"
                              class="ml-2 px-2 py-1 text-xs bg-primary-green/10 text-primary-green rounded hover:bg-primary-green hover:text-white transition-colors whitespace-nowrap"
                            >
                              {{ activity.user_submission?.is_completed ? getButtonLabel('review') : getButtonLabel('start') }}
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <!-- No Activities -->
                      <div v-else class="text-center py-4 text-gray-500 text-sm">
                        <i class="fas fa-inbox text-2xl mb-2"></i>
                        <p>{{ currentLanguage === 'ar' ? 'لا توجد أنشطة لهذه الجلسة' : 'No activities for this session' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- No Sessions Message -->
              <div v-else-if="!loadingSessions" class="text-center py-8">
                <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                  <i class="fas fa-calendar-times text-6xl"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900 mb-2">
                  {{ currentLanguage === 'ar' ? 'لا توجد جلسات متاحة' : 'No sessions available' }}
                </h4>
                <p class="text-gray-600">
                  {{ currentLanguage === 'ar' ? 'لم يتم إضافة جلسات لهذا البرنامج بعد' : 'No sessions have been added to this program yet' }}
                </p>
              </div>
            </div>
          

          <!-- Modal Footer -->
          <div class="sticky bottom-0 bg-white border-t border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row gap-3">
              <button 
                v-if="!selectedProgram.is_enrolled && canEnroll(selectedProgram)"
                @click="enrollProgram(selectedProgram)"
                class="flex-1 px-6 py-4 rounded-xl text-white font-bold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 shadow-md flex items-center justify-center gap-3 bg-gradient-to-r from-primary-green to-secondary-green"
              >
                <i class="fas fa-user-plus text-lg"></i>
                {{ getButtonLabel('enrollNow') }}
              </button>
              <button 
                v-if="selectedProgram.is_enrolled"
                @click="continueProgram(selectedProgram)"
                class="flex-1 px-6 py-4 border-2 border-primary-green text-primary-green font-bold rounded-xl hover:bg-primary-green hover:text-white transition-all duration-300 flex items-center justify-center gap-3"
              >
                <i class="fas fa-play-circle"></i>
                {{ getButtonLabel('continue') }}
              </button>
              <button 
                @click="selectedProgram = null"
                class="px-6 py-4 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all flex items-center justify-center gap-2"
              >
                <i class="fas fa-times"></i>
                {{ getButtonLabel('close') }}
              </button>
            </div>
          </div></div>
        </div>
      </div>
    </div>
    <!-- Activity Details Modal -->
    <div v-if="showActivityDetailsModal && selectedActivity" class="fixed inset-0 z-[600] overflow-y-auto">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
      <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl max-w-3xl w-full shadow-2xl">
          <div class="p-6 border-b border-gray-200 flex items-start justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900">
                {{ getActivityTitle(selectedActivity) }}
              </h3>
              <p class="text-sm text-gray-500">
                {{ getActivityTypeText(selectedActivity.activity_type) }}
              </p>
            </div>
            <button
              @click="showActivityDetailsModal = false"
              class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors"
              :aria-label="getButtonLabel('close')"
            >
              <i class="fas fa-times text-xl text-gray-500"></i>
            </button>
          </div>
          <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <p v-if="getActivityDescription(selectedActivity)" class="text-gray-700">
              {{ getActivityDescription(selectedActivity) }}
            </p>
            <div v-if="selectedActivity.content_ar" class="prose max-w-none">
              <div v-html="selectedActivity.content_ar"></div>
            </div>
            <div v-if="selectedActivity.activity_type === 'quiz'" class="space-y-3">
              <div class="bg-primary-green/10 rounded-lg p-4 text-sm text-gray-700">
                <div class="font-semibold text-gray-900 mb-2">
                  {{ currentLanguage === 'ar' ? 'اختبار' : 'Quiz' }}
                </div>
                <p>
                  {{ currentLanguage === 'ar'
                    ? 'هذا النشاط عبارة عن اختبار مرتبط بمقياس نفسي.'
                    : 'This activity is a quiz linked to a psychological scale.' }}
                </p>
              </div>
              <button
                v-if="selectedActivity.scale_id"
                class="btn btn-primary"
                @click="router.push({ name: 'Measures', query: { measureId: selectedActivity.scale_id } })"
              >
                {{ currentLanguage === 'ar' ? 'بدء الاختبار' : 'Start Quiz' }}
              </button>
            </div>

            <div v-else-if="selectedActivity.media_url" class="space-y-3">
              <video
                v-if="selectedActivity.media_type === 'video'"
                :src="selectedActivity.media_url"
                controls
                class="w-full rounded-lg"
              ></video>
              <audio
                v-else-if="selectedActivity.media_type === 'audio'"
                :src="selectedActivity.media_url"
                controls
                class="w-full"
              ></audio>
              <a
                v-else
                :href="selectedActivity.media_url"
                target="_blank"
                class="text-primary-green underline"
              >
                {{ currentLanguage === 'ar' ? 'فتح الملف' : 'Open file' }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Header from '@/components/frontend/layouts/Header.vue'  
import Footer from '@/components/frontend/layouts/Footer.vue' 
import Hero from '@/components/frontend/layouts/hero.vue'
import { useTranslations } from '@/composables/useTranslations'
import api from '@/utils/api'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import { useNotifications } from '@/composables/useNotifications'

const router = useRouter()
const route = useRoute()
const { showError, showSuccess } = useNotifications()
const { currentLanguage, toggleLanguage, translate } = useTranslations()

// Refs
const loading = ref(false)
const loadingSessions = ref(false)
const programs = ref([])
const error = ref(null)
const selectedProgram = ref(null)
const activeFilter = ref('all')
const searchQuery = ref('')
const sortBy = ref('newest')
const pagination = ref(null)
const programsSection = ref(null)
const showQuickEnrollModal = ref(false)
const programToEnroll = ref(null)
const expandedSessions = ref([])
const selectedActivity = ref(null)
const showActivityDetailsModal = ref(false)

// جلسات البرنامج المحدد
const programSessions = ref([])

// Filters
const filters = [
  { id: 'all', label: 'all', icon: 'fas fa-list' },
  { id: 'ongoing', label: 'ongoing', icon: 'fas fa-play-circle' },
  { id: 'upcoming', label: 'upcoming', icon: 'fas fa-calendar-alt' },
  { id: 'enrolled', label: 'enrolled', icon: 'fas fa-user-check' }
]

// ==================== COMPUTED PROPERTIES ====================

const filteredPrograms = computed(() => {
  let filtered = [...programs.value]

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(program => 
      getProgramTitle(program).toLowerCase().includes(query) ||
      getProgramDescription(program).toLowerCase().includes(query) ||
      getProgramScale(program).toLowerCase().includes(query)
    )
  }

  // Apply status filter
  if (activeFilter.value !== 'all') {
    filtered = filtered.filter(program => {
      switch (activeFilter.value) {
        case 'ongoing':
          return program.status === 'active'
        case 'upcoming':
          return program.status === 'upcoming'
        case 'enrolled':
          return program.is_enrolled === true
        default:
          return true
      }
    })
  }

  // Apply sorting
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'newest':
        return new Date(b.created_at) - new Date(a.created_at)
      case 'oldest':
        return new Date(a.created_at) - new Date(b.created_at)
      case 'name_asc':
        return getProgramTitle(a)?.localeCompare(getProgramTitle(b))
      case 'name_desc':
        return getProgramTitle(b)?.localeCompare(getProgramTitle(a))
      case 'popular':
        return (b.enrollment_count || 0) - (a.enrollment_count || 0)
      default:
        return 0
    }
  })

  return filtered
})

const paginatedPrograms = computed(() => {
  if (!pagination.value) return filteredPrograms.value
  
  const startIndex = (pagination.value.current_page - 1) * pagination.value.per_page
  const endIndex = startIndex + pagination.value.per_page
  return filteredPrograms.value.slice(startIndex, endIndex)
})

const ongoingCount = computed(() => {
  return programs.value.filter(p => p.status === 'active').length
})

const upcomingCount = computed(() => {
  return programs.value.filter(p => p.status === 'upcoming').length
})

const enrolledCount = computed(() => {
  return programs.value.filter(p => p.is_enrolled).length
})

const completedSessionsCount = computed(() => {
  return programSessions.value.filter(s => s.is_completed).length
})

// ==================== HELPER FUNCTIONS ====================

// التحقق من حالة المصادقة
const isAuthenticated = () => {
  const token = localStorage.getItem('frontend_token')
  return !!(token && token.trim() !== '' && token !== 'null' && token !== 'undefined')
}

// دالة للحصول على عنوان البرنامج
const getProgramTitle = (program) => {
  if (!program) return ''
  
  if (currentLanguage.value === 'ar') {
    return program.name_ar || program.name_en || program.name || ''
  } else {
    return program.name_en || program.name_ar || program.name || ''
  }
}

// دالة للحصول على وصف البرنامج
const getProgramDescription = (program) => {
  if (!program) return ''
  
  if (currentLanguage.value === 'ar') {
    return program.description_ar || program.description_en || program.description || ''
  } else {
    return program.description_en || program.description_ar || program.description || ''
  }
}

// دالة للحصول على عدد الجلسات
const getTotalSessions = (program) => {
  return program.sessions_count || 0
}

// دالة للحصول على اسم المقياس
const getProgramScale = (program) => {
  if (!program.scale) return ''
  return program.scale.name || ''
}

// دالة للحصول على مدة البرنامج
const getDurationText = (program) => {
  if (!program) return getProgramText('flexible')
  
  if (program.duration) {
    return `${program.duration} ${getProgramText('days')}`
  }
  
  if (program.max_duration_days) {
    return `${program.max_duration_days} ${getProgramText('days')}`
  }
  
  if (program.sessions_count > 0) {
    return `${program.sessions_count} ${getProgramText('sessions')}`
  }
  
  return getProgramText('flexible')
}

// دالة للحصول على عنوان الجلسة
const getSessionTitle = (session) => {
  if (!session) return ''
  
  if (currentLanguage.value === 'ar') {
    return session.title_ar || session.title_en || session.title || ''
  } else {
    return session.title_en || session.title_ar || session.title || ''
  }
}

// دالة للحصول على وصف الجلسة
const getSessionDescription = (session) => {
  if (!session) return ''
  
  if (currentLanguage.value === 'ar') {
    return session.description_ar || session.description_en || session.description || session.goal || ''
  } else {
    return session.description_en || session.description_ar || session.description || session.goal || ''
  }
}

// دالة لتحويل مسار الصورة إلى URL كامل
const getFullImageUrl = (imagePath) => {
  if (!imagePath) {
    return '/images/program-default.jpg'
  }
  
  // إذا كان المسار يحتوي بالفعل على http أو https، ارجعه كما هو
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }
  
  // إذا كان المسار يبدأ بـ media/، أضف المسار الكامل
  if (imagePath.startsWith('media/')) {
    return `${window.location.origin}/${imagePath}`
  }
  
  // إذا كان المسار يبدأ بـ programs/ فقط، أضف media/ قبلها
  if (imagePath.startsWith('programs/')) {
    return `${window.location.origin}/media/${imagePath}`
  }
  
  // إذا كان المسار يبدأ بـ /storage/، أضف النطاق
  if (imagePath.startsWith('/storage/')) {
    return `${window.location.origin}${imagePath}`
  }
  
  // في حالات أخرى، أضف media/programs/ قبل المسار
  return `${window.location.origin}/media/programs/${imagePath}`
}

// معالجة أخطاء تحميل الصور
const handleImageError = (event, program) => {
  console.log('🖼️ Image load error for program:', {
    id: program.id,
    name: getProgramTitle(program),
    image_url: program.image_url
  })
  
  // استخدام صورة افتراضية
  const fallbackImage = '/images/program-default.jpg'
  
  if (event.target.src !== fallbackImage) {
    event.target.src = fallbackImage
  }
  
  event.target.onerror = null
}

// معالجة تحميل الصور بنجاح
const handleImageLoad = (program) => {
  console.log('✅ Image loaded successfully:', {
    id: program.id,
    name: getProgramTitle(program),
    image_url: program.image_url
  })
}

// دالات الترجمة المباشرة
const getFilterLabel = (filterId) => {
  const labels = {
    'ar': {
      'all': 'جميع البرامج',
      'ongoing': 'جارية الآن',
      'upcoming': 'قادمة',
      'enrolled': 'مسجل فيها'
    },
    'en': {
      'all': 'All Programs',
      'ongoing': 'Ongoing',
      'upcoming': 'Upcoming',
      'enrolled': 'Enrolled'
    }
  }
  return labels[currentLanguage.value]?.[filterId] || filterId
}

const getSortLabel = (sortId) => {
  const labels = {
    'ar': {
      'newest': 'الأحدث',
      'oldest': 'الأقدم',
      'popular': 'الأكثر شعبية',
      'name_asc': 'الاسم أ-ي',
      'name_desc': 'الاسم ي-أ'
    },
    'en': {
      'newest': 'Newest',
      'oldest': 'Oldest',
      'popular': 'Most Popular',
      'name_asc': 'Name A-Z',
      'name_desc': 'Name Z-A'
    }
  }
  return labels[currentLanguage.value]?.[sortId] || sortId
}

const getPlaceholder = () => {
  return currentLanguage.value === 'ar' ? 'ابحث عن برنامج...' : 'Search for a program...'
}

const getErrorMessage = (errorKey) => {
  const messages = {
    'ar': {
      'oops': 'عذراً!',
      'loading': 'حدث خطأ في تحميل البيانات',
      'network': 'خطأ في الاتصال بالشبكة',
      'sessionExpired': 'انتهت جلستك، يرجى تسجيل الدخول مرة أخرى',
      'enrollFailed': 'فشل في التسجيل',
      'loadingDetails': 'حدث خطأ في تحميل التفاصيل',
      'loginRequired': 'يجب تسجيل الدخول للوصول إلى هذه الصفحة',
      'completePreviousSession': 'يجب إكمال الجلسة السابقة أولاً',
      'serverError': 'خطأ في الخادم الداخلي'
    },
    'en': {
      'oops': 'Oops!',
      'loading': 'Error loading data',
      'network': 'Network connection error',
      'sessionExpired': 'Your session has expired, please log in again',
      'enrollFailed': 'Failed to enroll',
      'loadingDetails': 'Error loading details',
      'loginRequired': 'Login required to access this page',
      'completePreviousSession': 'You must complete the previous session first',
      'serverError': 'Internal server error'
    }
  }
  return messages[currentLanguage.value]?.[errorKey] || errorKey
}

const getButtonLabel = (buttonKey) => {
  const labels = {
    'ar': {
      'startJourney': 'ابدأ رحلتك',
      'learnMore': 'تعرف أكثر',
      'retry': 'إعادة المحاولة',
      'resetFilters': 'إعادة التعيين',
      'viewDetails': 'عرض التفاصيل',
      'enroll': 'التسجيل',
      'continue': 'المتابعة',
      'previous': 'السابق',
      'next': 'التالي',
      'confirm': 'تأكيد',
      'cancel': 'إلغاء',
      'close': 'إغلاق',
      'enrollNow': 'سجل الآن',
      'start': 'ابدأ',
      'review': 'مراجعة'
    },
    'en': {
      'startJourney': 'Start Your Journey',
      'learnMore': 'Learn More',
      'retry': 'Try Again',
      'resetFilters': 'Reset Filters',
      'viewDetails': 'View Details',
      'enroll': 'Enroll',
      'continue': 'Continue',
      'previous': 'Previous',
      'next': 'Next',
      'confirm': 'Confirm',
      'cancel': 'Cancel',
      'close': 'Close',
      'enrollNow': 'Enroll Now',
      'start': 'Start',
      'review': 'Review'
    }
  }
  return labels[currentLanguage.value]?.[buttonKey] || buttonKey
}

const getProgramText = (textKey) => {
  const texts = {
    'ar': {
      'duration': 'المدة',
      'sessions': 'الجلسات',
      'activities': 'الأنشطة',
      'progress': 'التقدم',
      'description': 'الوصف',
      'sessionsList': 'قائمة الجلسات',
      'complete': 'مكتمل',
      'completed': 'مكتمل',
      'days': 'أيام',
      'hours': 'ساعات',
      'minutes': 'دقائق',
      'flexible': 'مرن',
      'enrolled': 'مسجل',
      'ongoing': 'جارية',
      'upcoming': 'قادمة',
      'available': 'متاحة',
      'sessionDuration': 'مدة الجلسة',
      'activityGap': 'فترة بين الأنشطة',
      'sessionGap': 'فترة بين الجلسات',
      'enrolledCount': 'عدد المشتركين',
      'noPrograms.title': 'لا توجد برامج متاحة',
      'noPrograms.subtitle': 'لم نتمكن من العثور على أي برامج تطابق معايير البحث الخاصة بك',
      'enroll.title': 'التسجيل في البرنامج',
      'enroll.message': 'هل أنت متأكد من رغبتك في التسجيل في هذا البرنامج؟',
      'freeServices.title': 'خدمات مجانية',
      'freeServices.subtitle': 'استفد من خدماتنا المجانية لدعم رحلتك النفسية',
    },
    'en': {
      'duration': 'Duration',
      'sessions': 'Sessions',
      'activities': 'Activities',
      'progress': 'Progress',
      'description': 'Description',
      'sessionsList': 'Sessions List',
      'complete': 'Complete',
      'completed': 'Completed',
      'days': 'Days',
      'hours': 'Hours',
      'minutes': 'Minutes',
      'flexible': 'Flexible',
      'enrolled': 'Enrolled',
      'ongoing': 'Ongoing',
      'upcoming': 'Upcoming',
      'available': 'Available',
      'sessionDuration': 'Session Duration',
      'activityGap': 'Activity Gap',
      'sessionGap': 'Session Gap',
      'enrolledCount': 'Enrolled Count',
      'noPrograms.title': 'No Programs Available',
      'noPrograms.subtitle': 'We couldn\'t find any programs matching your search criteria',
      'enroll.title': 'Enroll in Program',
      'enroll.message': 'Are you sure you want to enroll in this program?',
      'freeServices.title': 'Free Services',
      'freeServices.subtitle': 'Take advantage of our free services to support your mental health journey',
    }
  }
  return texts[currentLanguage.value]?.[textKey] || textKey
}

const getSessionStatusLabel = (status) => {
  const labels = {
    'ar': {
      'available': 'متاحة',
      'in-progress': 'قيد التقدم',
      'completed': 'مكتملة',
      'locked': 'مقفلة'
    },
    'en': {
      'available': 'Available',
      'in-progress': 'In Progress',
      'completed': 'Completed',
      'locked': 'Locked'
    }
  }
  return labels[currentLanguage.value]?.[status] || status
}

const getMessage = (messageKey) => {
  const messages = {
    'ar': {
      'loadedSuccess': 'تم تحميل البرامج بنجاح',
      'sessionsLoaded': 'تم تحميل الجلسات بنجاح',
      'enrollSuccess': 'تم التسجيل في البرنامج بنجاح',
      'enrollDetails': 'يمكنك الآن البدء في الجلسات',
      'loginToEnroll': 'سجل الدخول للتسجيل في البرامج',
      'sessionCompleted': 'تم إكمال الجلسة بنجاح',
      'activityCompleted': 'تم إكمال النشاط بنجاح',
      'sessionUnlocked': 'تم فتح الجلسة التالية',
      'mustCompleteAllActivities': 'يجب إكمال جميع الأنشطة قبل إنهاء الجلسة'
    },
    'en': {
      'loadedSuccess': 'Programs loaded successfully',
      'sessionsLoaded': 'Sessions loaded successfully',
      'enrollSuccess': 'Enrolled in program successfully',
      'enrollDetails': 'You can now start the sessions',
      'loginToEnroll': 'Log in to enroll in programs',
      'sessionCompleted': 'Session completed successfully',
      'activityCompleted': 'Activity completed successfully',
      'sessionUnlocked': 'Next session unlocked',
      'mustCompleteAllActivities': 'You must complete all activities before finishing the session'
    }
  }
  return messages[currentLanguage.value]?.[messageKey] || messageKey
}

// ==================== PROGRAM FUNCTIONS ====================

const getProgramBadge = (program) => {
  if (program.is_enrolled) return getProgramText('enrolled')
  
  switch (program.status) {
    case 'active':
      return getProgramText('ongoing')
    case 'upcoming':
      return getProgramText('upcoming')
    case 'completed':
      return getProgramText('completed')
    case 'available':
      return getProgramText('available')
    default:
      return getProgramText('available')
  }
}

const getBadgeClass = (program) => {
  if (program.is_enrolled) return 'bg-emerald-100 text-emerald-800'
  
  switch (program.status) {
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'upcoming':
      return 'bg-blue-100 text-blue-800'
    case 'completed':
      return 'bg-gray-100 text-gray-800'
    case 'available':
      return 'bg-primary-green/10 text-primary-green'
    default:
      return 'bg-primary-green/10 text-primary-green'
  }
}

const getProgramIcon = (id) => {
  const icons = [
    'fas fa-user-md',
    'fas fa-brain',
    'fas fa-users',
    'fas fa-child',
    'fas fa-laptop-medical',
    'fas fa-heartbeat'
  ]
  return icons[id?.toString().charCodeAt(0) % icons.length] || 'fas fa-book'
}

// الحصول على نوع النشاط
const getActivityTypeIcon = (type) => {
  const types = {
    video: 'fas fa-video',
    audio: 'fas fa-headphones',
    article: 'fas fa-file-alt',
    quiz: 'fas fa-question-circle',
    exercise: 'fas fa-running',
    assignment: 'fas fa-tasks',
    discussion: 'fas fa-comments',
    survey: 'fas fa-poll',
    reading: 'fas fa-book-open',
    practice: 'fas fa-dumbbell',
    meditation: 'fas fa-spa',
    reflection: 'fas fa-lightbulb',
    unknown: 'fas fa-star'
  }
  return types[type] || types.unknown
}

const getActivityTypeText = (type) => {
  const labels = {
    text: currentLanguage.value === 'ar' ? 'نص' : 'Text',
    video: currentLanguage.value === 'ar' ? 'فيديو' : 'Video',
    audio: currentLanguage.value === 'ar' ? 'صوتي' : 'Audio',
    file: currentLanguage.value === 'ar' ? 'ملف' : 'File',
    form: currentLanguage.value === 'ar' ? 'نموذج' : 'Form',
    exercise: currentLanguage.value === 'ar' ? 'تمرين' : 'Exercise',
    reflection_questions: currentLanguage.value === 'ar' ? 'أسئلة انعكاسية' : 'Reflection',
    quiz: currentLanguage.value === 'ar' ? 'اختبار' : 'Quiz'
  }
  return labels[type] || type
}

const getActivityTypeColor = (type) => {
  const colors = {
    video: 'text-red-500',
    audio: 'text-blue-500',
    article: 'text-green-500',
    quiz: 'text-purple-500',
    exercise: 'text-amber-500',
    assignment: 'text-teal-500',
    discussion: 'text-indigo-500',
    survey: 'text-pink-500',
    reading: 'text-emerald-500',
    practice: 'text-orange-500',
    meditation: 'text-cyan-500',
    reflection: 'text-violet-500',
    unknown: 'text-gray-500'
  }
  return colors[type] || colors.unknown
}

// دالة للحصول على عنوان النشاط
const getActivityTitle = (activity) => {
  if (!activity) return ''
  
  if (currentLanguage.value === 'ar') {
    return activity.name_ar || activity.name_en || activity.name || ''
  } else {
    return activity.name_en || activity.name_ar || activity.name || ''
  }
}

// دالة للحصول على وصف النشاط
const getActivityDescription = (activity) => {
  if (!activity) return ''
  
  if (currentLanguage.value === 'ar') {
    return activity.instructions_ar || activity.instructions_en || activity.instructions || ''
  } else {
    return activity.instructions_en || activity.instructions_ar || activity.instructions || ''
  }
}

// ==================== EVENT HANDLERS ====================

const startJourney = () => {
  router.push('/programs?filter=enrolled')
}

const scrollToPrograms = () => {
  if (programsSection.value) {
    programsSection.value.scrollIntoView({ behavior: 'smooth' })
  }
}

const setActiveFilter = (filterId) => {
  activeFilter.value = filterId
}

const searchPrograms = () => {
  console.log('Searching for:', searchQuery.value)
}

const resetFilters = () => {
  activeFilter.value = 'all'
  searchQuery.value = ''
  sortBy.value = 'newest'
}

const getPaginationPages = () => {
  if (!pagination.value) return []
  
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  
  // Always show first page
  pages.push(1)
  
  // Calculate range around current page
  let start = Math.max(2, current - 1)
  let end = Math.min(last - 1, current + 1)
  
  // Adjust if we're at the beginning
  if (current <= 3) {
    end = Math.min(last - 1, 4)
  }
  
  // Adjust if we're at the end
  if (current >= last - 2) {
    start = Math.max(2, last - 3)
  }
  
  // Add ellipsis after first page if needed
  if (start > 2) {
    pages.push('...')
  }
  
  // Add middle pages
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  // Add ellipsis before last page if needed
  if (end < last - 1) {
    pages.push('...')
  }
  
  // Always show last page if not already shown
  if (last > 1) {
    pages.push(last)
  }
  
  return pages
}

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page || page === pagination.value.current_page) {
    return
  }
  
  pagination.value.current_page = page
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const viewProgramDetails = async (program) => {
  try {
    selectedProgram.value = program
    expandedSessions.value = []
    
    // Load program sessions
    await loadProgramSessions(program.id)
  } catch (err) {
    console.error('Error loading program details:', err)
    showError(getErrorMessage('loadingDetails'))
  }
}

const quickEnroll = (program) => {
  if (!isAuthenticated()) {
    showError(getErrorMessage('loginRequired'))
    router.push({
      path: '/login',
      query: { redirect: router.currentRoute.value.fullPath }
    })
    return
  }
  
  programToEnroll.value = program
  showQuickEnrollModal.value = true
}

const confirmEnroll = async () => {
  if (!programToEnroll.value) return
  
  try {
    await enrollProgram(programToEnroll.value)
    showQuickEnrollModal.value = false
    programToEnroll.value = null
  } catch (error) {
    console.error('Enrollment error:', error)
  }
}

const canEnroll = (program) => {
  if (!isAuthenticated()) return false
  
  if (program.is_enrolled) return false
  
  return program.status === 'active'
}

// ==================== API FUNCTIONS ====================

const loadPrograms = async (page = 1) => {
  loading.value = true
  error.value = null
  
  try {
    const params = {
      page,
      per_page: 12,
      search: searchQuery.value || undefined,
      category: activeFilter.value !== 'all' && activeFilter.value !== 'enrolled' ? activeFilter.value : undefined
    }
    
    console.log(`📡 Loading programs with params:`, params)
    const response = await api.get('/frontend/programs', { params })
    
    console.log('📦 Programs API Response:', response)
    
    if (response.data && response.data.success) {
      programs.value = response.data.data || []
      pagination.value = response.data.meta || {
        current_page: 1,
        last_page: 1,
        per_page: 12,
        total: programs.value.length
      }
      
      console.log(`✅ Programs loaded successfully: ${programs.value.length} programs`)
      
      // Debug: Log first program structure
      if (programs.value.length > 0) {
        const firstProgram = programs.value[0]
        console.log('📊 First program structure:', {
          id: firstProgram.id,
          name_ar: firstProgram.name_ar,
          name_en: firstProgram.name_en,
          description_ar: firstProgram.description_ar,
          description_en: firstProgram.description_en,
          image_url: firstProgram.image_url,
          sessions_count: firstProgram.sessions_count,
          is_enrolled: firstProgram.is_enrolled,
          user_progress: firstProgram.user_progress,
          scale: firstProgram.scale,
          duration: firstProgram.duration,
          status: firstProgram.status,
          session_duration_minutes: firstProgram.session_duration_minutes,
          activity_gap_hours: firstProgram.activity_gap_hours,
          session_gap_hours: firstProgram.session_gap_hours,
          enrollment_count: firstProgram.enrollment_count
        })
      }
    } else {
      console.error('❌ API response not successful:', response.data)
      error.value = 'فشل في تحميل البرامج'
    }
  } catch (err) {
    console.error('❌ Error loading programs:', err)
    
    if (err.response) {
      console.error('Response status:', err.response.status)
      console.error('Response data:', err.response.data)
      
      if (err.response.status === 401) {
        error.value = getErrorMessage('sessionExpired')
        localStorage.removeItem('frontend_token')
        router.push('/login')
      } else if (err.response.status === 404) {
        error.value = 'نقطة النهاية غير موجودة'
      } else if (err.response.status === 500) {
        error.value = 'خطأ في الخادم الداخلي'
        
        if (err.response.data && typeof err.response.data === 'object') {
          console.error('Server error details:', err.response.data)
          
          if (err.response.data.message) {
            error.value = `خطأ في الخادم: ${err.response.data.message}`
          }
          
          if (err.response.data.exception) {
            console.error('Laravel Exception:', err.response.data.exception)
            console.error('File:', err.response.data.file)
            console.error('Line:', err.response.data.line)
          }
        }
      } else {
        error.value = err.response.data?.message || getErrorMessage('loading')
      }
    } else if (err.request) {
      console.error('No response received:', err.request)
      error.value = getErrorMessage('network')
    } else {
      console.error('Error setting up request:', err.message)
      error.value = getErrorMessage('loading')
    }
  } finally {
    loading.value = false
  }
}

const loadProgramSessions = async (programId) => {
  if (!programId) {
    console.error('❌ No program ID provided')
    return
  }
  
  loadingSessions.value = true
  programSessions.value = []
  
  try {
    console.log(`📡 Loading sessions for program ${programId}...`)
    
    try {
      await api.head(`/frontend/programs/${programId}/sessions`)
    } catch (testErr) {
      console.warn(`⚠️ Endpoint test failed:`, testErr.message)
    }
    
    const response = await api.get(`/frontend/programs/${programId}/sessions`)
    
    console.log('📦 Sessions API Response:', {
      status: response.status,
      statusText: response.statusText,
      data: response.data
    })
    
    if (response.data && response.data.success) {
      let sessions = []
      
      if (response.data.data?.sessions && Array.isArray(response.data.data.sessions)) {
        sessions = response.data.data.sessions
      } else if (response.data.data && Array.isArray(response.data.data)) {
        sessions = response.data.data
      } else if (Array.isArray(response.data.sessions)) {
        sessions = response.data.sessions
      } else if (Array.isArray(response.data)) {
        sessions = response.data
      }
      
      console.log(`✅ Sessions extracted: ${sessions.length} sessions`)
      
      if (sessions.length > 0) {
        programSessions.value = sessions
        
        console.log('📊 First session structure:', {
          id: sessions[0].id,
          title_ar: sessions[0].title_ar,
          title_en: sessions[0].title_en,
          title: sessions[0].title,
          order: sessions[0].order,
          description_ar: sessions[0].description_ar,
          description_en: sessions[0].description_en,
          description: sessions[0].description,
          activities_count: sessions[0].activities_count,
          is_completed: sessions[0].is_completed,
          user_completion: sessions[0].user_completion,
          activities: sessions[0].activities
        })
      } else {
        console.warn('⚠️ No sessions found in response')
        programSessions.value = []
      }
    } else {
      console.error('❌ Sessions API response not successful:', response.data)
      
      if (response.data?.message) {
        showError(`فشل في تحميل الجلسات: ${response.data.message}`)
      } else {
        showError('فشل في تحميل الجلسات')
      }
    }
  } catch (err) {
    console.error('❌ Error loading sessions:', err)
    
    if (err.response) {
      console.error('Response status:', err.response.status)
      console.error('Response data:', err.response.data)
      
      if (err.response.status === 401) {
        showError(getErrorMessage('sessionExpired'))
      } else if (err.response.status === 404) {
        showError('نقطة النهاية غير موجودة')
      } else if (err.response.status === 500) {
        showError(getErrorMessage('serverError'))
        
        if (err.response.data) {
          console.error('Server error details:', err.response.data)
        }
      } else {
        showError(err.response.data?.message || getErrorMessage('loading'))
      }
    } else if (err.request) {
      console.error('No response received:', err.request)
      showError(getErrorMessage('network'))
    } else {
      console.error('Error setting up request:', err.message)
      showError(getErrorMessage('loading'))
    }
    
    programSessions.value = []
  } finally {
    loadingSessions.value = false
  }
}

const enrollProgram = async (program) => {
  if (!isAuthenticated()) {
    showError(getErrorMessage('loginRequired'))
    router.push({
      path: '/login',
      query: { redirect: router.currentRoute.value.fullPath }
    })
    return
  }
  
  try {
    loading.value = true
    
    console.log(`📡 Enrolling in program ${program.id}...`)
    const response = await api.post(`/frontend/programs/${program.id}/enroll`)
    
    console.log('📦 Enroll API Response:', response.data)
    
    if (response.data && response.data.success) {
      const programIndex = programs.value.findIndex(p => p.id === program.id)
      if (programIndex !== -1) {
        programs.value[programIndex].is_enrolled = true
        programs.value[programIndex].user_progress = {
          percentage: 0,
          completed_sessions: 0,
          total_sessions: getTotalSessions(program),
          completed_activities: 0,
          total_activities: 0
        }
      }
      
      if (selectedProgram.value && selectedProgram.value.id === program.id) {
        selectedProgram.value.is_enrolled = true
        selectedProgram.value.user_progress = {
          percentage: 0,
          completed_sessions: 0,
          total_sessions: getTotalSessions(program),
          completed_activities: 0,
          total_activities: 0
        }
      }
      
      showSuccess(getMessage('enrollSuccess'), getMessage('enrollDetails'))
      
      await loadProgramSessions(program.id)
    } else {
      console.error('❌ Enroll API response not successful:', response.data)
      showError(getErrorMessage('enrollFailed'))
    }
  } catch (err) {
    console.error('❌ Error enrolling in program:', err)
    
    if (err.response) {
      console.error('Response status:', err.response.status)
      console.error('Response data:', err.response.data)
      
      if (err.response.status === 401) {
        showError(getErrorMessage('sessionExpired'))
      } else if (err.response.status === 400) {
        showError(err.response.data?.message || 'أنت مسجل بالفعل في هذا البرنامج')
      } else if (err.response.status === 500) {
        showError(getErrorMessage('serverError'))
      } else {
        showError(err.response.data?.message || getErrorMessage('enrollFailed'))
      }
    } else {
      showError(getErrorMessage('network'))
    }
  } finally {
    loading.value = false
  }
}

const continueProgram = (program) => {
  if (!isAuthenticated()) {
    showError(getErrorMessage('loginRequired'))
    router.push({
      path: '/login',
      query: { redirect: router.currentRoute.value.fullPath }
    })
    return
  }
  
  if (!program.is_enrolled) {
    showError('يجب التسجيل في البرنامج أولاً')
    return
  }
  
  viewProgramDetails(program)
}

// ==================== SESSION FUNCTIONS ====================

const getSessionStatus = (session) => {
  if (session.is_completed) return 'completed'
  
  if (session.user_completion?.is_completed === false) {
    return 'in-progress'
  }
  
  if (session.order > 1) {
    const previousSession = programSessions.value.find(s => s.order === session.order - 1)
    if (previousSession && !previousSession.is_completed) {
      return 'locked'
    }
  }
  
  return 'available'
}

const getSessionProgress = (session) => {
  if (!session.activities || session.activities.length === 0) {
    return session.is_completed ? 100 : 0
  }
  
  const completedActivities = session.activities.filter(a => a.user_submission?.is_completed).length
  return Math.round((completedActivities / session.activities.length) * 100)
}

const getSessionButtonLabel = (session) => {
  const status = getSessionStatus(session)
  
  switch (status) {
    case 'completed':
      return getButtonLabel('review')
    case 'in-progress':
      return getButtonLabel('continue')
    case 'available':
      return getButtonLabel('start')
    default:
      return getButtonLabel('start')
  }
}

const canStartSession = (session) => {
  const status = getSessionStatus(session)
  return status !== 'locked'
}

const toggleSessionExpanded = (sessionId) => {
  const index = expandedSessions.value.indexOf(sessionId)
  if (index > -1) {
    expandedSessions.value.splice(index, 1)
  } else {
    expandedSessions.value.push(sessionId)
  }
}

const startSession = (session) => {
  if (!isAuthenticated()) {
    showError(getErrorMessage('loginRequired'))
    router.push({
      path: '/login',
      query: { redirect: router.currentRoute.value.fullPath }
    })
    return
  }
  
  if (!selectedProgram.value || !selectedProgram.value.is_enrolled) {
    showError('يجب التسجيل في البرنامج أولاً')
    return
  }
  
  const status = getSessionStatus(session)
  
  if (status === 'locked') {
    showError(getErrorMessage('completePreviousSession'))
    return
  }
  
  if (!expandedSessions.value.includes(session.id)) {
    expandedSessions.value.push(session.id)
  }
  if (!session.activities || session.activities.length === 0) {
    showError(currentLanguage.value === 'ar' ? 'لا توجد أنشطة لهذه الجلسة' : 'No activities for this session')
  }
}

const completedActivitiesCount = (session) => {
  if (!session.activities) return 0
  return session.activities.filter(a => a.user_submission?.is_completed).length
}

// ==================== ACTIVITY FUNCTIONS ====================

const viewActivityDetails = (activity) => {
  if (!selectedProgram.value || !selectedProgram.value.is_enrolled) {
    showError('يجب التسجيل في البرنامج أولاً')
    return
  }
  
  selectedActivity.value = activity
  showActivityDetailsModal.value = true
}

const startActivity = (activity) => {
  viewActivityDetails(activity)
}

// ==================== LIFECYCLE HOOKS ====================

onMounted(() => {
  if (route.query.filter) {
    activeFilter.value = route.query.filter
  }
  
  if (route.query.search) {
    searchQuery.value = route.query.search
  }
  
  loadPrograms(route.query.page || 1)
})

watch(currentLanguage, (newLang) => {
  document.documentElement.dir = newLang === 'ar' ? 'rtl' : 'ltr'
  document.documentElement.lang = newLang
})

let searchTimeout
watch(searchQuery, (newQuery) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadPrograms(1)
  }, 500)
})

watch(activeFilter, () => {
  loadPrograms(1)
})
</script>

<style scoped>
.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
}

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}

::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}

.rounded-2xl {
  border-radius: 1rem;
}

.object-cover {
  object-fit: cover;
}

@media (max-width: 640px) {
  .grid-cols-1 {
    grid-template-columns: 1fr;
  }
  
  .flex-wrap {
    flex-wrap: wrap;
  }
  
  .gap-3 {
    gap: 0.75rem;
  }
}
</style>