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

  form.title = ''
  form.description = ''
  form.due_date = null
  form.status = TaskStatus.PENDING
}
</script>

<template>
  <form @submit.prevent="onSubmit">
    <div>
      <p v-if="localError">
        {{ localError }}
      </p>

      <p v-if="errors?.title">
        {{ errors.title[0] }}
      </p>
      <input
        v-model="form.title"
        placeholder="Название"
      >
    </div>

    <div>
      <textarea
        v-model="form.description"
        placeholder="Описание"
      />
    </div>

    <div>
      <input
        v-model="form.due_date"
        type="date"
      >
    </div>

    <div>
      <select v-model="form.status">
        <option
          v-for="status in TaskStatusOptions"
          :key="status.value"
          :value="status.value"
        >
          {{ status.label }}
        </option>
      </select>
    </div>

    <button
      :disabled="loading"
    >
      {{ loading ? 'Создание...' : 'Создать задачу' }}
    </button>
  </form>
</template>
