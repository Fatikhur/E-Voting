{{-- Global Confirmation Modal Dialog Box --}}
<div x-data="confirmDialogModal()"
     x-cloak
     x-show="isOpen"
     @open-confirm-dialog.window="open($event.detail)"
     @keydown.window.escape="close()"
     class="relative z-50"
     role="dialog"
     aria-modal="true"
     aria-labelledby="confirm-dialog-title"
     aria-describedby="confirm-dialog-message">

    <!-- Backdrop Overlay -->
    <div x-show="isOpen"
         x-transition:enter="transition-opacity ease-linear duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
         aria-hidden="true"></div>

    <!-- Centered Modal Content -->
    <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4 text-center sm:p-0 overflow-y-auto">
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl border border-[#E5E7EB] transition-all sm:my-8 sm:w-full sm:max-w-md p-6">

            <div class="flex items-start gap-3.5">
                <!-- Icon Badge based on Tone -->
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 border"
                     :class="{
                        'bg-red-50 text-red-600 border-red-200': type === 'danger',
                        'bg-amber-50 text-amber-600 border-amber-200': type === 'warning',
                        'bg-blue-50 text-[#1877F2] border-blue-200': type === 'info',
                        'bg-emerald-50 text-[#269B68] border-emerald-200': type === 'success'
                     }">
                    
                    {{-- Danger Icon (Alert / Delete / Sign out) --}}
                    <template x-if="type === 'danger'">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </template>

                    {{-- Warning Icon (Deactivate / Close / Caution) --}}
                    <template x-if="type === 'warning'">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </template>

                    {{-- Info Icon (Sync / Verify / Activate) --}}
                    <template x-if="type === 'info'">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </template>

                    {{-- Success Icon (Open / Publish / Approve / Vote) --}}
                    <template x-if="type === 'success'">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>

                <!-- Text Body -->
                <div class="flex-1 min-w-0">
                    <h3 id="confirm-dialog-title" class="text-base font-semibold text-[#252A34] tracking-tight" x-text="title"></h3>
                    <p id="confirm-dialog-message" class="text-xs sm:text-sm text-[#6B7280] mt-1 leading-relaxed" x-text="message"></p>
                    
                    <!-- Contextual Detail Box -->
                    <div x-show="detail"
                         x-transition
                         class="mt-3 p-2.5 rounded-lg border text-xs leading-relaxed"
                         :class="{
                            'bg-red-50 border-red-200 text-red-800': type === 'danger',
                            'bg-amber-50 border-amber-200 text-amber-800': type === 'warning',
                            'bg-blue-50 border-blue-200 text-blue-800': type === 'info',
                            'bg-emerald-50 border-emerald-200 text-emerald-800': type === 'success'
                         }"
                         x-text="detail"></div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="mt-5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 pt-4 border-t border-[#E5E7EB]">
                <button type="button"
                        @click="close()"
                        :disabled="isSubmitting"
                        class="w-full sm:w-auto px-3.5 py-2 rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] hover:bg-slate-50 text-xs sm:text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 disabled:opacity-50"
                        x-text="cancelText">
                </button>
                <button type="button"
                        x-ref="confirmButton"
                        @click="executeConfirm()"
                        :disabled="isSubmitting"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold text-white transition shadow-xs focus-visible:outline-none focus-visible:ring-2 disabled:opacity-50 flex items-center justify-center gap-2"
                        :class="{
                            'bg-red-600 hover:bg-red-700 focus-visible:ring-red-400': type === 'danger',
                            'bg-amber-600 hover:bg-amber-700 focus-visible:ring-amber-400': type === 'warning',
                            'bg-[#1877F2] hover:bg-blue-700 focus-visible:ring-blue-400': type === 'info',
                            'bg-[#269B68] hover:bg-emerald-700 focus-visible:ring-emerald-400': type === 'success'
                        }">
                    <span x-show="!isSubmitting" x-text="confirmText"></span>
                    <span x-show="isSubmitting" class="flex items-center gap-1.5" style="display: none;">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memproses...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDialogModal() {
    return {
        isOpen: false,
        title: 'Konfirmasi Tindakan',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        detail: '',
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        type: 'warning',
        onConfirm: null,
        isSubmitting: false,

        open(options = {}) {
            this.title = options.title || 'Konfirmasi Tindakan';
            this.message = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.detail = options.detail || '';
            this.confirmText = options.confirmText || 'Ya, Lanjutkan';
            this.cancelText = options.cancelText || 'Batal';
            this.type = options.type || 'warning';
            this.onConfirm = options.onConfirm || null;
            this.isSubmitting = false;
            this.isOpen = true;
            this.$nextTick(() => {
                if (this.$refs.confirmButton) {
                    this.$refs.confirmButton.focus();
                }
            });
        },

        close() {
            if (this.isSubmitting) return;
            this.isOpen = false;
            this.onConfirm = null;
        },

        executeConfirm() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            if (typeof this.onConfirm === 'function') {
                this.onConfirm();
            }
        }
    };
}

// Global API Helper
window.confirmAction = function(options) {
    window.dispatchEvent(new CustomEvent('open-confirm-dialog', { detail: options }));
};

// Global Delegated Click Handler for [data-confirm]
document.addEventListener('click', function(e) {
    const trigger = e.target.closest('[data-confirm]');
    if (!trigger) return;

    const form = trigger.closest('form');
    // If inside a form and the form is invalid, show validation bubble instead of opening modal
    if (form && typeof form.checkValidity === 'function' && !form.checkValidity()) {
        form.reportValidity();
        return;
    }

    e.preventDefault();
    e.stopPropagation();

    const type = trigger.dataset.confirmType || 'warning';
    const title = trigger.dataset.confirmTitle || 'Konfirmasi Tindakan';
    const message = trigger.dataset.confirmMessage || trigger.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
    const detail = trigger.dataset.confirmDetail || '';
    const confirmText = trigger.dataset.confirmButton || 'Ya, Lanjutkan';
    const cancelText = trigger.dataset.confirmCancel || 'Batal';

    window.confirmAction({
        title: title,
        message: message,
        detail: detail,
        confirmText: confirmText,
        cancelText: cancelText,
        type: type,
        onConfirm: () => {
            if (form) {
                form.submit();
            } else if (trigger.tagName === 'A' && trigger.href) {
                window.location.href = trigger.href;
            }
        }
    });
}, true);
</script>
