import { authService } from '~/services/auth'
import { userService } from '~/services/user'

export interface User {
  id: number
  name: string
  email: string
}

export const useAuth = () => {
  const token = useCookie<string | null>('access_token', {
    default: () => null,
    sameSite: 'lax'
  })

  const user = useState<User | null>('auth:user', () => null)

  const initialized = useState<boolean>(
    'auth:initialized',
    () => false
  )

  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, password: string) {
    const response = await authService.login({
      email,
      password
    })

    token.value = response.access_token

    user.value = await userService.me()
    initialized.value = true
  }

  async function fetchUser() {
    user.value = await userService.me()
    initialized.value = true
  }

  function logout() {
    token.value = null
    user.value = null
    initialized.value = false
  }

  return {
    token,
    user,
    isAuthenticated,
    initialized,
    login,
    logout,
    fetchUser
  }
}
