<script setup lang="ts">
import { TaskStatus, TaskStatusOptions } from '~/types/task-status'
import type { CreateTaskRequest } from '~/types/task'

const props = defineProps<{
  loading?: boolean
  errors?: Record<string, string[]>
}>()

const localError = ref('')

const emit = defineEmits<{
  submit: [CreateTaskRequest]
}>()

const form = reactive<CreateTaskRequest>({
  title: '',
  description: '',
  due_date: null,
  status: TaskStatus.PENDING
})

function onSubmit() {
  localError.value = ''

  if (form.title.trim().length < 3) {
    localError.value = 'Минимум 3 символа'
    return
  }

  emit('submit', { ...form })
}
</script>

<template>
  <form
    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
    @submit.prevent="onSubmit"
  >
    <div class="space-y-5">
      <div
        v-if="localError"
        class="rounded-lg bg-red-50 p-3 text-sm text-red-600">
        {{ localError }}
      </div>

      <div
        v-if="errors?.title"
        class="rounded-lg bg-red-50 p-3 text-sm text-red-600">
        {{ errors.title[0] }}
      </div>

      <div>
        <label
          for="title"
          class="mb-2 block text-sm font-medium text-gray-700"
        >
          Название
        </label>

        <input
          id="title"
          v-model="form.title"
          type="text"
          placeholder="Введите название задачи"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
        >
      </div>

      <div>
        <label
          for="description"
          class="mb-2 block text-sm font-medium text-gray-700"
        >
          Описание
        </label>

        <textarea
          id="description"
          v-model="form.description"
          rows="5"
          placeholder="Введите описание задачи"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
        />
      </div>

      <div class="grid gap-5 md:grid-cols-2">
        <div>
          <label
            for="due-date"
            class="mb-2 block text-sm font-medium text-gray-700"
          >
            Дедлайн
          </label>

          <input
            id="due-date"
            v-model="form.due_date"
            type="date"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
          >
        </div>

        <div>
          <label
            for="status"
            class="mb-2 block text-sm font-medium text-gray-700"
          >
            Статус
          </label>

          <select
            id="status"
            v-model="form.status"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
          >
            <option
              v-for="status in TaskStatusOptions"
              :key="status.value"
              :value="status.value"
            >
              {{ status.label }}
            </option>
          </select>
        </div>
      </div>

      <div class="flex justify-end">
        <button
          :disabled="loading"
          class="rounded-lg bg-blue-600 px-6 py-2 font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-300"
        >
          {{ loading ? 'Создание...' : 'Создать задачу' }}
        </button>
      </div>
    </div>
  </form>
</template>
