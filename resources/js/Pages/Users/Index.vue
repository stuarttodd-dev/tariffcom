<template>
  <Head title="Users" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Users
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <div class="flex-1 max-w-sm">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search users..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  @input="debouncedSearch"
                />
              </div>
              <div class="flex gap-2">
                <Link
                  :href="route('users.create')"
                >
                  <PrimaryButton>Create User</PrimaryButton>
                </Link>
                <Link
                  :href="route('users.trashed')"
                >
                  <SecondaryButton>Archived Users</SecondaryButton>
                </Link>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table v-if="users.data.length" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Created
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="user in users.data" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ user.full_name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ user.prefixname }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ user.email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="user.type === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                        {{ user.type }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(user.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end gap-2">
                        <Link
                          :href="route('users.show', user.id)"
                        >
                          <SecondaryButton>View</SecondaryButton>
                        </Link>
                        <Link
                          :href="route('users.edit', user.id)"
                        >
                          <PrimaryButton>Edit</PrimaryButton>
                        </Link>
                        <DangerButton
                          v-if="user.id !== currentUserId"
                          @click="askDeleteUser(user.id)"
                        >
                          Delete
                        </DangerButton>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-else class="flex flex-col items-center justify-center py-12">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-4a4 4 0 100-8 4 4 0 000 8zm-6 8a6 6 0 0112 0H3z" />
                </svg>
                <p class="text-gray-500 text-lg">No users found. Try adjusting your search or create a new user.</p>
              </div>
            </div>

            <Modal :show="showDeleteModal" @close="cancelDeleteUser">
              <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Confirm Deletion</h2>
                <p class="mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
                <div class="flex justify-end gap-2">
                  <SecondaryButton @click="cancelDeleteUser">Cancel</SecondaryButton>
                  <DangerButton @click="confirmDeleteUser">Delete</DangerButton>
                </div>
              </div>
            </Modal>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  users: Object,
  filters: Object,
})

const search = ref(props.filters.search || '')
const page = usePage()
const currentUserId = computed(() => page.props.auth.user.id)

const showDeleteModal = ref(false)
const userIdToDelete = ref(null)

const debouncedSearch = () => {
  router.get(route('users.index'), { search: search.value }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const askDeleteUser = (userId) => {
  userIdToDelete.value = userId
  showDeleteModal.value = true
}

const confirmDeleteUser = () => {
  if (userIdToDelete.value) {
    router.delete(route('users.destroy', userIdToDelete.value))
  }
  showDeleteModal.value = false
  userIdToDelete.value = null
}

const cancelDeleteUser = () => {
  showDeleteModal.value = false
  userIdToDelete.value = null
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script> 