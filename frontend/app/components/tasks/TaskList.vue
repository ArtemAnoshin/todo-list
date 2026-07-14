<script setup lang="ts">
import type { Task } from '~/types/task'

defineProps<{
  tasks: Task[]
}>()
</script>

<template>
  <div>
    <div
      v-for="task in tasks"
      :key="task.id"
      class="task"
    >
      <h3>{{ task.title }}</h3>

      <p>{{ task.description }}</p>

      <small>
        {{ task.status }}
      </small>
    </div>
  </div>
</template>
