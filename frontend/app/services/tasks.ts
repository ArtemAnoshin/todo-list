import type { PaginatedResponse } from '~/types/api'
import type { Task, TaskFilters, CreateTaskRequest } from '~/types/task'

export const taskService = {
  async index(
    filters: TaskFilters = {}
  ): Promise<PaginatedResponse<Task>> {
    const api = useApi()

    return await api('/tasks', {
      query: filters
    })
  },

  async create(data: CreateTaskRequest): Promise<Task> {
    const api = useApi()

    return await api('/tasks', {
      method: 'POST',
      body: data
    })
  },

  async create(data: CreateTaskRequest): Promise<Task> {
    const api = useApi()

    return await api('/tasks', {
      method: 'POST',
      body: data,
    })
  }
}
