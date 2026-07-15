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
  <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <!-- Обертка для центрирования -->
    <div class="w-full max-w-md space-y-8">
      <!-- Контейнер формы -->
      <div class="bg-white p-8 rounded-lg shadow-xl border border-gray-200">
        <!-- Заголовок -->
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-6">
          Вход
        </h1>

        <!-- Форма -->
        <form
          class="space-y-6"
          @submit.prevent="submit"
        >
          <!-- Группа Email -->
          <div>
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Email
            </label>
            <input
              id="email"
              v-model="email"
              type="email"
              placeholder="user@example.com"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out"
            >
          </div>

          <!-- Группа Пароль -->
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Пароль
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out"
            >
          </div>

          <!-- Сообщение об ошибке -->
          <p
            v-if="error"
            class="text-red-500 text-sm font-medium"
          >
            {{ error }}
          </p>

          <!-- Кнопка отправки -->
          <div>
            <button
              :disabled="loading"
              type="submit"
              class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out cursor-pointer"
            >
              {{ loading ? 'Входим...' : 'Войти' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
