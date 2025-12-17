<template>
    <AdminLayout>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
        <p class="mt-2 text-sm text-gray-600">View and manage all customer orders</p>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Search Input -->
          <div class="flex-1">
            <label for="search" class="sr-only">Search orders</label>
            <div class="relative">
              <input
                id="search"
                v-model="filters.search"
                @keyup.enter="applyFilters"
                type="text"
                class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Search by order #, email, or customer name..."
              />
              <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Status Filter -->
          <div class="w-full sm:w-48">
            <label for="status-filter" class="sr-only">Filter by status</label>
            <select
              id="status-filter"
              v-model="filters.status"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>

          <!-- Search Button -->
          <button
            @click="applyFilters"
            :disabled="isLoading"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="!isLoading">Search</span>
            <span v-else class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Searching...
            </span>
          </button>

          <!-- Clear Filters -->
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
          >
            Clear
          </button>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Loading State -->
        <div v-if="isLoading" class="p-12 text-center">
          <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-gray-500">Loading orders...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="!ordersData.data?.length" class="p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ hasActiveFilters ? 'Try adjusting your search filters' : 'Orders will appear here once customers place them' }}
          </p>
        </div>

        <!-- Orders Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Order
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Payment
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="order in ordersData.data" :key="order.id" class="hover:bg-gray-50 transition-colors">
                <!-- Order Number -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ order.order_number }}
                  </div>
                  <div class="text-xs text-gray-500">
                    ID: {{ order.id }}
                  </div>
                </td>

                <!-- Customer Info -->
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ order.user?.name ?? 'Guest' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ order.guest_email ?? order.user?.email }}
                  </div>
                </td>

                <!-- Date -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ formatDate(order.created_at) }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ formatTime(order.created_at) }}
                  </div>
                </td>

                <!-- Total Amount -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900">
                    £{{ Number(order.total_amount).toFixed(2) }}
                  </div>
                </td>

                <!-- Status Badge -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="statusBadgeClass(order.status)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  >
                    {{ order.status }}
                  </span>
                </td>

                <!-- Payment Info -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div v-if="order.payments?.length" class="text-sm">
                    <div class="font-medium text-gray-900 capitalize">
                      {{ formatPaymentMethod(order.payments[0].payment_method) }}
                    </div>
                    <div
                      class="text-xs capitalize"
                      :class="paymentStatusClass(order.payments[0].status)"
                    >
                      {{ order.payments[0].status }}
                    </div>
                  </div>
                  <div v-else class="text-xs text-gray-400">
                    No payment
                  </div>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <!-- View Button -->
                    <Link
                      :href="route('admin.orders.show', order.id)"
                      class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                      :aria-label="`View order ${order.order_number}`"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View
                    </Link>

                    <!-- Status Button -->
                    <button
                      @click="openQuickStatus(order)"
                      class="inline-flex items-center px-3 py-1.5 text-sm bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors"
                      :aria-label="`Update status for order ${order.order_number}`"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      Status
                    </button>

                    <!-- Refund Button -->
                    <button
                      @click="openRefund(order)"
                      :disabled="order.status === 'refunded'"
                      class="inline-flex items-center px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                      :aria-label="`Refund order ${order.order_number}`"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                      </svg>
                      Refund
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="ordersData.data?.length" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="goToPage(ordersData.current_page - 1)"
                :disabled="!ordersData.prev_page_url || isLoading"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button
                @click="goToPage(ordersData.current_page + 1)"
                :disabled="!ordersData.next_page_url || isLoading"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ ordersData.from ?? 0 }}</span>
                  to
                  <span class="font-medium">{{ ordersData.to ?? 0 }}</span>
                  of
                  <span class="font-medium">{{ ordersData.total ?? 0 }}</span>
                  orders
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button
                    @click="goToPage(ordersData.current_page - 1)"
                    :disabled="!ordersData.prev_page_url || isLoading"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>

                  <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    Page {{ ordersData.current_page ?? 1 }} of {{ ordersData.last_page ?? 1 }}
                  </span>

                  <button
                    @click="goToPage(ordersData.current_page + 1)"
                    :disabled="!ordersData.next_page_url || isLoading"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <transition name="notification">
        <div
          v-if="successMessage"
          class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg shadow-lg p-4 max-w-sm"
        >
          <div class="flex items-start">
            <svg class="h-5 w-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
            </div>
            <button @click="successMessage = null" class="ml-auto text-green-500 hover:text-green-600">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </transition>
    </div>

    <!-- Status Update Modal -->
    <teleport to="body">
      <transition name="modal">
        <div
          v-if="showStatusModal"
          class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
          @click.self="closeStatus"
        >
          <div
            class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all"
            role="dialog"
            aria-modal="true"
            aria-labelledby="status-modal-title"
          >
            <div class="px-6 pt-6 pb-4">
              <div class="flex items-center justify-between mb-4">
                <h3 id="status-modal-title" class="text-lg font-semibold text-gray-900">
                  Update Order Status
                </h3>
                <button
                  @click="closeStatus"
                  class="text-gray-400 hover:text-gray-500 focus:outline-none"
                  :disabled="statusLoading"
                >
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-600">Order Number</div>
                <div class="font-medium text-gray-900">{{ selectedOrder.order_number }}</div>
              </div>

              <div class="space-y-4">
                <div>
                  <label for="status-select" class="block text-sm font-medium text-gray-700 mb-2">
                    New Status
                  </label>
                  <select
                    id="status-select"
                    v-model="statusForm.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="refunded">Refunded</option>
                  </select>
                </div>
              </div>

              <div v-if="statusError" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">{{ statusError }}</p>
              </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
              <button
                @click="closeStatus"
                :disabled="statusLoading"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="submitStatus"
                :disabled="statusLoading"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
              >
                <span v-if="!statusLoading">Update Status</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Updating...
                </span>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </teleport>

    <!-- Refund Modal -->
    <teleport to="body">
      <transition name="modal">
        <div
          v-if="showRefundModal"
          class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
          @click.self="closeRefund"
        >
          <div
            class="bg-white rounded-lg shadow-xl w-full max-w-lg transform transition-all"
            role="dialog"
            aria-modal="true"
            aria-labelledby="refund-modal-title"
          >
            <div class="px-6 pt-6 pb-4">
              <div class="flex items-center justify-between mb-4">
                <h3 id="refund-modal-title" class="text-lg font-semibold text-gray-900">
                  Issue Refund
                </h3>
                <button
                  @click="closeRefund"
                  class="text-gray-400 hover:text-gray-500 focus:outline-none"
                  :disabled="refundLoading"
                >
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                  <svg class="h-5 w-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  <div class="ml-3">
                    <h4 class="text-sm font-medium text-red-800">Refund Warning</h4>
                    <p class="mt-1 text-sm text-red-700">This action cannot be undone. The refund will be processed immediately.</p>
                  </div>
                </div>
              </div>

              <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-600">Order Number</div>
                    <div class="font-medium text-gray-900">{{ selectedOrder.order_number }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-600">Order Total</div>
                    <div class="font-semibold text-gray-900">£{{ Number(selectedOrder.total_amount).toFixed(2) }}</div>
                  </div>
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label for="refund-amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Refund Amount (GBP) <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">£</span>
                    <input
                      id="refund-amount"
                      v-model.number="refundForm.amount"
                      type="number"
                      step="0.01"
                      :max="selectedOrder.total_amount"
                      min="0.01"
                      class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                      placeholder="0.00"
                    />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Maximum: £{{ Number(selectedOrder.total_amount).toFixed(2) }}</p>
                </div>

                <div>
                  <label for="refund-reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason (Optional)
                  </label>
                  <textarea
                    id="refund-reason"
                    v-model="refundForm.reason"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                    placeholder="Enter the reason for this refund..."
                  ></textarea>
                </div>
              </div>

              <div v-if="refundError" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">{{ refundError }}</p>
              </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
              <button
                @click="closeRefund"
                :disabled="refundLoading"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="submitRefund"
                :disabled="refundLoading"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
              >
                <span v-if="!refundLoading">Issue Refund</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </span>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </teleport>
  </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue';

const page = usePage()

const props = defineProps({
  orders: {
    type: Object,
    required: true,
  },
})

// Computed property for reactive orders data
const ordersData = computed(() => props.orders)

// Loading states
const isLoading = ref(false)
const statusLoading = ref(false)
const refundLoading = ref(false)

// Success message
const successMessage = ref(null)

// Filters
const filters = reactive({
  search: ordersData.value.search || '',
  status: ordersData.value.status || '',
  page: ordersData.value.current_page || 1,
})

// Check if filters are active
const hasActiveFilters = computed(() => {
  return filters.search || filters.status
})

// Apply filters
const applyFilters = () => {
  filters.page = 1 // Reset to first page when filtering
  getOrders()
}

// Clear all filters
const clearFilters = () => {
  filters.search = ''
  filters.status = ''
  filters.page = 1
  getOrders()
}

// Reload list with filters
const getOrders = () => {
  isLoading.value = true
  const qs = {
    search: filters.search || undefined,
    status: filters.status || undefined,
    page: filters.page,
  }
  router.get(route('admin.orders.index'), qs, {
    preserveState: true,
    replace: true,
    onFinish: () => {
      isLoading.value = false
    }
  })
}

// Pagination
const goToPage = (pageNum) => {
  if (pageNum < 1 || pageNum > ordersData.value.last_page) return
  filters.page = pageNum
  getOrders()
}

// Status modal
const showStatusModal = ref(false)
const selectedOrder = ref({})
const statusForm = reactive({ status: 'pending' })
const statusError = ref('')

const openQuickStatus = (order) => {
  selectedOrder.value = order
  statusForm.status = order.status
  statusError.value = ''
  showStatusModal.value = true
}

const closeStatus = () => {
  if (statusLoading.value) return
  showStatusModal.value = false
  statusError.value = ''
}

const submitStatus = () => {
  statusError.value = ''
  statusLoading.value = true

  router.patch(
    route('admin.orders.update', selectedOrder.value.id),
    { status: statusForm.status },
    {
      preserveState: true,
      onSuccess: () => {
        closeStatus()
        showSuccessMessage('Order status updated successfully')
      },
      onError: (errors) => {
        statusError.value = errors.status || Object.values(errors).join(', ') || 'Failed to update status'
      },
      onFinish: () => {
        statusLoading.value = false
      }
    }
  )
}

// Refund modal
const showRefundModal = ref(false)
const refundForm = reactive({ amount: null, reason: '' })
const refundError = ref('')

const openRefund = (order) => {
  if (order.status === 'refunded') return

  selectedOrder.value = order
  refundForm.amount = parseFloat(order.total_amount)
  refundForm.reason = ''
  refundError.value = ''
  showRefundModal.value = true
}

const closeRefund = () => {
  if (refundLoading.value) return
  showRefundModal.value = false
  refundError.value = ''
}

const submitRefund = () => {
  refundError.value = ''

  if (!refundForm.amount || refundForm.amount <= 0) {
    refundError.value = 'Enter a valid refund amount.'
    return
  }

  if (refundForm.amount > selectedOrder.value.total_amount) {
    refundError.value = 'Refund amount cannot exceed order total.'
    return
  }

  refundLoading.value = true

  router.post(
    route('admin.orders.refund', selectedOrder.value.id),
    refundForm,
    {
      preserveState: true,
      onSuccess: () => {
        closeRefund()
        showSuccessMessage('Refund processed successfully')
      },
      onError: (errors) => {
        refundError.value = errors.refund || errors.amount || Object.values(errors).join(', ') || 'Refund failed'
      },
      onFinish: () => {
        refundLoading.value = false
      }
    }
  )
}

// Helper function to show success messages
const showSuccessMessage = (message) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = null
  }, 5000)
}

// Watch for flash messages from backend
watch(() => page.props.flash?.success, (newValue) => {
  if (newValue) {
    showSuccessMessage(newValue)
  }
})

// Status badge styling
const statusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-indigo-100 text-indigo-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    refunded: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Payment status styling
const paymentStatusClass = (status) => {
  const classes = {
    completed: 'text-green-600',
    paid: 'text-green-600',
    pending: 'text-yellow-600',
    failed: 'text-red-600',
    refunded: 'text-gray-600',
  }
  return classes[status] || 'text-gray-500'
}

// Format payment method
const formatPaymentMethod = (method) => {
  const methods = {
    stripe: 'Credit Card',
    cod: 'Cash on Delivery',
    bank_transfer: 'Bank Transfer',
    paypal: 'PayPal',
  }
  return methods[method] || method
}

// Date formatting
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-GB', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Keyboard shortcuts
onMounted(() => {
  const handleKeydown = (e) => {
    if (e.key === 'Escape') {
      if (showStatusModal.value) closeStatus()
      if (showRefundModal.value) closeRefund()
    }
  }
  
  document.addEventListener('keydown', handleKeydown)
  
  // Cleanup
  return () => {
    document.removeEventListener('keydown', handleKeydown)
  }
})
</script>

<style scoped>
/* Modal transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: all 0.25s ease;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95);
  opacity: 0;
}

/* Notification transitions */
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from,
.notification-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>