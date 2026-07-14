import type { User } from '~/composables/useAuth'

export const userService = {
  async me(): Promise<User> {
    const api = useApi()

    return await api('/user')
  }
}
