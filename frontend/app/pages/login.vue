<script setup lang="ts">
const auth = useAuth()

const email = ref('')
const password = ref('')

const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''

  try {
    await auth.login(
      email.value,
      password.value
    )

    await navigateTo('/')
  } catch (e) {
    error.value = 'Неверный email или пароль'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div>
    <h1>Вход</h1>

    <form @submit.prevent="submit">
      <div>
        <input
          v-model="email"
          type="email"
          placeholder="Email"
        >
      </div>

      <div>
        <input
          v-model="password"
          type="password"
          placeholder="Пароль"
        >
      </div>

      <p v-if="error">
        {{ error }}
      </p>

      <button :disabled="loading">
        {{ loading ? 'Входим...' : 'Войти' }}
      </button>
    </form>
  </div>
</template>
