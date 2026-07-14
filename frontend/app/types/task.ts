import type { TaskStatus } from './task-status'

export interface Task {
  id: number
  user_id: number

  title: string
  description: string | null

  due_date: string | null

  status: string

  created_at: string
  updated_at: string
}

export interface TaskFilters {
  page?: number

  per_page?: number

  search?: string

  status?: string

  due_date_from?: string

  due_date_to?: string

  sort_by?: string

  sort_direction?: 'asc' | 'desc'
}

export interface CreateTaskRequest {
  title: string
  description: string
  due_date: string | null
  status: TaskStatus | null
}

export interface UpdateTaskRequest extends CreateTaskRequest {}
