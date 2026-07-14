<script setup lang="ts">
import { taskService } from '~/services/tasks'
import type { TaskFilters } from '~/types/task'

definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const router = useRouter()

const successMessage = ref('')

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
  <div class="pt-6">
    <p v-if="successMessage">
      {{ successMessage }}
    </p>

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold">
        Мои задачи
      </h1>

      <NuxtLink
        to="/tasks/create"
        class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700"
      >
        + Добавить задачу
      </NuxtLink>
    </div>

    <TasksTaskFilters
      v-model:search="filters.search"
      v-model:status="filters.status"
      @submit="applyFilters"
    />

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
