<template>
	<div class="h-2 w-full overflow-hidden rounded-full bg-tertiary">
		<div class="h-full bg-brand-500" :style="{ width: pct + '%' }"></div>
	</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props { value: number; max?: number }
const props = withDefaults(defineProps<Props>(), { max: 100 });
const pct = computed(() => Math.min(100, Math.round((props.value / props.max) * 100)));
</script>