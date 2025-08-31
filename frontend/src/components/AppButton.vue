<template>
  <button
    :class="[
      'py-2.5 px-6 rounded-lg font-bold min-w-[160px] font-montserrat transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed',
      variantClasses
    ]"
    :type="type"
    @click="$emit('click', $event)"
    :disabled="disabled"
  >
    <slot>{{ label }}</slot>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, default: '' },
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' }, // "primary" or "secondary"
  disabled: { type: Boolean, default: false },
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-[oklch(45.229%_0.035_264.131)] text-white border-none hover:bg-[oklch(42%_0.035_264.131)] focus:ring-[oklch(45.229%_0.035_264.131)]'
    case 'secondary':
      return 'bg-transparent text-white border border-white/20 hover:bg-white/10 focus:ring-white'
    default:
      return 'bg-gray-500 text-white'
  }
})
</script>
