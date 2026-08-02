<template>
	<div class="card">
		<div v-if="$slots.header" class="mb-3 flex items-center justify-between gap-2">
			<div class="font-medium"><slot name="header" /></div>
			<div v-if="$slots.actions" class="flex items-center gap-2"><slot name="actions" /></div>
		</div>
		<slot />
	</div>
</template>
<script setup lang="ts"></script>
