<template>
    <AdminLayout>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-start justify-between mb-4">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <h1 class="text-3xl font-bold text-gray-900">Order {{ order.order_number }}</h1>
              <span
                :class="statusBadgeClass(order.status)"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold capitalize"
              >
                {{ order.status }}
              </span>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-600">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Placed: {{ formatDate(order.created_at) }}
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Order ID: {{ order.id }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <inertia-link
              :href="route('admin.orders.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Orders
            </inertia-link>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2">
            <label for="status-update" class="text-sm font-medium text-gray-700">Status:</label>
            <select
              id="status-update"
              v-model="statusForm.status"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>

          <button
            @click="updateStatus"
            :disabled="statusLoading || statusForm.status === order.status"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <svg v-if="!statusLoading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ statusLoading ? 'Updating...' : 'Update Status' }}
          </button>

          <button
            @click="openRefund"
            :disabled="order.status === 'refunded'"
            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
            </svg>
            Issue Refund
          </button>
        </div>
      </div>

      <!-- Success Message -->
      <transition name="notification">
        <div
          v-if="successMessage"
          class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4"
        >
          <div class="flex items-start">
            <svg class="h-5 w-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
            </div>
            <button @click="successMessage = null" class="text-green-500 hover:text-green-600">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </transition>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Order Items -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Order Items Card -->
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
              <p class="text-sm text-gray-600 mt-1">{{ order.items?.length || 0 }} item(s)</p>
            </div>

            <div class="divide-y divide-gray-200">
              <div
                v-for="item in order.items"
                :key="item.id"
                class="p-6 hover:bg-gray-50 transition-colors"
              >
                <div class="flex gap-4">
                  <!-- Product Image -->
                  <div class="flex-shrink-0">
                    <img
                      :src="item.product?.images?.[0]?.image_path ? '/storage/' + item.product.images[0].image_path : '/images/placeholder.png'"
                      :alt="item.product_name"
                      class="w-24 h-24 object-cover rounded-lg border border-gray-200"
                      @error="handleImageError"
                    />
                  </div>

                  <!-- Product Details -->
                  <div class="flex-1 min-w-0">
                    <h3 class="text-base font-medium text-gray-900 mb-1">
                      {{ item.product_name }}
                    </h3>
                    <div class="space-y-1 text-sm text-gray-600">
                      <div class="flex items-center">
                        <span class="font-medium mr-2">SKU:</span>
                        <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ item.product_sku }}</span>
                      </div>
                      <div class="flex items-center">
                        <span class="font-medium mr-2">Quantity:</span>
                        <span>{{ item.quantity }}</span>
                      </div>
                      <div class="flex items-center">
                        <span class="font-medium mr-2">Price per item:</span>
                        <span>£{{ Number(item.price).toFixed(2) }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Item Total -->
                  <div class="flex-shrink-0 text-right">
                    <div class="text-lg font-semibold text-gray-900">
                      £{{ Number(item.total).toFixed(2) }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                      Total
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty State -->
              <div v-if="!order.items || order.items.length === 0" class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">No items in this order</p>
              </div>
            </div>
          </div>

          <!-- Customer Information Card -->
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-semibold text-gray-900">Customer Information</h2>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 class="text-sm font-medium text-gray-900 mb-2">Contact Details</h3>
                  <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      {{ order.user?.name || 'Guest Customer' }}
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      {{ order.guest_email || order.user?.email || 'No email' }}
                    </div>
                  </div>
                </div>

                <div v-if="billing">
                  <h3 class="text-sm font-medium text-gray-900 mb-2">Billing Address</h3>
                  <div class="text-sm text-gray-700 space-y-1">
                    <div>{{ billing.first_name }} {{ billing.last_name }}</div>
                    <div>{{ billing.address_line_1 }}</div>
                    <div v-if="billing.address_line_2">{{ billing.address_line_2 }}</div>
                    <div>{{ billing.city }}, {{ billing.state }} {{ billing.postal_code }}</div>
                    <div>{{ billing.country }}</div>
                    <div v-if="billing.phone" class="flex items-center mt-2 text-xs text-gray-600">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      {{ billing.phone }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Summary & Details -->
        <div class="space-y-6">
          <!-- Order Summary Card -->
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
            </div>
            <div class="p-6">
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Subtotal</span>
                  <span class="font-medium text-gray-900">£{{ Number(order.subtotal || 0).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Shipping</span>
                  <span class="font-medium text-gray-900">£{{ Number(order.shipping_amount || 0).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Tax</span>
                  <span class="font-medium text-gray-900">£{{ Number(order.tax_amount || 0).toFixed(2) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200">
                  <div class="flex justify-between items-center">
                    <span class="text-base font-semibold text-gray-900">Total</span>
                    <span class="text-xl font-bold text-gray-900">£{{ Number(order.total_amount).toFixed(2) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Shipping Address Card -->
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-semibold text-gray-900">Shipping Address</h2>
            </div>
            <div class="p-6">
              <div v-if="shipping" class="text-sm text-gray-700 space-y-1">
                <div class="font-medium text-gray-900">{{ shipping.first_name }} {{ shipping.last_name }}</div>
                <div>{{ shipping.address_line_1 }}</div>
                <div v-if="shipping.address_line_2">{{ shipping.address_line_2 }}</div>
                <div>{{ shipping.city }}, {{ shipping.state }} {{ shipping.postal_code }}</div>
                <div>{{ shipping.country }}</div>
                <div v-if="shipping.phone" class="flex items-center mt-3 text-xs text-gray-600">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  {{ shipping.phone }}
                </div>
              </div>
              <div v-else class="text-sm text-gray-500">
                No shipping address provided
              </div>
            </div>
          </div>

          <!-- Payment Information Card -->
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-semibold text-gray-900">Payment Information</h2>
            </div>
            <div class="p-6">
              <div v-if="order.payments?.length" class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                  <span class="text-gray-600">Method</span>
                  <span class="font-medium text-gray-900 capitalize">
                    {{ formatPaymentMethod(order.payments[0].payment_method) }}
                  </span>
                </div>
                <div class="flex justify-between items-center text-sm">
                  <span class="text-gray-600">Status</span>
                  <span
                    :class="paymentStatusClass(order.payments[0].status)"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize"
                  >
                    {{ order.payments[0].status }}
                  </span>
                </div>
                <div v-if="order.payments[0].transaction_id" class="pt-3 border-t border-gray-200">
                  <div class="text-xs text-gray-600 mb-1">Transaction ID</div>
                  <div class="font-mono text-xs bg-gray-50 px-2 py-1.5 rounded border border-gray-200 break-all">
                    {{ order.payments[0].transaction_id }}
                  </div>
                </div>
                <div v-if="order.payments[0].payment_details" class="text-xs text-gray-600">
                  <details class="cursor-pointer">
                    <summary class="font-medium hover:text-gray-900">View payment details</summary>
                    <pre class="mt-2 bg-gray-50 p-2 rounded text-xs overflow-auto">{{ JSON.stringify(order.payments[0].payment_details, null, 2) }}</pre>
                  </details>
                </div>
              </div>
              <div v-else class="text-sm text-gray-500">
                No payment information available
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
                  <svg class="h-5 w-5 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  <div class="ml-3">
                    <h4 class="text-sm font-medium text-red-800">Refund Warning</h4>
                    <p class="mt-1 text-sm text-red-700">This action cannot be undone. The refund will be processed immediately.</p>
                  </div>
                </div>
              </div>

              <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <div class="text-gray-600">Order Number</div>
                    <div class="font-medium text-gray-900">{{ order.order_number }}</div>
                  </div>
                  <div>
                    <div class="text-gray-600">Order Total</div>
                    <div class="font-semibold text-gray-900">£{{ Number(order.total_amount).toFixed(2) }}</div>
                  </div>
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label for="refund-amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Refund Amount (GBP) <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm">£</span>
                    <input
                      id="refund-amount"
                      v-model.number="refundForm.amount"
                      type="number"
                      step="0.01"
                      :max="order.total_amount"
                      min="0.01"
                      class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                      placeholder="0.00"
                    />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Maximum refund: £{{ Number(order.total_amount).toFixed(2) }}</p>
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
import { ref, reactive, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue';

const page = usePage()

const props = defineProps({
  order: {
    type: Object,
    required: true,
  },
})

// Loading states
const statusLoading = ref(false)
const refundLoading = ref(false)

// Success message
const successMessage = ref(null)

// Computed addresses
const shipping = computed(() => {
  return props.order.addresses?.find(a => a.type === 'shipping') || null
})

const billing = computed(() => {
  return props.order.addresses?.find(a => a.type === 'billing') || null
})

// Status update form
const statusForm = reactive({
  status: props.order.status || 'pending'
})

const updateStatus = () => {
  if (statusForm.status === props.order.status) return
  
  statusLoading.value = true

  router.patch(
    route('admin.orders.update', props.order.id),
    { status: statusForm.status },
    {
      preserveState: true,
      onSuccess: () => {
        showSuccessMessage('Order status updated successfully')
      },
      onError: (errors) => {
        console.error('Status update failed:', errors)
      },
      onFinish: () => {
        statusLoading.value = false
      }
    }
  )
}

// Refund modal
const showRefundModal = ref(false)
const refundForm = reactive({
  amount: null,
  reason: ''
})
const refundError = ref('')

const openRefund = () => {
  if (props.order.status === 'refunded') return

  refundForm.amount = parseFloat(props.order.total_amount)
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

  if (refundForm.amount > props.order.total_amount) {
    refundError.value = 'Refund amount cannot exceed order total.'
    return
  }

  refundLoading.value = true

  router.post(
    route('admin.orders.refund', props.order.id),
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
    completed: 'bg-green-100 text-green-800',
    paid: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    failed: 'bg-red-100 text-red-800',
    refunded: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Format payment method
const formatPaymentMethod = (method) => {
  const methods = {
    stripe: 'Credit Card (Stripe)',
    cod: 'Cash on Delivery',
    bank_transfer: 'Bank Transfer',
    paypal: 'PayPal',
  }
  return methods[method] || method
}

// Date formatting
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleString('en-GB', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Handle image errors
const handleImageError = (e) => {
  e.target.src = '/images/placeholder.png'
}
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

.notification-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}

.notification-leave-to {
  opacity: 0;
}
</style>