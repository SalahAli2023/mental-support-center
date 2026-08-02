<template>
  <div>
    <Header />
    
    <!-- الهيرو الرئيسي -->
    <AboutHero />
    
    <!-- قسم نظرة عامة -->
    <AboutOverview />
    
    <!-- قسم الرؤية والرسالة -->
    <VisionMissionSection />
    
    <!-- قسم الأهداف -->
    <ObjectivesSection />
    
    <!-- قسم الإحصائيات -->
    <StatisticsSection />
    
    <!-- قسم آراء العملاء -->
    <TestimonialsSection />
    
    <!-- قسم الدعوة للإجراء -->
    <!-- <CTASection /> -->
    
    <Footer />
  </div>
</template>

<script setup>
import Header from '@/components/frontend/layouts/header.vue'
import Footer from '@/components/frontend/layouts/footer.vue'
import AboutHero from '@/components/frontend/about/AboutHero.vue'
import AboutOverview from '@/components/frontend/about/AboutOverview.vue'
import VisionMissionSection from '@/components/frontend/about/VisionMissionSection.vue'
import ObjectivesSection from '@/components/frontend/about/ObjectivesSection.vue'
import StatisticsSection from '@/components/frontend/about/StatisticsSection.vue'
import TestimonialsSection from '@/components/frontend/about/TestimonialsSection.vue'
// import CTASection from '@/components/frontend/about/CTASection.vue'
</script>