<template>
  <Head :title="`Edit User: ${user.full_name}`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit User
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="prefixname" class="block text-sm font-medium text-gray-700">Prefix</label>
                  <select
                    id="prefixname"
                    v-model="form.prefixname"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Select prefix</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Ms">Ms</option>
                  </select>
                  <div v-if="form.errors.prefixname" class="text-red-600 text-sm mt-1">
                    {{ form.errors.prefixname }}
                  </div>
                </div>

                <div>
                  <label for="suffixname" class="block text-sm font-medium text-gray-700">Suffix</label>
                  <input
                    id="suffixname"
                    v-model="form.suffixname"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <div v-if="form.errors.suffixname" class="text-red-600 text-sm mt-1">
                    {{ form.errors.suffixname }}
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label for="firstname" class="block text-sm font-medium text-gray-700">First Name *</label>
                  <input
                    id="firstname"
                    v-model="form.firstname"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <div v-if="form.errors.firstname" class="text-red-600 text-sm mt-1">
                    {{ form.errors.firstname }}
                  </div>
                </div>

                <div>
                  <label for="middlename" class="block text-sm font-medium text-gray-700">Middle Name</label>
                  <input
                    id="middlename"
                    v-model="form.middlename"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <div v-if="form.errors.middlename" class="text-red-600 text-sm mt-1">
                    {{ form.errors.middlename }}
                  </div>
                </div>

                <div>
                  <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name *</label>
                  <input
                    id="lastname"
                    v-model="form.lastname"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <div v-if="form.errors.lastname" class="text-red-600 text-sm mt-1">
                    {{ form.errors.lastname }}
                  </div>
                </div>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
                <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                  {{ form.errors.email }}
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
                    {{ form.errors.password }}
                  </div>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                  <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>

              <div>
                <label for="photo" class="block text-sm font-medium text-gray-700">Photo URL</label>
                <input
                  id="photo"
                  v-model="form.photo"
                  type="url"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
                <div v-if="form.errors.photo" class="text-red-600 text-sm mt-1">
                  {{ form.errors.photo }}
                </div>
              </div>

              <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select
                  id="type"
                  v-model="form.type"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                </select>
                <div v-if="form.errors.type" class="text-red-600 text-sm mt-1">
                  {{ form.errors.type }}
                </div>
              </div>

              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('users.show', user.id)"
                  class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md disabled:opacity-50"
                >
                  {{ form.processing ? 'Updating...' : 'Update User' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  user: Object,
})

const form = useForm({
  prefixname: props.user.prefixname,
  firstname: props.user.firstname,
  middlename: props.user.middlename,
  lastname: props.user.lastname,
  suffixname: props.user.suffixname,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  photo: props.user.photo,
  type: props.user.type,
})

const submit = () => {
  form.put(route('users.update', props.user.id))
}
</script> 