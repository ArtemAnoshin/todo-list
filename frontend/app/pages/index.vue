<script setup lang="ts">
import { taskService } from '~/services/tasks'
import type { TaskFilters } from '~/types/task'
import { TaskStatusOptions } from '~/types/task-status'

definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const router = useRouter()

const filters = reactive<TaskFilters>({
  page: Number(route.query.page ?? 1),

  per_page: Number(route.query.per_page ?? 15),

  search: String(route.query.search ?? ''),

  status: String(route.query.status ?? ''),

  due_date_from: String(route.query.due_date_from ?? ''),

  due_date_to: String(route.query.due_date_to ?? ''),

  sort_by: String(route.query.sort_by ?? 'created_at'),

  sort_direction:
    (route.query.sort_direction as 'asc' | 'desc') ?? 'desc'
})

const {
  data,
  pending,
  error,
  refresh
} = await useAsyncData(
  'tasks',
  () => taskService.index(filters)
)

async function goToPage(page: number) {
  if (!data.value) return

  if (page < 1) return

  if (page > data.value.last_page) return

  filters.page = page

  await applyFilters()
}

async function applyFilters() {
  await router.replace({
    query: {
      ...filters
    }
  })

  refresh()
}
</script>

<template>
  <div>
    <h1>Задачи</h1>

    <input
      v-model="filters.search"
      type="text"
      placeholder="Поиск по названию..."
    >

    <select v-model="filters.status">
      <option
        v-for="option in TaskStatusOptions"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>

    <button @click="applyFilters()">
      Найти
    </button>

    <div v-if="pending">
      Загрузка...
    </div>

    <div v-else-if="error">
      Не удалось загрузить задачи.
    </div>

    <div v-else-if="!data || data.data.length === 0">
      У вас пока нет задач.
    </div>

    <TasksTaskList
      :tasks="data.data"
    />

    <div v-if="data">
      Найдено задач: {{ data.total }}
    </div>

    <div v-if="data && data.last_page > 1">
      <button
        :disabled="filters.page === 1"
        @click="goToPage(filters.page! - 1)"
      >
        ← Назад
      </button>

      <span>
        Страница {{ data.current_page }} из {{ data.last_page }}
      </span>

      <button
        :disabled="filters.page === data.last_page"
        @click="goToPage(filters.page! + 1)"
      >
        Вперед →
      </button>
    </div>
  </div>
</template>
