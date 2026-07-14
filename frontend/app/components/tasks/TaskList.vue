<script setup lang="ts">
import type { Task } from '~/types/task'
import { TaskStatus } from '~/types/task-status'

defineProps<{
  tasks: Task[]
}>()
</script>

<template>
  <div class="space-y-4">
    <div
      v-for="task in tasks"
      :key="task.id"
      class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md"
    >
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
          class="rounded-lg border border-blue-500 px-4 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50"
        >
          ✏️ Редактировать
        </NuxtLink>

        <button
          class="rounded-lg border border-red-500 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 cursor-pointer"
        >
          🗑 Удалить
        </button>
      </div>
    </div>
  </div>
</template>
