<script setup lang="ts">
import { taskService } from '~/services/tasks'
import type { Task } from '~/types/task'
import { TaskStatus } from '~/types/task-status'

// Определяем входящие пропсы
defineProps<{
  tasks: Task[]
}>()

// Определяем возможные события, которые может генерировать компонент
const emit = defineEmits<{
  // Событие, которое будет сгенерировано после успешного удаления задачи
  'task-deleted': [deletedTaskId: number]
}>()

// Состояние для отслеживания процесса удаления конкретной задачи
// Используем Map для возможности одновременного удаления нескольких задач
const deletingTasks = ref(new Map<number, boolean>())

// Состояние для хранения глобальной ошибки (если есть)
const globalError = ref<string | null>(null)

// Функция для удаления задачи
async function deleteTask(id: number) {
  // Проверка подтверждения (опционально)
  if (!confirm('Вы уверены, что хотите удалить эту задачу?')) {
    return
  }

  // Устанавливаем состояние загрузки для конкретной задачи
  deletingTasks.value.set(id, true)
  // Обновляем реактивность Map
  deletingTasks.value = new Map(deletingTasks.value)

  // Сбрасываем предыдущую глобальную ошибку
  globalError.value = null

  try {
    // Выполняем запрос на удаление
    await taskService.destroy(id)

    // После успешного удаления генерируем событие
    emit('task-deleted', id)

  } catch (error: any) {
    console.error('Ошибка при удалении задачи:', error)

    // Проверяем наличие структурированных ошибок в ответе
    if (error.data?.errors) {
      // Если ошибки - массив строк
      if (Array.isArray(error.data.errors)) {
        globalError.value = error.data.errors.join(', ')
      } else if (typeof error.data.errors === 'string') {
        // Если ошибка - одна строка
        globalError.value = error.data.errors
      } else {
        // Для других форматов ошибок
        globalError.value = JSON.stringify(error.data.errors)
      }
    } else if (error.message) {
      // Если есть общее сообщение об ошибке
      globalError.value = error.message
    } else {
      // Общее сообщение об ошибке по умолчанию
      globalError.value = 'Произошла ошибка при удалении задачи.'
    }
  } finally {
    // Убираем индикатор загрузки для этой задачи
    deletingTasks.value.delete(id)
    deletingTasks.value = new Map(deletingTasks.value)
  }
}
</script>

<template>
  <div class="space-y-4">
    <!-- Блок для отображения глобальной ошибки -->
    <div
      v-if="globalError"
      class="bg-red-50 border-l-4 border-red-400 p-4 mb-4"
    >
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">{{ globalError }}</p>
        </div>
      </div>
    </div>

    <!-- Список задач -->
    <div
      v-for="task in tasks"
      :key="task.id"
      class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md relative"
    >
      <!-- Индикатор загрузки для конкретной задачи -->
      <div
        v-if="deletingTasks.get(task.id)"
        class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center z-10 rounded-xl"
      >
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
      </div>

      <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ task.title }}
          </h3>

          <p class="mt-2 text-sm text-gray-600">
            {{ task.description }}
          </p>

          <p class="mt-3 text-sm text-gray-500">
            <span class="font-medium">Дедлайн:</span>
            {{ task.due_date }}
          </p>
        </div>

        <span
          class="rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap"
          :class="{
            'bg-gray-100 text-gray-700': task.status === TaskStatus.PENDING,
            'bg-yellow-100 text-yellow-800': task.status === TaskStatus.IN_PROGRESS,
            'bg-green-100 text-green-700': task.status === TaskStatus.COMPLETED
          }"
        >
          {{ task.status }}
        </span>
      </div>

      <div class="mt-5 flex justify-end gap-2">
        <NuxtLink
          :to="`/tasks/${task.id}`"
          class="rounded-lg border border-blue-500 px-4 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50 disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="deletingTasks.get(task.id)"
        >
          ✏️ Редактировать
        </NuxtLink>

        <button
          type="button"
          class="rounded-lg border border-red-500 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="deletingTasks.get(task.id)"
          @click="deleteTask(task.id)"
        >
          <!-- Текст кнопки зависит от состояния загрузки для этой задачи -->
          {{ deletingTasks.get(task.id) ? 'Удаляем...' : '🗑 Удалить' }}
        </button>
      </div>
    </div>
  </div>
</template>
