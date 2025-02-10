<div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Date Range</label>
                <select name="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="week" @selected(request('period') === 'week')>This Week</option>
                    <option value="month" @selected(request('period') === 'month')>This Month</option>
                    <option value="quarter" @selected(request('period') === 'quarter')>This Quarter</option>
                    <option value="year" @selected(request('period') === 'year')>This Year</option>
                    <option value="custom" @selected(request('period') === 'custom')>Custom Range</option>
                </select>
            </div>

            <div class="custom-range @if(request('period') !== 'custom') hidden @endif">
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div class="custom-range @if(request('period') !== 'custom') hidden @endif">
                <label class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Methods</option>
                    <option value="card" @selected(request('payment_method') === 'card')>Card</option>
                    <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>
