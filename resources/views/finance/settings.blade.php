
@extends('layouts.app')
@section('title', 'Finance Settings')
    @section('content')
        <div class="py-6" >

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" >

                <h1 class="text-2xl font-semibold text-primary-text" > Finance Settings </h1>

                <div class="mt-6" >

                    <div class="bg-card-bg overflow-hidden shadow-subtle sm:rounded-md" >

                        <div class="border-b border-border-color" >
                            <nav class="flex -mb-px" >
                                <button id="tab-general" class="tab-btn active border-accent text-accent px-6 py-4 border-b-2 font-medium text-sm" > General </button>

                                <button id="tab-payment-methods" class="tab-btn border-transparent text-secondary-text hover:text-primary-text hover:border-gray-300 px-6 py-4 border-b-2 font-medium text-sm" > Payment Methods </button>

                                <button id="tab-expense-categories" class="tab-btn border-transparent text-secondary-text hover:text-primary-text hover:border-gray-300 px-6 py-4 border-b-2 font-medium text-sm" > Expense Categories </button>

                                <button id="tab-tax" class="tab-btn border-transparent text-secondary-text hover:text-primary-text hover:border-gray-300 px-6 py-4 border-b-2 font-medium text-sm" > Tax Settings </button>

                                <button id="tab-invoices" class="tab-btn border-transparent text-secondary-text hover:text-primary-text hover:border-gray-300 px-6 py-4 border-b-2 font-medium text-sm" > Invoice Templates </button>
                            </nav>
                        </div>
                        <!-- General Settings -->
                            <div id="content-general" class="tab-content p-6" >

                                <form action="{{ route('finance.settings.general.update') }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6" >

                                        <div class="sm:col-span-3" >
                                            <label for="currency" class="block text-sm font-medium text-secondary-text" > Default Currency </label>
                                            <div class="mt-1" >

                                                <select id="currency" name="currency" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                    <option value="PHP" {{ $settings['currency'] == 'PHP' ? 'selected' : '' }}>Philippine Peso (₱)</option><option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>US Dollar ($)</option><option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>Euro (€)</option><option value="GBP" {{ $settings['currency'] == 'GBP' ? 'selected' : '' }}>British Pound (£)</option><option value="JPY" {{ $settings['currency'] == 'JPY' ? 'selected' : '' }}>Japanese Yen (¥)</option>
                                                </select>

                                            </div>

                                        </div>

                                        <div class="sm:col-span-3" >
                                            <label for="fiscal_year_start" class="block text-sm font-medium text-secondary-text" > Fiscal Year Start </label>
                                            <div class="mt-1" >

                                                <select id="fiscal_year_start" name="fiscal_year_start" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                    <option value="1" {{ $settings['fiscal_year_start'] == 1 ? 'selected' : '' }}>January</option><option value="2" {{ $settings['fiscal_year_start'] == 2 ? 'selected' : '' }}>February</option><option value="3" {{ $settings['fiscal_year_start'] == 3 ? 'selected' : '' }}>March</option><option value="4" {{ $settings['fiscal_year_start'] == 4 ? 'selected' : '' }}>April</option><option value="5" {{ $settings['fiscal_year_start'] == 5 ? 'selected' : '' }}>May</option><option value="6" {{ $settings['fiscal_year_start'] == 6 ? 'selected' : '' }}>June</option><option value="7" {{ $settings['fiscal_year_start'] == 7 ? 'selected' : '' }}>July</option><option value="8" {{ $settings['fiscal_year_start'] == 8 ? 'selected' : '' }}>August</option><option value="9" {{ $settings['fiscal_year_start'] == 9 ? 'selected' : '' }}>September</option><option value="10" {{ $settings['fiscal_year_start'] == 10 ? 'selected' : '' }}>October</option><option value="11" {{ $settings['fiscal_year_start'] == 11 ? 'selected' : '' }}>November</option><option value="12" {{ $settings['fiscal_year_start'] == 12 ? 'selected' : '' }}>December</option>
                                                </select>

                                            </div>

                                        </div>

                                        <div class="sm:col-span-3" >
                                            <label for="decimal_separator" class="block text-sm font-medium text-secondary-text" > Decimal Separator </label>
                                            <div class="mt-1" >

                                                <select id="decimal_separator" name="decimal_separator" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                    <option value="." {{ $settings['decimal_separator'] == '.' ? 'selected' : '' }}>Period (.)</option><option value="," {{ $settings['decimal_separator'] == ',' ? 'selected' : '' }}>Comma (,)</option>
                                                </select>

                                            </div>

                                        </div>

                                        <div class="sm:col-span-3" >
                                            <label for="thousand_separator" class="block text-sm font-medium text-secondary-text" > Thousand Separator </label>
                                            <div class="mt-1" >

                                                <select id="thousand_separator" name="thousand_separator" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                    <option value="," {{ $settings['thousand_separator'] == ',' ? 'selected' : '' }}>Comma (,)</option><option value="." {{ $settings['thousand_separator'] == '.' ? 'selected' : '' }}>Period (.)</option><option value=" " {{ $settings['thousand_separator'] == ' ' ? 'selected' : '' }}>Space ( )</option>
                                                </select>

                                            </div>

                                        </div>

                                        <div class="sm:col-span-3" >
                                            <label for="payment_terms" class="block text-sm font-medium text-secondary-text" > Default Payment Terms </label>
                                            <div class="mt-1" >

                                                <select id="payment_terms" name="payment_terms" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                    <option value="due_on_receipt" {{ $settings['payment_terms'] == 'due_on_receipt' ? 'selected' : '' }}>Due on receipt</option><option value="net_7" {{ $settings['payment_terms'] == 'net_7' ? 'selected' : '' }}>Net 7</option><option value="net_15" {{ $settings['payment_terms'] == 'net_15' ? 'selected' : '' }}>Net 15</option><option value="net_30" {{ $settings['payment_terms'] == 'net_30' ? 'selected' : '' }}>Net 30</option><option value="net_60" {{ $settings['payment_terms'] == 'net_60' ? 'selected' : '' }}>Net 60</option><option value="custom" {{ $settings['payment_terms'] == 'custom' ? 'selected' : '' }}>Custom</option>
                                                </select>

                                            </div>

                                        </div>

                                        <div class="sm:col-span-3" >
                                            <label for="custom_payment_terms" class="block text-sm font-medium text-secondary-text" > Custom Payment Terms (Days) </label>
                                            <div class="mt-1" >

                                                <input type="number" id="custom_payment_terms" name="custom_payment_terms" value="{{ $settings['custom_payment_terms'] }}" min="1" max="365" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                </div>

                                            </div>

                                            <div class="sm:col-span-6" >

                                                <div class="flex items-start" >

                                                    <div class="flex items-center h-5" >

                                                        <input id="enable_late_fees" name="enable_late_fees" type="checkbox" {{ $settings['enable_late_fees'] ? 'checked' : '' }} class="h-4 w-4 text-accent border-border-color rounded bg-input-bg focus:ring-accent" >

                                                        </div>

                                                        <div class="ml-3 text-sm" >
                                                            <label for="enable_late_fees" class="font-medium text-primary-text" >Enable Late Fees</label>
                                                            <p class="text-secondary-text" >Automatically calculate late fees for overdue invoices</p>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="sm:col-span-3" >
                                                    <label for="late_fee_type" class="block text-sm font-medium text-secondary-text" > Late Fee Type </label>
                                                    <div class="mt-1" >

                                                        <select id="late_fee_type" name="late_fee_type" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                            <option value="percentage" {{ $settings['late_fee_type'] == 'percentage' ? 'selected' : '' }}>Percentage</option><option value="fixed" {{ $settings['late_fee_type'] == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="sm:col-span-3" >
                                                    <label for="late_fee_value" class="block text-sm font-medium text-secondary-text" > Late Fee Value </label>
                                                    <div class="mt-1" >

                                                        <input type="number" id="late_fee_value" name="late_fee_value" value="{{ $settings['late_fee_value'] }}" step="0.01" min="0" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="mt-6 flex justify-end" >

                                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Save Settings </button>

                                                </div>

                                            </form>

                                        </div>
                                        <!-- Payment Methods Settings -->
                                            <div id="content-payment-methods" class="tab-content hidden p-6" >

                                                <div class="flex justify-between items-center mb-6" >

                                                    <h3 class="text-lg font-medium text-primary-text" >Payment Methods</h3>

                                                    <button id="add-payment-method-btn" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Add Payment Method </button>

                                                </div>

                                                <div class="overflow-x-auto" >
                                                    <table class="min-w-full divide-y divide-border-color" ><thead class="bg-gray-50" ><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Name</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Description</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Fee (%)</th><th scope="col" class="px-6 py-3 text-center text-xs font-medium text-secondary-text uppercase tracking-wider" >Active</th><th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider" >Actions</th></tr></thead><tbody class="bg-card-bg divide-y divide-border-color" >
                                                        @foreach($paymentMethods as $method) <tr><td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text" >{{ $method->name }}</td><td class="px-6 py-4 text-sm text-primary-text" >{{ $method->description }}</td><td class="px-6 py-4 whitespace-nowrap text-sm text-primary-text" >{{ $method->fee }}%</td><td class="px-6 py-4 whitespace-nowrap text-sm text-primary-text text-center" ><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" > {{ $method->is_active ? 'Active' : 'Inactive' }} </span></td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" >
                                                            <button class="edit-payment-method text-accent hover:text-accent-hover" data-id="{{ $method->id }}">Edit</button>

                                                            <button class="delete-payment-method text-red-600 hover:text-red-900 ml-4" data-id="{{ $method->id }}">Delete</button>
                                                        </td></tr>
                                                    @endforeach
                                                </tbody></table>
                                            </div>

                                        </div>
                                        <!-- Expense Categories Settings -->
                                            <div id="content-expense-categories" class="tab-content hidden p-6" >

                                                <div class="flex justify-between items-center mb-6" >

                                                    <h3 class="text-lg font-medium text-primary-text" >Expense Categories</h3>

                                                    <button id="add-expense-category-btn" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Add Category </button>

                                                </div>

                                                <div class="overflow-x-auto" >
                                                    <table class="min-w-full divide-y divide-border-color" ><thead class="bg-gray-50" ><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Name</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Description</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Color</th><th scope="col" class="px-6 py-3 text-center text-xs font-medium text-secondary-text uppercase tracking-wider" >Budget Tracking</th><th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider" >Actions</th></tr></thead><tbody class="bg-card-bg divide-y divide-border-color" >
                                                        @foreach($expenseCategories as $category) <tr><td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text" >{{ $category->name }}</td><td class="px-6 py-4 text-sm text-primary-text" >{{ $category->description }}</td><td class="px-6 py-4 whitespace-nowrap text-sm text-primary-text" >
                                                            <div class="h-4 w-4 rounded" style="background-color: {{ $category->
                                                                color }}">
                                                            </div>
                                                        </td><td class="px-6 py-4 whitespace-nowrap text-sm text-primary-text text-center" ><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->budget_tracking ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}" > {{ $category->budget_tracking ? 'Enabled' : 'Disabled' }} </span></td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" >
                                                            <button class="edit-expense-category text-accent hover:text-accent-hover" data-id="{{ $category->id }}">Edit</button>

                                                            <button class="delete-expense-category text-red-600 hover:text-red-900 ml-4" data-id="{{ $category->id }}">Delete</button>
                                                        </td></tr>
                                                    @endforeach
                                                </tbody></table>
                                            </div>

                                        </div>
                                        <!-- Tax Settings -->
                                            <div id="content-tax" class="tab-content hidden p-6" >

                                                <form action="{{ route('finance.settings.tax.update') }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6" >

                                                        <div class="sm:col-span-6" >

                                                            <div class="flex items-start" >

                                                                <div class="flex items-center h-5" >

                                                                    <input id="enable_tax" name="enable_tax" type="checkbox" {{ $taxSettings['enable_tax'] ? 'checked' : '' }} class="h-4 w-4 text-accent border-border-color rounded bg-input-bg focus:ring-accent" >

                                                                    </div>

                                                                    <div class="ml-3 text-sm" >
                                                                        <label for="enable_tax" class="font-medium text-primary-text" >Enable Tax Calculations</label>
                                                                        <p class="text-secondary-text" >Apply taxes to invoices and bookings</p>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <div class="sm:col-span-3" >
                                                                <label for="tax_name" class="block text-sm font-medium text-secondary-text" > Tax Name </label>
                                                                <div class="mt-1" >

                                                                    <input type="text" id="tax_name" name="tax_name" value="{{ $taxSettings['tax_name'] }}" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                    </div>

                                                                </div>

                                                                <div class="sm:col-span-3" >
                                                                    <label for="tax_number" class="block text-sm font-medium text-secondary-text" > Tax Registration Number </label>
                                                                    <div class="mt-1" >

                                                                        <input type="text" id="tax_number" name="tax_number" value="{{ $taxSettings['tax_number'] }}" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                        </div>

                                                                    </div>

                                                                    <div class="sm:col-span-3" >
                                                                        <label for="tax_rate" class="block text-sm font-medium text-secondary-text" > Default Tax Rate (%) </label>
                                                                        <div class="mt-1" >

                                                                            <input type="number" id="tax_rate" name="tax_rate" value="{{ $taxSettings['tax_rate'] }}" step="0.01" min="0" max="100" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                            </div>

                                                                        </div>

                                                                        <div class="sm:col-span-3" >
                                                                            <label for="tax_type" class="block text-sm font-medium text-secondary-text" > Tax Type </label>
                                                                            <div class="mt-1" >

                                                                                <select id="tax_type" name="tax_type" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                                                    <option value="inclusive" {{ $taxSettings['tax_type'] == 'inclusive' ? 'selected' : '' }}>Inclusive (Included in price)</option><option value="exclusive" {{ $taxSettings['tax_type'] == 'exclusive' ? 'selected' : '' }}>Exclusive (Added to price)</option>
                                                                                </select>

                                                                            </div>

                                                                        </div>

                                                                        <div class="sm:col-span-6" >

                                                                            <div class="flex items-start" >

                                                                                <div class="flex items-center h-5" >

                                                                                    <input id="enable_multiple_tax_rates" name="enable_multiple_tax_rates" type="checkbox" {{ $taxSettings['enable_multiple_tax_rates'] ? 'checked' : '' }} class="h-4 w-4 text-accent border-border-color rounded bg-input-bg focus:ring-accent" >

                                                                                    </div>

                                                                                    <div class="ml-3 text-sm" >
                                                                                        <label for="enable_multiple_tax_rates" class="font-medium text-primary-text" >Enable Multiple Tax Rates</label>
                                                                                        <p class="text-secondary-text" >Allow different tax rates for different services or locations</p>

                                                                                    </div>

                                                                                </div>

                                                                            </div>

                                                                            <div class="sm:col-span-6" >
                                                                                <label for="tax_note" class="block text-sm font-medium text-secondary-text" > Tax Note (appears on invoices) </label>
                                                                                <div class="mt-1" >

                                                                                    <textarea id="tax_note" name="tax_note" rows="3" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >{{ $taxSettings['tax_note'] }}</textarea>

                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <div class="mt-6 flex justify-end" >

                                                                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Save Tax Settings </button>

                                                                        </div>

                                                                    </form>

                                                                </div>
                                                                <!-- Invoice Templates -->
                                                                    <div id="content-invoices" class="tab-content hidden p-6" >

                                                                        <form action="{{ route('finance.settings.invoices.update') }}" method="POST">
                                                                            @csrf @method('PUT')
                                                                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6" >

                                                                                <div class="sm:col-span-6" >
                                                                                    <label for="invoice_template" class="block text-sm font-medium text-secondary-text" > Invoice Template </label>
                                                                                    <div class="mt-1" >

                                                                                        <select id="invoice_template" name="invoice_template" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                                                                            <option value="standard" {{ $invoiceSettings['invoice_template'] == 'standard' ? 'selected' : '' }}>Standard</option><option value="professional" {{ $invoiceSettings['invoice_template'] == 'professional' ? 'selected' : '' }}>Professional</option><option value="minimal" {{ $invoiceSettings['invoice_template'] == 'minimal' ? 'selected' : '' }}>Minimal</option><option value="creative" {{ $invoiceSettings['invoice_template'] == 'creative' ? 'selected' : '' }}>Creative</option>
                                                                                        </select>

                                                                                    </div>

                                                                                </div>

                                                                                <div class="sm:col-span-3" >
                                                                                    <label for="invoice_prefix" class="block text-sm font-medium text-secondary-text" > Invoice Number Prefix </label>
                                                                                    <div class="mt-1" >

                                                                                        <input type="text" id="invoice_prefix" name="invoice_prefix" value="{{ $invoiceSettings['invoice_prefix'] }}" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                        </div>

                                                                                    </div>

                                                                                    <div class="sm:col-span-3" >
                                                                                        <label for="next_invoice_number" class="block text-sm font-medium text-secondary-text" > Next Invoice Number </label>
                                                                                        <div class="mt-1" >

                                                                                            <input type="number" id="next_invoice_number" name="next_invoice_number" value="{{ $invoiceSettings['next_invoice_number'] }}" min="1" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                            </div>

                                                                                        </div>

                                                                                        <div class="sm:col-span-6" >
                                                                                            <label for="invoice_footer" class="block text-sm font-medium text-secondary-text" > Invoice Footer Text </label>
                                                                                            <div class="mt-1" >

                                                                                                <textarea id="invoice_footer" name="invoice_footer" rows="3" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >{{ $invoiceSettings['invoice_footer'] }}</textarea>

                                                                                            </div>

                                                                                        </div>

                                                                                        <div class="sm:col-span-6" >
                                                                                            <label for="invoice_terms" class="block text-sm font-medium text-secondary-text" > Default Terms and Conditions </label>
                                                                                            <div class="mt-1" >

                                                                                                <textarea id="invoice_terms" name="invoice_terms" rows="5" class="block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >{{ $invoiceSettings['invoice_terms'] }}</textarea>

                                                                                            </div>

                                                                                        </div>

                                                                                        <div class="sm:col-span-6" >

                                                                                            <div class="flex items-start" >

                                                                                                <div class="flex items-center h-5" >

                                                                                                    <input id="email_invoice_automatically" name="email_invoice_automatically" type="checkbox" {{ $invoiceSettings['email_invoice_automatically'] ? 'checked' : '' }} class="h-4 w-4 text-accent border-border-color rounded bg-input-bg focus:ring-accent" >

                                                                                                    </div>

                                                                                                    <div class="ml-3 text-sm" >
                                                                                                        <label for="email_invoice_automatically" class="font-medium text-primary-text" >Email Invoice Automatically</label>
                                                                                                        <p class="text-secondary-text" >Automatically send invoices to customers when they are created</p>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                        <div class="mt-6" >

                                                                                            <h3 class="text-lg font-medium text-primary-text mb-4" >Invoice Preview</h3>

                                                                                            <div class="border border-border-color rounded-md" >

                                                                                                <div class="bg-gray-50 p-4 text-center" >
                                                                                                    <span class="text-sm text-secondary-text" >Preview will update when settings are saved</span>
                                                                                                </div>

                                                                                                <div class="p-4" >
                                                                                                    <img src="{{ asset('images/invoice-' . $invoiceSettings['invoice_template'] . '-preview.png') }}" alt="Invoice Template Preview" class="mx-auto" >
                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                            <div class="mt-6 flex justify-end" >

                                                                                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Save Invoice Settings </button>

                                                                                            </div>

                                                                                        </form>

                                                                                    </div>

                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                    <!-- Payment Method Modal -->
                                                                        <div id="payment-method-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden" >

                                                                            <div class="absolute inset-0 bg-gray-900 bg-opacity-50" >

                                                                            </div>

                                                                            <div class="relative bg-card-bg rounded-lg shadow-xl max-w-md w-full mx-4" >

                                                                                <div class="px-6 py-4 border-b border-border-color flex justify-between items-center" >

                                                                                    <h3 id="payment-method-modal-title" class="text-lg font-medium text-primary-text" >Add Payment Method</h3>

                                                                                    <button class="close-modal text-gray-400 hover:text-gray-500" ><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>

                                                                                    </div>

                                                                                    <form id="payment-method-form" action="{{ route('finance.payment-methods.store') }}" method="POST" class="p-6" >
                                                                                        @csrf
                                                                                        <input type="hidden" id="payment-method-id" name="id">

                                                                                            <input type="hidden" name="_method" value="POST">

                                                                                                <div class="space-y-4" >

                                                                                                    <div>
                                                                                                        <label for="payment-method-name" class="block text-sm font-medium text-secondary-text" >Name</label>
                                                                                                        <input type="text" id="payment-method-name" name="method_name" required class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                        </div>
                                                                                                        
                                                                                                        <div>
                                                                                                            <label for="payment-method-type" class="block text-sm font-medium text-secondary-text" >Type</label>
                                                                                                            <select id="payment-method-type" name="method_type" required class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                                                                                                                <option value="">Select Type</option>
                                                                                                                <option value="online">Online</option>
                                                                                                                <option value="onsite">Onsite</option>
                                                                                                            </select>
                                                                                                        </div>

                                                                                                        <div>
                                                                                                            <label for="payment-method-description" class="block text-sm font-medium text-secondary-text" >Description</label>
                                                                                                            <textarea id="payment-method-description" name="description" rows="3" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" ></textarea>

                                                                                                        </div>

                                                                                                        <div style="display:none;">
                                                                                                            <label for="payment-method-fee" class="block text-sm font-medium text-secondary-text" >Processing Fee (%)</label>
                                                                                                            <input type="number" id="payment-method-fee" name="fee" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                            </div>

                                                                                                            <div class="flex items-start" >

                                                                                                                <div class="flex items-center h-5" >

                                                                                                                    <input id="payment-method-active" name="is_active" type="checkbox" checked class="h-4 w-4 text-accent border-border-color rounded bg-input-bg focus:ring-accent" >

                                                                                                                    </div>

                                                                                                                    <div class="ml-3 text-sm" >
                                                                                                                        <label for="payment-method-active" class="font-medium text-primary-text" >Active</label>
                                                                                                                        <p class="text-secondary-text" >Allow this payment method to be used</p>

                                                                                                                    </div>

                                                                                                                </div>

                                                                                                            </div>

                                                                                                            <div class="mt-6 flex justify-end" >

                                                                                                                <button type="button" class="close-modal mr-3 inline-flex justify-center py-2 px-4 border border-border-color rounded-md shadow-sm text-sm font-medium text-primary-text bg-input-bg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Cancel </button>

                                                                                                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Save </button>

                                                                                                            </div>

                                                                                                        </form>

                                                                                                    </div>

                                                                                                </div>
                                                                                                <!-- Expense Category Modal -->
                                                                                                    <div id="expense-category-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden" >

                                                                                                        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" >

                                                                                                        </div>

                                                                                                        <div class="relative bg-card-bg rounded-lg shadow-xl max-w-md w-full mx-4" >

                                                                                                            <div class="px-6 py-4 border-b border-border-color flex justify-between items-center" >

                                                                                                                <h3 id="expense-category-modal-title" class="text-lg font-medium text-primary-text" >Add Expense Category</h3>

                                                                                                                <button class="close-modal text-gray-400 hover:text-gray-500" ><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>

                                                                                                                </div>

                                                                                                                <form id="expense-category-form" action="{{ route('finance.expense-categories.store') }}" method="POST" class="p-6" >
                                                                                                                    @csrf
                                                                                                                    <input type="hidden" id="expense-category-id" name="id">

                                                                                                                        <input type="hidden" name="_method" value="POST">

                                                                                                                            <div class="space-y-4" >

                                                                                                                                <div>
                                                                                                                                    <label for="expense-category-name" class="block text-sm font-medium text-secondary-text" >Name</label>
                                                                                                                                    <input type="text" id="expense-category-name" name="name" required class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                    </div>

                                                                                                                                    <div>
                                                                                                                                        <label for="expense-category-description" class="block text-sm font-medium text-secondary-text" >Description</label>
                                                                                                                                        <textarea id="expense-category-description" name="description" rows="3" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" ></textarea>

                                                                                                                                    </div>

                                                                                                                                    <div>
                                                                                                                                        <label for="expense-category-color" class="block text-sm font-medium text-secondary-text" >Color</label>
                                                                                                                                        <input type="color" id="expense-category-color" name="color" class="mt-1 block h-10 w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                        </div>

                                                                                                                                        <div class="flex items-start" >

                                                                                                                                            <div class="flex items-center h-5" >

                                                                                                                                                <input id="expense-category-budget-tracking" name="budget_tracking" type="checkbox" class="h-4 w-4 text-accent border-border-color rounded bg-input-bg focus:ring-accent" >

                                                                                                                                                </div>

                                                                                                                                                <div class="ml-3 text-sm" >
                                                                                                                                                    <label for="expense-category-budget-tracking" class="font-medium text-primary-text" >Enable Budget Tracking</label>
                                                                                                                                                    <p class="text-secondary-text" >Track expenses against budget for this category</p>

                                                                                                                                                </div>

                                                                                                                                            </div>

                                                                                                                                            <div id="budget-amount-container" class="hidden" >
                                                                                                                                                <label for="expense-category-budget" class="block text-sm font-medium text-secondary-text" >Monthly Budget Amount</label>
                                                                                                                                                <div class="mt-1 relative rounded-md shadow-sm" >

                                                                                                                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" >
                                                                                                                                                        <span class="text-gray-500 sm:text-sm" >₱</span>
                                                                                                                                                    </div>

                                                                                                                                                    <input type="number" id="expense-category-budget" name="budget" step="0.01" min="0" class="pl-7 mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                    </div>

                                                                                                                                                </div>

                                                                                                                                            </div>

                                                                                                                                            <div class="mt-6 flex justify-end" >

                                                                                                                                                <button type="button" class="close-modal mr-3 inline-flex justify-center py-2 px-4 border border-border-color rounded-md shadow-sm text-sm font-medium text-primary-text bg-input-bg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Cancel </button>

                                                                                                                                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Save </button>

                                                                                                                                            </div>

                                                                                                                                        </form>

                                                                                                                                    </div>

                                                                                                                                </div>
                                                                                                                                <!-- Delete Confirmation Modal -->
                                                                                                                                    <div id="delete-confirmation-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden" >

                                                                                                                                        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" >

                                                                                                                                        </div>

                                                                                                                                        <div class="relative bg-card-bg rounded-lg shadow-xl max-w-md w-full mx-4" >

                                                                                                                                            <div class="p-6" >

                                                                                                                                                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full" >
                                                                                                                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="mt-4 text-center" >

                                                                                                                                                        <h3 id="delete-modal-title" class="text-lg font-medium text-primary-text" >Delete Item</h3>

                                                                                                                                                        <p id="delete-modal-message" class="mt-2 text-sm text-secondary-text" > Are you sure you want to delete this item? This action cannot be undone. </p>

                                                                                                                                                    </div>

                                                                                                                                                </div>

                                                                                                                                                <form id="delete-form" method="POST" class="px-6 py-4 bg-gray-50 text-right rounded-b-lg" >
                                                                                                                                                    @csrf @method('DELETE')
                                                                                                                                                    <button type="button" class="close-modal mr-3 inline-flex justify-center py-2 px-4 border border-border-color rounded-md shadow-sm text-sm font-medium text-primary-text bg-input-bg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Cancel </button>

                                                                                                                                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" > Delete </button>

                                                                                                                                                </form>

                                                                                                                                            </div>

                                                                                                                                        </div>
                                                                                                                                    @endsection
                                                                                                                                    @section('scripts') <script> document.addEventListener('DOMContentLoaded', function() { // Tab functionality const tabButtons = document.querySelectorAll('.tab-btn'); const tabContents = document.querySelectorAll('.tab-content'); function setActiveTab(tabId) { // Remove active class from all buttons and hide all contents tabButtons.forEach(btn => { btn.classList.remove('active', 'border-accent', 'text-accent'); btn.classList.add('border-transparent', 'text-secondary-text', 'hover:text-primary-text', 'hover:border-gray-300'); }); tabContents.forEach(content => { content.classList.add('hidden'); }); // Add active class to clicked button and show corresponding content const activeButton = document.getElementById(tabId); const activeContent = document.getElementById('content-' + tabId.replace('tab-', '')); activeButton.classList.add('active', 'border-accent', 'text-accent'); activeButton.classList.remove('border-transparent', 'text-secondary-text', 'hover:text-primary-text', 'hover:border-gray-300'); activeContent.classList.remove('hidden'); } tabButtons.forEach(button => { button.addEventListener('click', () => { setActiveTab(button.id); }); }); // Payment Method Modal Functionality const paymentMethodModal = document.getElementById('payment-method-modal'); const addPaymentMethodBtn = document.getElementById('add-payment-method-btn'); const paymentMethodForm = document.getElementById('payment-method-form'); const paymentMethodModalTitle = document.getElementById('payment-method-modal-title'); const editPaymentMethodBtns = document.querySelectorAll('.edit-payment-method'); const deletePaymentMethodBtns = document.querySelectorAll('.delete-payment-method'); function openPaymentMethodModal(isEdit = false, data = null) { if (isEdit) { paymentMethodModalTitle.textContent = 'Edit Payment Method'; document.getElementById('payment-method-id').value = data.method_id; document.getElementById('payment-method-name').value = data.method_name; document.getElementById('payment-method-type').value = data.method_type; document.getElementById('payment-method-description').value = data.description; document.getElementById('payment-method-active').checked = data.is_active; paymentMethodForm.action = "/finance/payment-methods/" + data.method_id; paymentMethodForm.querySelector('input[name="_method"]').value = 'PUT'; } else { paymentMethodModalTitle.textContent = 'Add Payment Method'; paymentMethodForm.reset(); paymentMethodForm.action = "{{ route('finance.payment-methods.store') }}"; paymentMethodForm.querySelector('input[name="_method"]').value = 'POST'; } paymentMethodModal.classList.remove('hidden'); } function closePaymentMethodModal() { paymentMethodModal.classList.add('hidden'); } addPaymentMethodBtn.addEventListener('click', () => openPaymentMethodModal()); editPaymentMethodBtns.forEach(btn => { btn.addEventListener('click', function() { const id = this.dataset.id; // Fetch payment method data and open modal fetch("/finance/payment-methods/" + id + "/edit") .then(response => response.json()) .then(data => { openPaymentMethodModal(true, data); }); }); }); // Expense Category Modal Functionality const expenseCategoryModal = document.getElementById('expense-category-modal'); const addExpenseCategoryBtn = document.getElementById('add-expense-category-btn'); const expenseCategoryForm = document.getElementById('expense-category-form'); const expenseCategoryModalTitle = document.getElementById('expense-category-modal-title'); const editExpenseCategoryBtns = document.querySelectorAll('.edit-expense-category'); const deleteExpenseCategoryBtns = document.querySelectorAll('.delete-expense-category'); const budgetTrackingCheckbox = document.getElementById('expense-category-budget-tracking'); const budgetAmountContainer = document.getElementById('budget-amount-container'); function toggleBudgetAmount() { budgetAmountContainer.classList.toggle('hidden', !budgetTrackingCheckbox.checked); } budgetTrackingCheckbox.addEventListener('change', toggleBudgetAmount); function openExpenseCategoryModal(isEdit = false, data = null) { if (isEdit) { expenseCategoryModalTitle.textContent = 'Edit Expense Category'; document.getElementById('expense-category-id').value = data.id; document.getElementById('expense-category-name').value = data.name; document.getElementById('expense-category-description').value = data.description; document.getElementById('expense-category-color').value = data.color; document.getElementById('expense-category-budget-tracking').checked = data.budget_tracking; document.getElementById('expense-category-budget').value = data.budget; expenseCategoryForm.action = "/finance/expense-categories/" + data.id; expenseCategoryForm.querySelector('input[name="_method"]').value = 'PUT'; } else { expenseCategoryModalTitle.textContent = 'Add Expense Category'; expenseCategoryForm.reset(); document.getElementById('expense-category-color').value = '#' + Math.floor(Math.random()*16777215).toString(16); expenseCategoryForm.action = "{{ route('finance.expense-categories.store') }}"; expenseCategoryForm.querySelector('input[name="_method"]').value = 'POST'; } toggleBudgetAmount(); expenseCategoryModal.classList.remove('hidden'); } function closeExpenseCategoryModal() { expenseCategoryModal.classList.add('hidden'); } addExpenseCategoryBtn.addEventListener('click', () => openExpenseCategoryModal()); editExpenseCategoryBtns.forEach(btn => { btn.addEventListener('click', function() { const id = this.dataset.id; // Fetch expense category data and open modal fetch("/finance/expense-categories/" + id + "/edit") .then(response => response.json()) .then(data => { openExpenseCategoryModal(true, data); }); }); }); // Delete Confirmation Modal Functionality const deleteModal = document.getElementById('delete-confirmation-modal'); const deleteForm = document.getElementById('delete-form'); const deleteModalTitle = document.getElementById('delete-modal-title'); const deleteModalMessage = document.getElementById('delete-modal-message'); function openDeleteModal(type, id, name) { deleteModalTitle.textContent = `Delete ${type}`; deleteModalMessage.textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`; if (type === 'Payment Method') { deleteForm.action = `/finance/payment-methods/${id}`; } else if (type === 'Expense Category') { deleteForm.action = `/finance/expense-categories/${id}`; } deleteModal.classList.remove('hidden'); } function closeDeleteModal() { deleteModal.classList.add('hidden'); } deletePaymentMethodBtns.forEach(btn => { btn.addEventListener('click', function() { const id = this.dataset.id; const name = this.closest('tr').querySelector('td:first-child').textContent; openDeleteModal('Payment Method', id, name); }); }); deleteExpenseCategoryBtns.forEach(btn => { btn.addEventListener('click', function() { const id = this.dataset.id; const name = this.closest('tr').querySelector('td:first-child').textContent; openDeleteModal('Expense Category', id, name); }); }); // Close modal buttons document.querySelectorAll('.close-modal').forEach(btn => { btn.addEventListener('click', function() { paymentMethodModal.classList.add('hidden'); expenseCategoryModal.classList.add('hidden'); deleteModal.classList.add('hidden'); }); }); // Show/hide custom payment terms field const paymentTermsSelect = document.getElementById('payment_terms'); const customTermsField = document.getElementById('custom_payment_terms'); function toggleCustomTerms() { const isCustom = paymentTermsSelect.value === 'custom'; customTermsField.parentElement.style.display = isCustom ? 'block' : 'none'; } if (paymentTermsSelect) { paymentTermsSelect.addEventListener('change', toggleCustomTerms); toggleCustomTerms(); // Call on page load } // Late fees fields const enableLateFees = document.getElementById('enable_late_fees'); const lateFeeType = document.getElementById('late_fee_type'); const lateFeeValue = document.getElementById('late_fee_value'); function toggleLateFees() { const isEnabled = enableLateFees.checked; lateFeeType.parentElement.parentElement.style.display = isEnabled ? 'block' : 'none'; lateFeeValue.parentElement.parentElement.style.display = isEnabled ? 'block' : 'none'; } if (enableLateFees) { enableLateFees.addEventListener('change', toggleLateFees); toggleLateFees(); // Call on page load } }); </script> @endsection