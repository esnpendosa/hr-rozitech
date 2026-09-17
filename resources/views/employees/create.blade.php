@extends('layouts.app')
@section('title', __('common.add') . ' ' . __('employee.employee'))
@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('employees.index') }}"
       class="text-gray-500 hover:text-gray-700 text-sm inline-flex items-center gap-1">
        ← @lang('common.back')
    </a>
    <span class="text-gray-300">/</span>
    <h1 class="text-2xl font-bold text-gray-800">@lang('common.add') @lang('employee.employee')</h1>
</div>

<form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- Section: Informasi Pribadi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5 pb-2 border-b border-gray-100">
            Informasi Pribadi
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- NIK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    @lang('employee.nik') <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nik" value="{{ old('nik') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 @error('nik') border-red-400 @enderror">
                @error('nik')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Full Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    @lang('employee.full_name') <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 @error('full_name') border-red-400 @enderror">
                @error('full_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('employee.gender')</label>
                <select name="gender"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">— @lang('common.filter') —</option>
                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>@lang('employee.male')</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>@lang('employee.female')</option>
                </select>
            </div>

            {{-- Religion --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('employee.religion')</label>
                <input type="text" name="religion" value="{{ old('religion') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            {{-- Marital Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('employee.marital_status')</label>
                <select name="marital_status"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">— @lang('common.filter') —</option>
                    <option value="single"   {{ old('marital_status') === 'single'   ? 'selected' : '' }}>@lang('employee.single')</option>
                    <option value="married"  {{ old('marital_status') === 'married'  ? 'selected' : '' }}>@lang('employee.married')</option>
                    <option value="divorced" {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>@lang('employee.divorced')</option>
                    <option value="widowed"  {{ old('marital_status') === 'widowed'  ? 'selected' : '' }}>@lang('employee.widowed')</option>
                </select>
            </div>

            {{-- Birth Place --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('employee.birth_place')</label>
                <input type="text" name="birth_place" value="{{ old('birth_place') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            {{-- Birth Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('employee.birth_date')</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.phone')</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.email')</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            {{-- Address --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.address')</label>
                <textarea name="address" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">{{ old('address') }}</textarea>
            </div>

        </div>
    </div>

    {{-- Section: Kepegawaian --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5 pb-2 border-b border-gray-100">
            @lang('common.employment')
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Branch --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.branch')</label>
                <select name="branch_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">— @lang('common.filter') —</option>
                    @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Department --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.department')</label>
                <select name="department_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">— @lang('common.filter') —</option>
                    @foreach($departments ?? [] as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Division --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.division')</label>
                <select name="division_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">— @lang('common.filter') —</option>
                    @foreach($divisions ?? [] as $division)
                        <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Team --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.team')</label>
                <select name="team_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">— @lang('common.filter') —</option>
                    @foreach($teams ?? [] as $team)
                        <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Position --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">@lang('common.position')</label>
                <select name="position_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">— @lang('common.filter') —</option>
                    @foreach($positions ?? [] as $position)
                        <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Employment Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    @lang('employee.employment_type') <span class="text-red-500">*</span>
                </label>
                <select name="employment_type" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 @error('employment_type') border-red-400 @enderror">
                    <option value="">— @lang('common.filter') —</option>
                    <option value="permanent"  {{ old('employment_type') === 'permanent'  ? 'selected' : '' }}>@lang('employee.permanent')</option>
                    <option value="contract"   {{ old('employment_type') === 'contract'   ? 'selected' : '' }}>@lang('employee.contract')</option>
                    <option value="probation"  {{ old('employment_type') === 'probation'  ? 'selected' : '' }}>@lang('employee.probation')</option>
                    <option value="part_time"  {{ old('employment_type') === 'part_time'  ? 'selected' : '' }}>@lang('employee.part_time')</option>
                    <option value="freelance"  {{ old('employment_type') === 'freelance'  ? 'selected' : '' }}>@lang('employee.freelance')</option>
                </select>
                @error('employment_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Join Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    @lang('employee.join_date') <span class="text-red-500">*</span>
                </label>
                <input type="date" name="join_date" value="{{ old('join_date') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 @error('join_date') border-red-400 @enderror">
                @error('join_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Employee Number (optional) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    @lang('employee.employee_number')
                    <span class="text-gray-400 font-normal text-xs">({{ __('common.filter') }})</span>
                </label>
                <input type="text" name="employee_number" value="{{ old('employee_number') }}"
                       placeholder="{{ __('employee.employee_number') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3">
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
            @lang('common.save')
        </button>
        <a href="{{ route('employees.index') }}"
           class="border border-gray-300 text-gray-600 px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
            @lang('common.cancel')
        </a>
    </div>

</form>

@endsection
