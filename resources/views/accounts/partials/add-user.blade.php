<section>
    <x-modal name="add-user-modal" maxWidth="md" :show="$errors->any()">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Add New User</h2>
            <form class="mt-6" id="add-user-form" action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <x-accounts.input-field label="Name" name="name" required />
                    <x-accounts.select-field label="Role" name="role" :options="['Admin', 'Staff']" required />
                    <x-accounts.input-field label="Username" name="username" required />
                    <x-accounts.select-field class="md:col-span-2" label="Division" name="remarks" :options="['HREDRD - PRLS', 'HREDRD - EMES', 'RECORDS', 'HOACDD', 'ELUUPDD', 'PHSD']"
                        required />
                    <div class="flex flex-col md:col-span-2">
                    <x-accounts.input-field label="Password" name="password" type="password" id="password-input"
                            required />

                        <!-- Strength Bar -->
                        <div class="mt-2 h-2 w-full rounded bg-gray-200">
                            <div id="password-strength-bar"
                                class="h-2 w-0 rounded bg-gray-400 transition-all duration-300"></div>
                        </div>

                        <!-- Password Requirements -->
                        <ul class="mt-1 space-y-0.5 text-[11px] text-gray-600" id="password-requirements">
                            <li data-rule="length" data-text="At least 8 characters">● At least 8 characters</li>
                            <li data-rule="uppercase" data-text="Uppercase letter">● Uppercase letter</li>
                            <li data-rule="lowercase" data-text="Lowercase letter">● Lowercase letter</li>
                            <li data-rule="number" data-text="Number">● Number</li>
                            <li data-rule="special" data-text="Special char (!@#$%)">● Special char (!@#$%)</li>
                        </ul>

                    </div>
                </div>


                <div class="mt-6 flex justify-end gap-3">
                    <button class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                        x-on:click="window.dispatchEvent(new CustomEvent('close-modal',{detail:{name:'add-user-modal'}}))">
                        Cancel
                    </button>
                    <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-900" type="submit">Add
                        User</button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pwInput = document.getElementById('password-input');
            const bar = document.getElementById('password-strength-bar');
            const items = Array.from(document.querySelectorAll('#password-requirements li'));
            const rules = [{
                    rule: 'length',
                    regex: /.{8,}/
                },
                {
                    rule: 'uppercase',
                    regex: /[A-Z]/
                },
                {
                    rule: 'lowercase',
                    regex: /[a-z]/
                },
                {
                    rule: 'number',
                    regex: /[0-9]/
                },
                {
                    rule: 'special',
                    regex: /[!@#$%^&*(),.?":{}|<>]/
                }
            ];

            function getColor(count) {
                if (count === 0) return 'bg-gray-400';
                if (count === 1) return 'bg-red-500';
                if (count === 2) return 'bg-yellow-500';
                if (count === 3 || count === 4) return 'bg-blue-500';
                return 'bg-green-600';
            }

            pwInput.addEventListener('input', () => {
                let passed = 0;
                items.forEach(item => {
                    const ok = rules.find(r => r.rule === item.dataset.rule).regex.test(pwInput
                        .value);
                    if (ok) passed++;
                    item.textContent = (ok ? '✅' : '●') + ' ' + item.dataset.text;
                    item.className = ok ? 'text-green-600 text-xs' : 'text-gray-500 text-xs';
                });
                bar.style.width = `${(passed/rules.length)*100}%`;
                bar.className = `h-2 rounded transition-all duration-300 ${getColor(passed)}`;
            });
        });
    </script>
</section>
