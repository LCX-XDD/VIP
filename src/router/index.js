const routes = [
  {
    path: '/todo',
    name: 'Todo',
    component: () => import('@/views/Todo.vue')
  },
  {
    path: '/user',
    name: 'User',
    component: () => import('@/views/User.vue')
  }
  // ... other routes ...
] 