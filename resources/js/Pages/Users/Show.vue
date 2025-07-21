<template>
  <Head :title="`User: ${user.data.full_name}`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        User Details
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900">
                {{ user.data.full_name }}
              </h3>
              <div class="flex space-x-2">
                <Link
                  :href="route('users.edit', user.data.id)"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md"
                >
                  Edit User
                </Link>
                <Link
                  :href="route('users.index')"
                  class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md"
                >
                  Back to Users
                </Link>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div v-if="user.data.photo" class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                <img
                  :src="user.data.photo"
                  :alt="user.data.full_name"
                  class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Prefix</label>
                <p class="mt-1 text-sm text-gray-900">{{ user.data.prefixname || 'N/A' }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Suffix</label>
                <p class="mt-1 text-sm text-gray-900">{{ user.data.suffixname || 'N/A' }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">First Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ user.data.firstname }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ user.data.middlename || 'N/A' }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Last Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ user.data.lastname }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ user.data.email }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1"
                      :class="user.type === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                  {{ user.data.type }}
                </span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                <p class="mt-1 text-sm text-gray-900">
                  {{ user.data.email_verified_at ? 'Yes' : 'No' }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Created At</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(user.data.created_at) }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Updated At</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(user.data.updated_at) }}</p>
              </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
              <h4 class="text-lg font-medium text-gray-900 mb-4">User Details (Auto-generated)</h4>
              <div v-if="user.data.details && user.data.details.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="detail in user.data.details" :key="detail.id" class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex items-center">
                    <span v-if="detail.icon" class="text-gray-500 mr-2">{{ detail.icon }}</span>
                    <div>
                      <p class="text-sm font-medium text-gray-700">{{ detail.key }}</p>
                      <p class="text-sm text-gray-900">{{ detail.value }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-gray-500 text-sm">
                No additional details available.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  user: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}
</script> 