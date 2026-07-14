export interface LoginRequest {
  email: string
  password: string
}

export interface LoginResponse {
  access_token: string
}

export const authService = {
  async login(data: LoginRequest): Promise<LoginResponse> {
    const api = useApi()

    return await api('/auth/login', {
      method: 'POST',
      body: data
    })
  }
}
