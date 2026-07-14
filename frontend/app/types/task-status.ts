export enum TaskStatus {
  PENDING = 'pending',
  IN_PROGRESS = 'in_progress',
  COMPLETED = 'completed'
}

export const TaskStatusOptions = [
  {
    value: '',
    label: 'Все статусы'
  },
  {
    value: TaskStatus.PENDING,
    label: 'Ожидает'
  },
  {
    value: TaskStatus.IN_PROGRESS,
    label: 'В работе'
  },
  {
    value: TaskStatus.COMPLETED,
    label: 'Завершена'
  }
]
