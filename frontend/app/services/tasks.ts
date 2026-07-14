import type { PaginatedResponse } from '~/types/api'
import type { Task, TaskFilters } from '~/types/task'

export const taskService = {
  async index(
    filters: TaskFilters = {}
  ): Promise<PaginatedResponse<Task>> {
    const api = useApi()

    return await api('/tasks', {
      query: filters
    })
  }
}
