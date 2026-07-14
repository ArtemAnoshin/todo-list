<script setup lang="ts">
import { taskService } from '~/services/tasks'
import type { Task, CreateTaskRequest } from '~/types/task'

definePageMeta({
  middleware: 'auth'
})

const route = useRoute()

const id = Number(route.params.id)

const loading = ref(false)
const errors = ref<Record<string, string[]>>({})
const task = ref<Task | null>(null)

onMounted(async () => {
  loading.value = true

  try {
    task.value = await taskService.show(id)
  } finally {
    loading.value = false
  }
})

async function updateTask(data: CreateTaskRequest) {
  loading.value = true
  errors.value = {}

  try {
    await taskService.update(id, data)

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
  <div class="mx-auto max-w-2xl pt-6">
    <h1 class="mb-6 text-2xl font-bold">
      Редактирование задачи
    </h1>

    <div
      v-if="loading && !task"
      class="rounded-lg border border-gray-200 p-6 text-center text-gray-500"
    >
      Загрузка...
    </div>

    <TasksTaskForm
      v-else-if="task"
      :initial-values="task"
      :loading="loading"
      :errors="errors"
      @submit="updateTask"
    />
  </div>
</template>
