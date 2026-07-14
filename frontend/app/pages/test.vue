<script setup lang="ts">
const auth = useAuth()

watchEffect(() => {
  console.log(auth.token.value)
  console.log(auth.isAuthenticated.value)
})
</script>

<template>
  <div>
    <button @click="auth.setToken('123456')">
      Login
    </button>

    <button @click="auth.logout()">
      Logout
    </button>
  </div>
</template>
