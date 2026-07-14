<script setup lang="ts">
import { taskService } from '~/services/tasks'
import type { CreateTaskRequest } from '~/types/task'

definePageMeta({
  middleware: 'auth'
})

const loading = ref(false)
const errors = ref<Record<string, string[]>>({})

async function onSubmit(data: CreateTaskRequest) {
  loading.value = true
  errors.value = {}

  try {
    await taskService.create(data)

    await navigateTo('/')
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="mx-auto max-w-2xl">
    <h1 class="mb-6 text-2xl font-bold">
      Новая задача
    </h1>

    <TasksTaskForm
      :loading="loading"
      :errors="errors"
      @submit="onSubmit"
    />
  </div>
</template>
