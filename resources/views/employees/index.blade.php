@extends('layouts.app')
@section('title', __('employee.employee'))
@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">{{ __('employee.employee') }}</h1>
    <a href="{{ route('employees.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 inline-flex items-center gap-1">
        + @lang('common.add') @lang('employee.employee')
    </a>
</div>

{{-- Filter Bar --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap gap-3 items-end">
        {{-- Search --}}
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">@lang('common.search')</label>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('employee.full_name') }}, {{ __('employee.nik') }}, {{ __('employee.employee_number') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        {{-- Employment Type --}}
        <div class="min-w-[180px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">@lang('employee.employment_type')</label>
            <select name="employment_type"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">— @lang('common.all') —</option>
                <option value="permanent"  {{ request('employment_type') === 'permanent'  ? 'selected' : '' }}>@lang('employee.permanent')</option>
                <option value="contract"   {{ request('employment_type') === 'contract'   ? 'selected' : '' }}>@lang('employee.contract')</option>
                <option value="probation"  {{ request('employment_type') === 'probation'  ? 'selected' : '' }}>@lang('employee.probation')</option>
                <option value="part_time"  {{ request('employment_type') === 'part_time'  ? 'selected' : '' }}>@lang('employee.part_time')</option>
                <option value="freelance"  {{ request('employment_type') === 'freelance'  ? 'selected' : '' }}>@lang('employee.freelance')</option>
            </select>
        </div>

        {{-- Status --}}
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">@lang('employee.status')</label>
            <select name="status"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">— @lang('common.all') —</option>
                <option value="active"     {{ request('status') === 'active'     ? 'selected' : '' }}>@lang('employee.active')</option>
                <option value="inactive"   {{ request('status') === 'inactive'   ? 'selected' : '' }}>@lang('employee.inactive')</option>
                <option value="resigned"   {{ request('status') === 'resigned'   ? 'selected' : '' }}>@lang('employee.resigned')</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>@lang('employee.terminated')</option>
            </select>
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
            @lang('common.filter')
        </button>

        @if(request()->hasAny(['search', 'employment_type', 'status']))
            <a href="{{ route('employees.index') }}"
               class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                @lang('common.reset')
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('employee.employee_number')</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('employee.full_name')</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.department')</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('common.position')</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('employee.employment_type')</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('employee.status')</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">@lang('employee.join_date')</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($employees ?? [] as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 font-mono text-xs">{{ $employee->employee_number ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            {{-- Photo Avatar --}}
                            @if(!empty($employee->photo))
                                <img src="{{ $employee->photo }}" alt="{{ $employee->full_name }}"
                                     class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-semibold">
                                    {{ strtoupper(substr($employee->full_name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $employee->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->department->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->position->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $typeColors = [
                                'permanent' => 'bg-blue-100 text-blue-700',
                                'contract'  => 'bg-yellow-100 text-yellow-700',
                                'probation' => 'bg-orange-100 text-orange-700',
                                'part_time' => 'bg-purple-100 text-purple-700',
                                'freelance' => 'bg-gray-100 text-gray-700',
                            ];
                            $typeColor = $typeColors[$employee->employment_type] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColor }}">
                            @lang('employee.' . $employee->employment_type)
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $statusColor = $employee->status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-500';
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            @lang('employee.' . $employee->status)
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('employees.show', $employee->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs font-medium">@lang('common.view')</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('employees.edit', $employee->id) }}"
                               class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">@lang('common.edit')</a>
                            <span class="text-gray-300">|</span>
                            <form method="POST" action="{{ route('employees.destroy', $employee->id) }}"
                                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-800 text-xs font-medium">@lang('common.delete')</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">
                        @lang('common.no_data')
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
