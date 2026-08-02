<template>
  <div class="flex flex-wrap justify-center gap-3 mt-3 text-3xl">
    <a
      v-for="icon in socialIcons"
      :key="icon.icon"
      :href="icon.link"
      :title="icon.title"
      class="rounded-full text-[#00a9b7] w-12 h-12 flex items-center justify-center hover:scale-110 transition"
    >
      <i :class="icon.icon"></i>
    </a>
  </div>
</template>

<script setup>
const socialIcons = [
  { icon: 'fab fa-facebook-f', link: '#', title: 'Facebook' },
  { icon: 'fab fa-twitter', link: '#', title: 'Twitter' },
  { icon: 'fab fa-linkedin-in', link: '#', title: 'LinkedIn' },
  { icon: 'fab fa-instagram', link: '#', title: 'Instagram' },
  { icon: 'fab fa-youtube', link: '#', title: 'YouTube' }
]
</script>
