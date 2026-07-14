export const useApi = () => {
  const config = useRuntimeConfig()

  const token = useCookie<string | null>('access_token')

  return $fetch.create({
    baseURL: config.public.apiUrl,

    onRequest({ options }) {
      if (!token.value) {
        return
      }

      options.headers = new Headers(options.headers)

      options.headers.set(
        'Authorization',
        `Bearer ${token.value}`
      )

      options.headers.set(
        'Accept',
        'application/json'
      )
    },

    async onResponseError({ response }) {
      if (response.status !== 401) {
        return
      }

      const auth = useAuth()

      auth.logout()

      await navigateTo('/login')
    }
  })
}
