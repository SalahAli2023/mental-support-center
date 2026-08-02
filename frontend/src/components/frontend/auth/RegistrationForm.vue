<template>
  <div>
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-3">
        {{ getTranslatedTitle('registrationPage.title') }}
      </h1>
    </div>

    <!-- Registration Steps -->
    <div class="flex justify-between items-center mb-8 px-2">
      <div v-for="step in steps" :key="step.number" class="flex flex-col items-center relative flex-1"
        :class="{ 'active': currentStep === step.number, 'completed': currentStep > step.number }">
        <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center mb-2 transition-colors z-10"
             :class="currentStep >= step.number ? 'bg-primary-green border-primary-green' : 'bg-white border-gray-300'">
          <span v-if="currentStep > step.number" class="text-white font-bold">
            <i class="fas fa-check"></i>
          </span>
          <span v-else class="font-bold" :class="currentStep >= step.number ? 'text-white' : 'text-gray-500'">
            {{ step.number }}
          </span>
        </div>
        <span class="text-sm text-center font-medium px-1" 
              :class="currentStep >= step.number ? 'text-primary-green' : 'text-gray-500'">
          {{ step.title }}
        </span>
        <div v-if="step.number < steps.length" class="absolute top-5 w-full h-0.5 bg-gray-300 -z-10" :class="[
               isRTL ? 'left-1/2' : 'right-1/2',
               currentStep > step.number ? 'bg-primary-green' : ''
             ]"></div>
      </div>
    </div>

    <!-- Steps Content -->
    <div class="min-h-96">
      <!-- Step 1: Contact Method (Email or Phone) -->
      <div v-if="currentStep === 1" class="space-y-6 px-2">
        <form @submit.prevent="handleContactSubmit" class="space-y-6">
          <!-- Contact Method Selection -->
          <div>
            <!-- <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.contactStep.methodLabel') }}
            </label> -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
              <!-- <button type="button" @click="form.contact_method = 'email'; form.phone = ''; errors.contact = ''"
                :class="form.contact_method === 'email' 
                  ? 'bg-primary-green text-white border-primary-green' 
                  : 'bg-white text-gray-700 border-gray-300'"
                class="px-4 py-4 border-2 rounded-xl font-semibold transition-all flex items-center justify-center gap-2">
                <i class="fas fa-envelope"></i>
                {{ getTranslatedTitle('registrationPage.contactStep.emailOption') }}
              </button> -->
              <!-- <button type="button" @click="form.contact_method = 'phone'; form.email = ''; errors.contact = ''"
                :class="form.contact_method === 'phone' 
                  ? 'bg-primary-green text-white border-primary-green' 
                  : 'bg-white text-gray-700 border-gray-300'"
                class="px-4 py-4 border-2 rounded-xl font-semibold transition-all flex items-center justify-center gap-2">
                <i class="fas fa-phone"></i>
                {{ getTranslatedTitle('registrationPage.contactStep.phoneOption') }}
              </button> -->
            </div>
          </div>

          <!-- Email Input -->
          <div v-if="form.contact_method === 'email'">
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.contactStep.emailLabel') }}
            </label>
            <input v-model="form.email" type="email"
              :placeholder="getTranslatedTitle('registrationPage.contactStep.emailPlaceholder')" 
              @input="validateContact"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.contact ? 'border-red-500' : ''">
            <p v-if="errors.contact" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.contact }}
            </p>
          </div>

          <!-- Phone Input -->
          <!-- <div v-if="form.contact_method === 'phone'">
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.contactStep.phoneLabel') }}
            </label>
            <input v-model="form.phone" type="tel"
              :placeholder="getTranslatedTitle('registrationPage.contactStep.phonePlaceholder')" 
              @input="validateContact"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.contact ? 'border-red-500' : ''">
            <p v-if="errors.contact" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.contact }}
            </p>
          </div> -->

          <button type="submit" :disabled="!isContactValid || isSubmitting"
            class="w-full py-4 bg-primary-green text-white rounded-xl hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-bold text-base">
            <span v-if="!isSubmitting">{{ getTranslatedTitle('registrationPage.contactStep.continue') }}</span>
            <span v-else class="flex items-center justify-center gap-3" :class="isRTL ? 'flex-row-reverse' : ''">
              <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              {{ getTranslatedTitle('registrationPage.contactStep.sending') }}
            </span>
          </button>
        </form>

        <div v-if="isPage" class="pt-6 border-t border-gray-200 flex justify-center">
          <p class="text-gray-600 text-center">
            {{ getTranslatedDescription('registrationPage.emailStep.haveAccount') }}
            <a href="/login" class="text-primary-green hover:text-opacity-80 font-bold">
              {{ getTranslatedTitle('registrationPage.emailStep.login') }}
            </a>
          </p>
        </div>
      </div>

      <!-- Step 2: Verification Code -->
      <div v-if="currentStep === 2" class="space-y-6 px-2">
        <h3 class="text-xl font-semibold text-center text-gray-800 mb-6">{{
          getTranslatedTitle('registrationPage.codeStep.title') }}</h3>
        
        <div class="text-center space-y-2 mb-8">
          <p class="text-gray-600 text-base">{{ getTranslatedDescription('registrationPage.codeStep.sentTo') }}</p>
          <p class="font-semibold text-primary-green text-lg">
            {{ form.contact_method === 'email' ? form.email : form.phone }}
          </p>
        </div>

        <form @submit.prevent="handleCodeSubmit" class="space-y-6">
          <!-- حاوية مربعات الرمز -->
          <div class="flex justify-center gap-2 px-2">
            <input 
              v-for="n in 6" 
              :key="n" 
              v-model="verificationCode[n - 1]" 
              type="text" 
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="1"
              @input="handleCodeInput(n - 1, $event)" 
              @keydown="handleCodeKeydown(n - 1, $event)"
              @paste="handlePaste($event)"
              class="
                w-12 h-14 
                text-center 
                text-xl
                font-bold 
                border-2 border-gray-300 
                rounded-lg
                focus:ring-2 focus:ring-primary-green focus:border-transparent
                transition-all duration-200
                bg-white
                [appearance:textfield]
                [&::-webkit-outer-spin-button]:appearance-none
                [&::-webkit-inner-spin-button]:appearance-none
              "
              :class="{
                'border-primary-green ring-2 ring-primary-green ring-opacity-30': verificationCode[n - 1],
                'shake': errors.code
              }"
            >
          </div>

          <!-- رسالة الخطأ -->
          <div v-if="errors.code" class="text-center mt-2">
            <p class="text-red-500 text-sm animate-fade-in">
              {{ errors.code }}
            </p>
          </div>

          <!-- وقت إعادة الإرسال -->
          <div class="text-center space-y-4 mt-8">
            <div v-if="resendCounter > 0" class="text-gray-600">
              <p class="text-base mb-2">{{ getTranslatedDescription('registrationPage.codeStep.resendIn') }}</p>
              <div class="inline-flex items-center justify-center gap-2 bg-gray-100 px-4 py-2 rounded-lg">
                <i class="fas fa-clock text-primary-green"></i>
                <span class="font-semibold text-base">{{ formatTime(resendCounter) }}</span>
              </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
              <button 
                v-if="resendCounter <= 0" 
                type="button" 
                @click="resendVerificationCode"
                :disabled="isResending"
                class="
                  text-primary-green hover:text-opacity-80 
                  font-bold text-base transition-colors
                  flex items-center gap-2
                  px-4 py-2 rounded-lg hover:bg-green-50
                  disabled:opacity-50 disabled:cursor-not-allowed
                "
              >
                <i class="fas fa-redo" :class="{ 'animate-spin': isResending }"></i>
                {{ getTranslatedTitle('registrationPage.codeStep.resend') }}
              </button>
              
              <button 
                type="button" 
                @click="currentStep = 1"
                class="
                  text-gray-600 hover:text-gray-800 
                  text-sm
                  px-4 py-2 rounded-lg hover:bg-gray-100
                  transition-colors
                  flex items-center gap-2
                "
              >
                <i class="fas fa-edit"></i>
                {{ getTranslatedTitle('registrationPage.codeStep.editContact') }}
              </button>
            </div>
          </div>

          <!-- زر التأكيد -->
          <div class="pt-6">
            <button 
              type="submit" 
              :disabled="!isCodeComplete || isSubmitting"
              class="
                w-full py-4 
                bg-primary-green text-white 
                rounded-xl hover:bg-opacity-90 
                transition-all 
                disabled:opacity-50 disabled:cursor-not-allowed 
                font-bold text-base
              "
            >
              <span v-if="!isSubmitting">{{ getTranslatedTitle('registrationPage.codeStep.confirm') }}</span>
              <span v-else class="flex items-center justify-center gap-3" :class="isRTL ? 'flex-row-reverse' : ''">
                <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                {{ getTranslatedTitle('registrationPage.codeStep.verifying') }}
              </span>
            </button>
          </div>
        </form>
      </div>

      <!-- Step 3: Basic Information -->
      <div v-if="currentStep === 3" class="space-y-6 px-2">
        <h3 class="text-xl font-semibold text-center text-gray-800 mb-6">{{
          getTranslatedTitle('registrationPage.infoStep.title') }}</h3>
        <div class="text-center text-gray-500">
          <span>{{ getTranslatedDescription('registrationPage.infoStep.contactSummary') }}</span>
          <div class="font-semibold text-primary-green mt-1 text-base">
            {{ form.contact_method === 'email' ? form.email : form.phone }}
          </div>
        </div>
        
        <form @submit.prevent="handleInfoSubmit" class="space-y-6">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.infoStep.nameLabel') }}
            </label>
            <input v-model="form.name" type="text" autocomplete="off"
              :placeholder="getTranslatedTitle('registrationPage.infoStep.namePlaceholder')"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.name ? 'border-red-500' : ''">
            <p v-if="errors.name" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.name }}
            </p>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.infoStep.passwordLabel') }}
            </label>
            <input v-model="form.password" type="password" autocomplete="off"
              :placeholder="getTranslatedTitle('registrationPage.infoStep.passwordPlaceholder')"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.password ? 'border-red-500' : ''">
            <p v-if="errors.password" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.password }}
            </p>
          </div>

          <button type="submit" :disabled="!form.name || !form.password || isSubmitting || !isContactValid"
            class="w-full py-4 bg-primary-green text-white rounded-xl hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-bold text-base">
            <span v-if="!isSubmitting">{{ getTranslatedTitle('registrationPage.infoStep.continue') }}</span>
            <span v-else class="flex items-center justify-center gap-3" :class="isRTL ? 'flex-row-reverse' : ''">
              <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              {{ getTranslatedTitle('registrationPage.infoStep.loading') }}
            </span>
          </button>
        </form>
      </div>

      <!-- Step 4: Detailed Information -->
      <div v-if="currentStep === 4" class="space-y-6 px-2">
        <h3 class="text-xl font-semibold text-center text-gray-800 mb-6">{{
          getTranslatedTitle('registrationPage.detailsStep.title') }}</h3>

        <form @submit.prevent="handleDetailsSubmit" class="space-y-6 max-h-[70vh] overflow-y-auto">
          <!-- Gender -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.genderLabel') }} *
            </label>
            <select v-model="form.gender"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.gender ? 'border-red-500' : ''">
              <option value="">{{ getTranslatedTitle('registrationPage.detailsStep.genderPlaceholder') }}</option>
              <option value="male">{{ getTranslatedTitle('registrationPage.detailsStep.genderMale') }}</option>
              <option value="female">{{ getTranslatedTitle('registrationPage.detailsStep.genderFemale') }}</option>
            </select>
            <p v-if="errors.gender" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.gender }}
            </p>
          </div>

          <!-- Date of Birth -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.dateOfBirthLabel') }} *
            </label>
            <input v-model="form.date_of_birth" type="date"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.date_of_birth ? 'border-red-500' : ''"
              :max="new Date().toISOString().split('T')[0]">
            <p v-if="errors.date_of_birth" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.date_of_birth }}
            </p>
          </div>

          <!-- Address Fields -->
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
                {{ getTranslatedTitle('registrationPage.detailsStep.countryLabel') }} *
              </label>
              <input v-model="form.country" type="text"
                :placeholder="getTranslatedTitle('registrationPage.detailsStep.countryPlaceholder')"
                class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
                :class="errors.country ? 'border-red-500' : ''">
              <p v-if="errors.country" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
                {{ errors.country }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
                {{ getTranslatedTitle('registrationPage.detailsStep.cityLabel') }} *
              </label>
              <input v-model="form.city" type="text"
                :placeholder="getTranslatedTitle('registrationPage.detailsStep.cityPlaceholder')"
                class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
                :class="errors.city ? 'border-red-500' : ''">
              <p v-if="errors.city" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
                {{ errors.city }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
                {{ getTranslatedTitle('registrationPage.detailsStep.governorateLabel') }} *
              </label>
              <input v-model="form.governorate" type="text"
                :placeholder="getTranslatedTitle('registrationPage.detailsStep.governoratePlaceholder')"
                class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
                :class="errors.governorate ? 'border-red-500' : ''">
              <p v-if="errors.governorate" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
                {{ errors.governorate }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
                {{ getTranslatedTitle('registrationPage.detailsStep.districtLabel') }} *
              </label>
              <input v-model="form.district" type="text"
                :placeholder="getTranslatedTitle('registrationPage.detailsStep.districtPlaceholder')"
                class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
                :class="errors.district ? 'border-red-500' : ''">
              <p v-if="errors.district" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
                {{ errors.district }}
              </p>
            </div>
          </div>

          <!-- Marital Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.maritalStatusLabel') }} *
            </label>
            <select v-model="form.marital_status"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.marital_status ? 'border-red-500' : ''">
              <option value="">{{ getTranslatedTitle('registrationPage.detailsStep.maritalStatusPlaceholder') }}</option>
              <option value="single">{{ getTranslatedTitle('registrationPage.detailsStep.maritalStatusSingle') }}</option>
              <option value="married">{{ getTranslatedTitle('registrationPage.detailsStep.maritalStatusMarried') }}</option>
              <option value="divorced">{{ getTranslatedTitle('registrationPage.detailsStep.maritalStatusDivorced') }}</option>
              <option value="widowed">{{ getTranslatedTitle('registrationPage.detailsStep.maritalStatusWidowed') }}</option>
            </select>
            <p v-if="errors.marital_status" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.marital_status }}
            </p>
          </div>

          <!-- Education Level -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.educationLevelLabel') }} *
            </label>
            <select v-model="form.education_level"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.education_level ? 'border-red-500' : ''">
              <option value="">{{ getTranslatedTitle('registrationPage.detailsStep.educationLevelPlaceholder') }}</option>
              <option value="elementary">{{ getTranslatedTitle('registrationPage.detailsStep.educationElementary') }}</option>
              <option value="middle">{{ getTranslatedTitle('registrationPage.detailsStep.educationMiddle') }}</option>
              <option value="high_school">{{ getTranslatedTitle('registrationPage.detailsStep.educationHighSchool') }}</option>
              <option value="diploma">{{ getTranslatedTitle('registrationPage.detailsStep.educationDiploma') }}</option>
              <option value="bachelor">{{ getTranslatedTitle('registrationPage.detailsStep.educationBachelor') }}</option>
              <option value="graduate">{{ getTranslatedTitle('registrationPage.detailsStep.educationGraduate') }}</option>
            </select>
            <p v-if="errors.education_level" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.education_level }}
            </p>
          </div>

          <!-- Employment Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.employmentStatusLabel') }} *
            </label>
            <select v-model="form.employment_status"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.employment_status ? 'border-red-500' : ''">
              <option value="">{{ getTranslatedTitle('registrationPage.detailsStep.employmentStatusPlaceholder') }}</option>
              <option value="student">{{ getTranslatedTitle('registrationPage.detailsStep.employmentStudent') }}</option>
              <option value="government_employee">{{ getTranslatedTitle('registrationPage.detailsStep.employmentGovernment') }}</option>
              <option value="private_employee">{{ getTranslatedTitle('registrationPage.detailsStep.employmentPrivate') }}</option>
              <option value="unemployed">{{ getTranslatedTitle('registrationPage.detailsStep.employmentUnemployed') }}</option>
              <option value="housewife">{{ getTranslatedTitle('registrationPage.detailsStep.employmentHousewife') }}</option>
              <option value="retired">{{ getTranslatedTitle('registrationPage.detailsStep.employmentRetired') }}</option>
            </select>
            <p v-if="errors.employment_status" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.employment_status }}
            </p>
          </div>

          <!-- Profession -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.professionLabel') }}
            </label>
            <input v-model="form.profession" type="text"
              :placeholder="getTranslatedTitle('registrationPage.detailsStep.professionPlaceholder')"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base">
          </div>

          <!-- Monthly Income -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.monthlyIncomeLabel') }} *
            </label>
            <select v-model="form.monthly_income"
              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-green focus:border-transparent text-base"
              :class="errors.monthly_income ? 'border-red-500' : ''">
              <option value="">{{ getTranslatedTitle('registrationPage.detailsStep.monthlyIncomePlaceholder') }}</option>
              <option value="less_than_60k">{{ getTranslatedTitle('registrationPage.detailsStep.incomeLessThan60k') }}</option>
              <option value="61k_to_120k">{{ getTranslatedTitle('registrationPage.detailsStep.income61kTo120k') }}</option>
              <option value="121k_to_200k">{{ getTranslatedTitle('registrationPage.detailsStep.income121kTo200k') }}</option>
              <option value="201k_to_350k">{{ getTranslatedTitle('registrationPage.detailsStep.income201kTo350k') }}</option>
              <option value="more_than_351k">{{ getTranslatedTitle('registrationPage.detailsStep.incomeMoreThan351k') }}</option>
            </select>
            <p v-if="errors.monthly_income" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.monthly_income }}
            </p>
          </div>

          <!-- Platform Purposes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3" :class="isRTL ? 'text-right' : 'text-left'">
              {{ getTranslatedTitle('registrationPage.detailsStep.platformPurposesLabel') }} *
            </label>
            <div class="space-y-2">
              <label v-for="purpose in platformPurposeOptions" :key="purpose.value" class="flex items-center gap-3 p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" :value="purpose.value" v-model="form.platform_purposes"
                  class="w-5 h-5 text-primary-green border-gray-300 rounded focus:ring-primary-green">
                <span class="text-gray-700 text-sm">{{ purpose.label }}</span>
              </label>
            </div>
            <p v-if="errors.platform_purposes" class="text-red-500 text-sm mt-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.platform_purposes }}
            </p>
          </div>

          <!-- Terms and Privacy -->
          <div class="space-y-4 pt-4 border-t border-gray-200">
            <label class="flex items-start gap-3 cursor-pointer">
              <input type="checkbox" v-model="form.terms_accepted"
                class="mt-1 w-5 h-5 text-primary-green border-gray-300 rounded focus:ring-primary-green">
              <span class="text-sm text-gray-700">
                {{ getTranslatedTitle('registrationPage.detailsStep.termsText') }}
                <a href="#" class="text-primary-green underline">{{ getTranslatedTitle('registrationPage.detailsStep.termsLink') }}</a>
              </span>
            </label>
            <p v-if="errors.terms_accepted" class="text-red-500 text-sm" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.terms_accepted }}
            </p>

            <label class="flex items-start gap-3 cursor-pointer">
              <input type="checkbox" v-model="form.privacy_accepted"
                class="mt-1 w-5 h-5 text-primary-green border-gray-300 rounded focus:ring-primary-green">
              <span class="text-sm text-gray-700">
                {{ getTranslatedTitle('registrationPage.detailsStep.privacyText') }}
                <a href="#" class="text-primary-green underline">{{ getTranslatedTitle('registrationPage.detailsStep.privacyLink') }}</a>
              </span>
            </label>
            <p v-if="errors.privacy_accepted" class="text-red-500 text-sm" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.privacy_accepted }}
            </p>

            <label class="flex items-start gap-3 cursor-pointer">
              <input type="checkbox" v-model="form.info_accuracy_confirmed"
                class="mt-1 w-5 h-5 text-primary-green border-gray-300 rounded focus:ring-primary-green">
              <span class="text-sm text-gray-700">
                {{ getTranslatedTitle('registrationPage.detailsStep.infoAccuracyText') }}
              </span>
            </label>
            <p v-if="errors.info_accuracy_confirmed" class="text-red-500 text-sm" :class="isRTL ? 'text-right' : 'text-left'">
              {{ errors.info_accuracy_confirmed }}
            </p>
          </div>

          <div class="flex gap-3 pt-4">
            <button type="button" @click="currentStep = 3"
              class="flex-1 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-bold text-base">
              {{ getTranslatedTitle('registrationPage.detailsStep.back') }}
            </button>
            <button type="submit" :disabled="isSubmitting || !isDetailsFormValid"
              class="flex-1 py-4 bg-primary-green text-white rounded-xl hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-bold text-base">
              <span v-if="!isSubmitting">{{ getTranslatedTitle('registrationPage.detailsStep.submit') }}</span>
              <span v-else class="flex items-center justify-center gap-3" :class="isRTL ? 'flex-row-reverse' : ''">
                <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                {{ getTranslatedTitle('registrationPage.detailsStep.creating') }}
              </span>
            </button>
          </div>
        </form>
      </div>

      <!-- Step 5: Success -->
      <div v-if="currentStep === 5" class="text-center space-y-8 py-8 px-2">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <i class="fas fa-check text-4xl text-primary-green"></i>
        </div>
        
        <h3 class="text-2xl font-bold text-gray-800">{{ getTranslatedTitle('registrationPage.successStep.title') }}</h3>
        <p class="text-gray-600 text-base leading-relaxed max-w-md mx-auto">
          {{ getTranslatedDescription('registrationPage.successStep.message') }}
        </p>

        <!-- Download Buttons -->
        <div class="flex flex-col gap-4 justify-center pt-6 px-4">
          <a href="https://apps.apple.com/app/id1244654624?mt=8" target="_blank" 
             class="flex items-center justify-center gap-4 px-4 py-4 bg-black text-white rounded-xl hover:bg-gray-800 transition-colors"
             :class="isRTL ? 'flex-row-reverse' : ''">
            <i class="fab fa-apple text-2xl"></i>
            <div :class="isRTL ? 'text-right' : 'text-left'">
              <div class="text-xs opacity-80">{{ getTranslatedTitle('registrationPage.successStep.downloadOn') }}</div>
              <div class="font-bold text-base">{{ getTranslatedTitle('registrationPage.successStep.appStore') }}</div>
            </div>
          </a>
          
          <a href="https://play.google.com/store/apps/details?id=com.labayh" target="_blank" 
             class="flex items-center justify-center gap-4 px-4 py-4 bg-black text-white rounded-xl hover:bg-gray-800 transition-colors"
             :class="isRTL ? 'flex-row-reverse' : ''">
            <i class="fab fa-google-play text-2xl"></i>
            <div :class="isRTL ? 'text-right' : 'text-left'">
              <div class="text-xs opacity-80">{{ getTranslatedTitle('registrationPage.successStep.getItOn') }}</div>
              <div class="font-bold text-base">{{ getTranslatedTitle('registrationPage.successStep.googlePlay') }}</div>
            </div>
          </a>
        </div>

        <div class="pt-6">
          <button @click="handleRegistrationSuccess"
            class="px-12 py-4 bg-primary-green text-white rounded-xl hover:bg-opacity-90 transition-all font-bold text-base">
            {{ getTranslatedTitle('registrationPage.successStep.start') }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onBeforeUnmount, nextTick } from 'vue'
import { useNotifications } from '../../../composables/useNotifications'
import { useProfile } from '../../../composables/useProfile'
import { t } from '../../../locales'
import api from '../../../utils/api'

const props = defineProps({
  isPage: {
    type: Boolean,
    default: false
  },
  language: {
    type: String,
    default: 'ar'
  }
})

const emit = defineEmits(['registration-success'])

const { showSuccess, showError } = useNotifications()
const { setUserFromApi } = useProfile()

const currentLanguage = ref(props.language)
watch(() => props.language, (newLanguage) => {
  currentLanguage.value = newLanguage
})

const currentStep = ref(1)
const isSubmitting = ref(false)
const isResending = ref(false)
const resendCounter = ref(0)
const verificationCode = ref<string[]>(Array(6).fill(''))
let resendTimer: ReturnType<typeof setInterval> | null = null

const form = reactive({
  contact_method: 'email', // 'email' or 'phone'
  email: '',
  phone: '',
  name: '',
  password: '',
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
  monthly_income: '',
  platform_purposes: [] as string[],
  terms_accepted: false,
  privacy_accepted: false,
  info_accuracy_confirmed: false
})

const errors = reactive<Record<string, string>>({})

// Check if current language is RTL
const isRTL = computed(() => {
  return props.language === 'ar'
})

// Translation function
const translate = (key: string) => {
  return t(key, currentLanguage.value)
}

const getTranslatedTitle = (key: string) => {
  const translation = t(key, currentLanguage.value)
  return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const getTranslatedDescription = (key: string) => {
  const translation = t(key, currentLanguage.value)
  return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const steps = computed(() => [
  { number: 1, title: translate('registrationPage.steps.contact') },
  { number: 2, title: translate('registrationPage.steps.code') },
  { number: 3, title: translate('registrationPage.steps.info') },
  { number: 4, title: translate('registrationPage.steps.details') }
])

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
const phoneRegex = /^[0-9+\-\s()]+$/
const isEmailValid = computed(() => emailRegex.test(form.email))
const isPhoneValid = computed(() => phoneRegex.test(form.phone) && form.phone.replace(/\D/g, '').length >= 8)
// const isContactValid = computed(() => {
//   if (form.contact_method === 'email') {
//     return isEmailValid.value
//   } else {
//     return isPhoneValid.value
//   }
// })

const isContactValid = computed(() => {
  // ✅ فقط البريد الإلكتروني
  return isEmailValid.value
})

const isCodeComplete = computed(() => verificationCode.value.every(digit => digit !== ''))

// Platform purpose options
const platformPurposeOptions = computed(() => [
  { value: 'information_resources', label: getTranslatedTitle('registrationPage.detailsStep.purposeInformation') },
  { value: 'self_assessment', label: getTranslatedTitle('registrationPage.detailsStep.purposeSelfAssessment') },
  { value: 'psychological_consultation', label: getTranslatedTitle('registrationPage.detailsStep.purposeConsultation') },
  { value: 'electronic_programs', label: getTranslatedTitle('registrationPage.detailsStep.purposePrograms') },
  { value: 'other', label: getTranslatedTitle('registrationPage.detailsStep.purposeOther') }
])

// Validation for details step
const isDetailsFormValid = computed(() => {
  return form.gender &&
    form.date_of_birth &&
    form.country &&
    form.city &&
    form.governorate &&
    form.district &&
    form.marital_status &&
    form.education_level &&
    form.employment_status &&
    form.monthly_income &&
    form.platform_purposes.length > 0 &&
    form.terms_accepted &&
    form.privacy_accepted &&
    form.info_accuracy_confirmed
})

// const validateContact = () => {
//   if (form.contact_method === 'email') {
//     if (form.email && !emailRegex.test(form.email)) {
//       errors.contact = translate('registrationPage.contactStep.emailError')
//     } else {
//       delete errors.contact
//     }
//   } else {
//     if (form.phone && !isPhoneValid.value) {
//       errors.contact = translate('registrationPage.contactStep.phoneError')
//     } else {
//       delete errors.contact
//     }
//   }
// }

// const validateContact = () => {
//   if (form.email && !emailRegex.test(form.email)) {
//     errors.contact = translate('registrationPage.contactStep.emailError')
//   } else {
//     delete errors.contact
//   }
// }

const validateContact = () => {
  if (form.email) {
    // ✅ التحقق من الصيغة الأساسية
    if (!emailRegex.test(form.email)) {
      errors.contact = translate('registrationPage.contactStep.emailError')
      return
    }
    
    // ✅ التحقق من وجود نقطة بعد @
    const parts = form.email.split('@')
    if (parts.length !== 2 || !parts[1].includes('.')) {
      errors.contact = 'يرجى إدخال بريد إلكتروني صحيح (مثال: name@domain.com)'
      return
    }
    
    // ✅ التحقق من أن النطاق طويل بما يكفي
    const domainParts = parts[1].split('.')
    if (domainParts.length < 2 || domainParts[domainParts.length - 1].length < 2) {
      errors.contact = 'يرجى إدخال بريد إلكتروني صحيح (مثال: name@domain.com)'
      return
    }
    
    delete errors.contact
  } else {
    delete errors.contact
  }
}


////
// const handleContactSubmit = async () => {
//   validateContact()
//   if (!isContactValid.value) return

//   // نقل المستخدم مباشرةً إلى خطوة إدخال الرمز
//   currentStep.value = 2

//   isSubmitting.value = true
//   try {
//     if (form.contact_method === 'email') {
//       await api.post('/registration/email/send-code', { email: form.email })
//       showSuccess(translate('registrationPage.success.emailSent'))
//     } else {
//       // TODO: إضافة endpoint لإرسال رمز التحقق عبر SMS
//       await api.post('/registration/phone/send-code', { phone: form.phone })
//       showSuccess(translate('registrationPage.success.phoneSent'))
//     }
//     startResendCounter()
//   } catch (error) {
//     // إظهار توست مع سبب المشكلة إن توفر من الخادم
//     showError(error, translate('registrationPage.errors.sendCode'))
//   } finally {
//     isSubmitting.value = false
//   }
// }

// const handleContactSubmit = async () => {
//   validateContact()
//   if (!isContactValid.value) return

//   isSubmitting.value = true
//   try {
//     // ✅ إرسال رمز التحقق إلى البريد الإلكتروني
//     await api.post('/registration/email/send-code', { email: form.email })
    
//     showSuccess(translate('registrationPage.success.emailSent'))
    
//     // ✅ الانتقال إلى خطوة إدخال الرمز
//     currentStep.value = 2
//     startResendCounter()
    
//   } catch (error: any) {
//     const message = error.response?.data?.message || translate('registrationPage.errors.sendCode')
//     showError(message)
//   } finally {
//     isSubmitting.value = false
//   }
// }


const handleContactSubmit = async () => {
  validateContact()
  if (!isContactValid.value) return

  isSubmitting.value = true
  try {
    // ✅ إرسال البريد الإلكتروني للتحقق (مع التحقق من الصحة)
    const response = await api.post('/registration/email/send-code', { 
      email: form.email 
    })
    
    if (response.data.success) {
      showSuccess(translate('registrationPage.success.emailSent'))
      currentStep.value = 2
      startResendCounter()
    }
    
  } catch (error: any) {
    // ✅ عرض رسالة الخطأ من الخادم
    const message = error.response?.data?.message || translate('registrationPage.errors.sendCode')
    
    // ✅ إذا كان الخطأ بسبب البريد الإلكتروني
    if (error.response?.data?.errors?.email) {
      errors.contact = error.response.data.errors.email[0]
    } else {
      showError(message)
    }
  } finally {
    isSubmitting.value = false
  }
}

const handleCodeInput = async (index: number, event: Event) => {
  const target = event.target as HTMLInputElement
  const value = target.value.replace(/\D/g, '')
  
  if (value) {
    verificationCode.value[index] = value.slice(-1)
    
    // الانتقال للحقل التالي تلقائياً
    if (index < verificationCode.value.length - 1) {
      await nextTick()
      const inputs = document.querySelectorAll<HTMLInputElement>('input[type="text"][inputmode="numeric"]')
      if (inputs[index + 1]) {
        inputs[index + 1].focus()
        inputs[index + 1].select()
      }
    }
  } else {
    verificationCode.value[index] = ''
  }
  
  // مسح رسالة الخطأ عند الكتابة
  delete errors.code
}

const handleCodeKeydown = (index: number, event: KeyboardEvent) => {
  // مسح رسالة الخطأ عند أي ضغطة
  delete errors.code
  
  if (event.key === 'Backspace') {
    if (!verificationCode.value[index] && index > 0) {
      // إذا كان الحقل فارغاً، انتقل للحقل السابق
      const target = event.target as HTMLInputElement
      const inputs = document.querySelectorAll<HTMLInputElement>('input[type="text"][inputmode="numeric"]')
      if (inputs[index - 1]) {
        inputs[index - 1].focus()
        inputs[index - 1].select()
      }
    } else if (verificationCode.value[index]) {
      // إذا كان الحقل يحتوي على قيمة، امسحها
      verificationCode.value[index] = ''
    }
  } else if (event.key === 'ArrowLeft' && index > 0) {
    event.preventDefault()
    const inputs = document.querySelectorAll<HTMLInputElement>('input[type="text"][inputmode="numeric"]')
    if (inputs[index - 1]) {
      inputs[index - 1].focus()
      inputs[index - 1].select()
    }
  } else if (event.key === 'ArrowRight' && index < verificationCode.value.length - 1) {
    event.preventDefault()
    const inputs = document.querySelectorAll<HTMLInputElement>('input[type="text"][inputmode="numeric"]')
    if (inputs[index + 1]) {
      inputs[index + 1].focus()
      inputs[index + 1].select()
    }
  } else if (event.key === 'v' && (event.ctrlKey || event.metaKey)) {
    // السماح باللصق (سيتم التعامل معه في handlePaste)
    return
  } else if (!/^\d$/.test(event.key) && !event.ctrlKey && !event.metaKey) {
    // منع إدخال أي شيء غير أرقام
    event.preventDefault()
  }
}

// إضافة دالة handlePaste للتعامل مع لصق الرمز
const handlePaste = (event: ClipboardEvent) => {
  event.preventDefault()
  const pastedText = event.clipboardData?.getData('text') || ''
  const numbers = pastedText.replace(/\D/g, '').slice(0, 6)
  
  if (numbers.length === 6) {
    // لصق الرمز كاملاً
    verificationCode.value = numbers.split('')
    
    // نقل التركيز للحقل الأخير
    nextTick(() => {
      const inputs = document.querySelectorAll<HTMLInputElement>('input[type="text"][inputmode="numeric"]')
      if (inputs[5]) {
        inputs[5].focus()
      }
    })
    
    // مسح رسالة الخطأ
    delete errors.code
  }
}

// const handleCodeSubmit = async () => {
//   if (!isCodeComplete.value) {
//     errors.code = translate('registrationPage.errors.incompleteCode')
    
//     // تأثير اهتزاز للحقول
//     const inputs = document.querySelectorAll('input[type="text"][inputmode="numeric"]')
//     inputs.forEach(input => {
//       input.classList.add('shake')
//       setTimeout(() => input.classList.remove('shake'), 500)
//     })
    
//     return
//   }

//   // نقل المستخدم إلى الخطوة التالية مؤقتاً
//   currentStep.value = 3

//   isSubmitting.value = true
//   try {
//     const code = verificationCode.value.join('')
    
//     if (form.contact_method === 'email') {
//       await api.post('/registration/email/verify-code', {
//         email: form.email,
//         code: code
//       })
//       showSuccess(translate('registrationPage.success.emailVerified'))
//     } else {
//       await api.post('/registration/phone/verify-code', {
//         phone: form.phone,
//         code: code
//       })
//       showSuccess(translate('registrationPage.success.phoneVerified'))
//     }
//     clearResendTimer()
//   } catch (error: any) {
//     // إذا فشل التحقق، ارجع للخطوة 2
//     currentStep.value = 2
    
//     const message = error.response?.data?.message || translate('registrationPage.errors.invalidCode')
//     errors.code = message
//     showError(message)
    
//     // تفريغ الحقول لإعادة الإدخال
//     verificationCode.value = Array(6).fill('')
    
//     // التركيز على الحقل الأول
//     nextTick(() => {
//       const firstInput = document.querySelector<HTMLInputElement>('input[type="text"][inputmode="numeric"]')
//       if (firstInput) {
//         firstInput.focus()
//       }
//     })
//   } finally {
//     isSubmitting.value = false
//   }
// }

// في script - تعديل handleCodeSubmit
const handleCodeSubmit = async () => {
  if (!isCodeComplete.value) {
    errors.code = translate('registrationPage.errors.incompleteCode')
    
    const inputs = document.querySelectorAll('input[type="text"][inputmode="numeric"]')
    inputs.forEach(input => {
      input.classList.add('shake')
      setTimeout(() => input.classList.remove('shake'), 500)
    })
    
    return
  }

  isSubmitting.value = true
  try {
    const code = verificationCode.value.join('')
    
    // ✅ فقط التحقق عبر البريد الإلكتروني
    await api.post('/registration/email/verify-code', {
      email: form.email,
      code: code
    })
    
    showSuccess(translate('registrationPage.success.emailVerified'))
    clearResendTimer()
    
    // ✅ الانتقال إلى الخطوة التالية بعد التحقق
    currentStep.value = 3
    
  } catch (error: any) {
    const message = error.response?.data?.message || translate('registrationPage.errors.invalidCode')
    errors.code = message
    showError(message)
    
    // تفريغ الحقول لإعادة الإدخال
    verificationCode.value = Array(6).fill('')
    
    nextTick(() => {
      const firstInput = document.querySelector<HTMLInputElement>('input[type="text"][inputmode="numeric"]')
      if (firstInput) {
        firstInput.focus()
      }
    })
  } finally {
    isSubmitting.value = false
  }
}

const handleInfoSubmit = async () => {
  // التحقق من الاسم
  if (!form.name.trim()) {
    errors.name = translate('registrationPage.infoStep.nameRequired')
  } else {
    delete errors.name
  }

  // التحقق من كلمة المرور
  if (!form.password.trim()) {
    errors.password = translate('registrationPage.infoStep.passwordRequired')
  } else if (form.password.length < 6) {
    errors.password = translate('registrationPage.infoStep.passwordMin')
  } else {
    delete errors.password
  }

  if (errors.name || errors.password) return

  // الانتقال إلى الخطوة التالية (المعلومات التفصيلية)
  currentStep.value = 4
}

const handleDetailsSubmit = async () => {
  // التحقق من جميع الحقول المطلوبة
  if (!form.gender) {
    errors.gender = translate('registrationPage.detailsStep.genderRequired')
  } else {
    delete errors.gender
  }

  if (!form.date_of_birth) {
    errors.date_of_birth = translate('registrationPage.detailsStep.dateOfBirthRequired')
  } else {
    delete errors.date_of_birth
  }

  if (!form.country) {
    errors.country = translate('registrationPage.detailsStep.countryRequired')
  } else {
    delete errors.country
  }

  if (!form.city) {
    errors.city = translate('registrationPage.detailsStep.cityRequired')
  } else {
    delete errors.city
  }

  if (!form.governorate) {
    errors.governorate = translate('registrationPage.detailsStep.governorateRequired')
  } else {
    delete errors.governorate
  }

  if (!form.district) {
    errors.district = translate('registrationPage.detailsStep.districtRequired')
  } else {
    delete errors.district
  }

  if (!form.marital_status) {
    errors.marital_status = translate('registrationPage.detailsStep.maritalStatusRequired')
  } else {
    delete errors.marital_status
  }

  if (!form.education_level) {
    errors.education_level = translate('registrationPage.detailsStep.educationLevelRequired')
  } else {
    delete errors.education_level
  }

  if (!form.employment_status) {
    errors.employment_status = translate('registrationPage.detailsStep.employmentStatusRequired')
  } else {
    delete errors.employment_status
  }

  if (!form.monthly_income) {
    errors.monthly_income = translate('registrationPage.detailsStep.monthlyIncomeRequired')
  } else {
    delete errors.monthly_income
  }

  if (!form.platform_purposes || form.platform_purposes.length === 0) {
    errors.platform_purposes = translate('registrationPage.detailsStep.platformPurposesRequired')
  } else {
    delete errors.platform_purposes
  }

  if (!form.terms_accepted) {
    errors.terms_accepted = translate('registrationPage.detailsStep.termsRequired')
  } else {
    delete errors.terms_accepted
  }

  if (!form.privacy_accepted) {
    errors.privacy_accepted = translate('registrationPage.detailsStep.privacyRequired')
  } else {
    delete errors.privacy_accepted
  }

  if (!form.info_accuracy_confirmed) {
    errors.info_accuracy_confirmed = translate('registrationPage.detailsStep.infoAccuracyRequired')
  } else {
    delete errors.info_accuracy_confirmed
  }

  if (Object.keys(errors).length > 0) return
  
  isSubmitting.value = true
  try {
    // استدعاء API لحفظ المستخدم في قاعدة البيانات (مسار الفرونتند)
    const response = await api.post('/frontend/register', {
      name: form.name,
      email: form.email,
      phone: null, // ❌ تعطيل الهاتف مؤقتاً
    //   email: form.contact_method === 'email' ? form.email : null,
    //   phone: form.contact_method === 'phone' ? form.phone : null,
      password: form.password,
      gender: form.gender,
      date_of_birth: form.date_of_birth,
      country: form.country,
      city: form.city,
      governorate: form.governorate,
      district: form.district,
      marital_status: form.marital_status,
      education_level: form.education_level,
      employment_status: form.employment_status,
      profession: form.profession || null,
      monthly_income: form.monthly_income,
      platform_purposes: form.platform_purposes,
      terms_accepted: form.terms_accepted,
      privacy_accepted: form.privacy_accepted,
      info_accuracy_confirmed: form.info_accuracy_confirmed,
    })

    const data = response.data?.data || {}
    const user = data.user
    const token = data.token

    if (user) {
      // حفظ المستخدم في useProfile/localStorage لاستخدامه في الواجهة
      setUserFromApi(user)
    }

    if (token) {
      // حفظ توكن العميل لاستخدامه في استعلامات /user, /appointments, /sessions
      localStorage.setItem('frontend_token', token)
    }

    currentStep.value = 5
    showSuccess(translate('registrationPage.success.accountCreated'))
    
  } catch (error) {
    // إظهار رسالة من الخادم إن وجدت (مثل بريد مستخدم من قبل)
    showError(error, translate('registrationPage.errors.createAccount'))
  } finally {
    isSubmitting.value = false
  }
}

const handleRegistrationSuccess = () => {
  emit('registration-success')
}

const clearResendTimer = () => {
  if (resendTimer) {
    clearInterval(resendTimer)
    resendTimer = null
  }
}

const startResendCounter = () => {
  resendCounter.value = 120
  clearResendTimer()
  resendTimer = setInterval(() => {
    resendCounter.value--
    if (resendCounter.value <= 0) {
      clearResendTimer()
    }
  }, 1000)
}

const resendVerificationCode = async () => {
  if (resendCounter.value > 0 || isResending.value) return
  
  isResending.value = true
  try {
    await handleContactSubmit()
    showSuccess(translate('registrationPage.success.codeResent'))
  } catch (error) {
    showError(error, translate('registrationPage.errors.resendFailed'))
  } finally {
    isResending.value = false
  }
}

const formatTime = (seconds: number) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
}

watch(() => props.language, () => {
  // trigger reactive updates
})

onBeforeUnmount(() => {
  clearResendTimer()
})
</script>

<style scoped>
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
  20%, 40%, 60%, 80% { transform: translateX(2px); }
}

.shake {
  animation: shake 0.5s ease-in-out;
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

/* تحسينات للجوال */
@media (max-width: 640px) {
  .steps-container {
    padding: 0 8px;
  }
  
  .verification-inputs {
    gap: 8px;
  }
  
  .verification-input {
    width: 44px;
    height: 52px;
    font-size: 20px;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  
  .button-group {
    flex-direction: column;
    gap: 12px;
  }
  
  .download-buttons {
    flex-direction: column;
  }
  
  .download-button {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .verification-input {
    width: 40px;
    height: 48px;
    font-size: 18px;
  }
  
  h1 {
    font-size: 24px;
  }
  
  h3 {
    font-size: 20px;
  }
  
  .success-icon {
    width: 80px;
    height: 80px;
  }
}
</style>