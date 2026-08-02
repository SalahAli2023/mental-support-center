<template>
	<div class="card flex items-center gap-4">
		<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent-500/20 text-accent-500">
			<slot name="icon" />
		</div>
		<div class="min-w-0">
			<div class="text-xs text-secondary">{{ label }}</div>
			<div class="truncate text-2xl font-semibold text-primary">{{ value }}</div>
		</div>
		<div class="ms-auto text-xs" :class="deltaClass">{{ delta }}</div>
	</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props { label: string; value: string | number; delta?: string; positive?: boolean }
const props = withDefaults(defineProps<Props>(), { delta: '', positive: true });
const deltaClass = computed(() => props.positive ? 'text-emerald-600' : 'text-rose-600');
</script>