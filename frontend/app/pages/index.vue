<script setup lang="ts">
const auth = useAuth()
</script>

<template>
  <pre>{{ auth.user }}</pre>
</template>
