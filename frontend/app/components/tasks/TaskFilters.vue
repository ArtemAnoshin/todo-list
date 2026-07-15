<script setup lang="ts">
import { TaskStatusOptions } from '~/types/task-status'

const search = defineModel<string>('search', {
  default: ''
})

const status = defineModel<string>('status', {
  default: ''
})

const emit = defineEmits<{
  submit: []
}>()
</script>

<template>
  <div
    class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
  >
    <div class="flex flex-col gap-4 md:flex-row">
      <input
        v-model="search"
        type="text"
        placeholder="Поиск по названию..."
        class="flex-1 rounded-lg border border-gray-300 px-4 py-2 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
      >

      <select
        v-model="status"
        class="rounded-lg border border-gray-300 px-4 py-2 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
      >
        <option
          v-for="option in TaskStatusOptions"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>

      <button
        class="rounded-lg bg-emerald-600 px-5 py-2 font-medium text-white transition hover:bg-emerald-700"
        @click="emit('submit')"
      >
        Найти
      </button>
    </div>
  </div>
</template>
