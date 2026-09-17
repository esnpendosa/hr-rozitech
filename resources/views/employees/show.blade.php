@extends('layouts.app')
@section('title', __('employee.employee'))
@section('content')

{{-- Back + Page Header --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('employees.index') }}"
       class="text-gray-500 hover:text-gray-700 text-sm inline-flex items-center gap-1">
        ← @lang('common.back')
    </a>
    <span class="text-gray-300">/</span>
    <h1 class="text-2xl font-bold text-gray-800">@lang('common.detail') @lang('employee.employee')</h1>
</div>

{{-- Employee Summary Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 flex items-center gap-5">
    {{-- Avatar --}}
    <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold flex-shrink-0">
        ?
    </div>
    <div>
        <p class="text-xl font-bold text-gray-800">—</p>
        <p class="text-sm text-gray-500">{{ __('employee.employee_number') }}: —</p>
    </div>
    <div class="ml-auto flex gap-2">
        <a href="{{ route('employees.edit', $id ?? 0) }}"
           class="border border-yellow-400 text-yellow-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-50">
            @lang('common.edit')
        </a>
    </div>
</div>

{{-- Tabbed Layout (Alpine.js) --}}
<div x-data="{ activeTab: 'profile' }">

    {{-- Tab Nav --}}
    <div class="flex gap-1 border-b border-gray-200 mb-0 bg-white rounded-t-xl px-4 pt-3">
        @php
            $tabs = [
                'profile'           => __('common.profile'),
                'employment'        => __('common.employment'),
                'contract'          => __('common.contract'),
                'document'          => __('common.document'),
                'emergency_contact' => __('common.emergency_contact'),
                'history'           => __('common.history'),
            ];
        @endphp
        @foreach($tabs as $tabKey => $tabLabel)
            <button @click="activeTab = '{{ $tabKey }}'"
                    :class="activeTab === '{{ $tabKey }}'
                        ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
                        : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm focus:outline-none whitespace-nowrap">
                {{ $tabLabel }}
            </button>
        @endforeach
    </div>

    {{-- Tab: Profile --}}
    <div x-show="activeTab === 'profile'" x-cloak
         class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">@lang('common.profile')</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.nik')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.full_name')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.gender')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.religion')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.marital_status')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.birth_place')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.birth_date')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.phone')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.email')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.address')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
        </dl>
    </div>

    {{-- Tab: Employment --}}
    <div x-show="activeTab === 'employment'" x-cloak
         class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">@lang('common.employment')</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.employee_number')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.branch')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.department')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.division')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.team')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('common.position')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.employment_type')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.join_date')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">@lang('employee.status')</dt>
                <dd class="text-gray-800">—</dd>
            </div>
        </dl>
    </div>

    {{-- Tab: Contracts --}}
    <div x-show="activeTab === 'contract'" x-cloak
         class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 overflow-hidden">
        <div class="px-6 pt-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-700">@lang('common.contract')</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.type')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.start_date')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.end_date')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.salary')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('employee.status')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.notes')</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                        @lang('common.no_data')
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Tab: Documents --}}
    <div x-show="activeTab === 'document'" x-cloak
         class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 overflow-hidden">
        <div class="px-6 pt-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-700">@lang('common.document')</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.type')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.title')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.file')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.created_at')</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                        @lang('common.no_data')
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Tab: Emergency Contacts --}}
    <div x-show="activeTab === 'emergency_contact'" x-cloak
         class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 overflow-hidden">
        <div class="px-6 pt-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-700">@lang('common.emergency_contact')</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.name')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.relationship')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.phone')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.address')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.primary')</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                        @lang('common.no_data')
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Tab: History --}}
    <div x-show="activeTab === 'history'" x-cloak
         class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 overflow-hidden">
        <div class="px-6 pt-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-700">@lang('common.history')</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.field')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.old_value')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.new_value')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.changed_by')</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.changed_at')</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                        @lang('common.no_data')
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>{{-- end x-data --}}

@endsection

@push('scripts')
{{-- Alpine.js for tab switching --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>[x-cloak] { display: none !important; }</style>
@endpush
