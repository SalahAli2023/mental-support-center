<template>
	<button
		:class="classes"
		:disabled="disabled"
		v-bind="$attrs"
	>
		<slot />
	</button>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
	variant?: 'primary' | 'secondary' | 'ghost' | 'outline';
	size?: 'sm' | 'md' | 'lg';
	disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
	variant: 'primary',
	size: 'md',
	disabled: false,
});

const classes = computed(() => {
	const base = 'btn';
	const size = props.size === 'sm' ? 'px-2 py-1 text-xs' : props.size === 'lg' ? 'px-4 py-2 text-base' : 'px-3 py-2 text-sm';
	const variants: Record<string, string> = {
		primary: 'bg-brand-500 text-white hover:bg-[#8FAE2F] focus-visible:ring-brand-500',
		secondary: 'bg-accent-500 text-white hover:bg-[#C28E86] focus-visible:ring-accent-500',
		ghost: 'hover:bg-gray-100 dark:hover:bg-gray-800',
		outline: 'border border-gray-300 bg-transparent hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800',
	};
	return [base, size, variants[props.variant]].join(' ');
});
</script>

<style scoped>
</style>
