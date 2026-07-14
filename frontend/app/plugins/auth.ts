export default defineNuxtPlugin(async () => {
  const auth = useAuth()

  if (auth.initialized.value)
    return

  if (!auth.token.value)
    return

  try {
    await auth.fetchUser()
  } catch {
    auth.logout()
  }
})
