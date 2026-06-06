@extends('layouts.admin')

@section('title', 'Return Details - REGASCO SIS')
@section('page-title', 'Supplier Return Details')

@section('admin-content')
<div class="mb-6">
    <a href="{{ route('admin.supplier-returns.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Returns
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Return Status</h3>
                @php
                    $badge = $return->status_badge ?? ['bg-gray-100 text-gray-700', 'fa-question-circle'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badge[0] }}">
                    <i class="fas {{ $badge[1] }} mr-2"></i>
                    {{ ucfirst($return->status) }}
                </span>
            </div>
            
            <!-- Status Timeline - Only Completed & Rejected -->
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    @foreach(['completed', 'rejected'] as $step)
                        @php
                            $stepConfig = [
                                'completed' => ['label' => 'Completed', 'icon' => 'fa-check-circle', 'color' => 'green'],
                                'rejected' => ['label' => 'Rejected', 'icon' => 'fa-times-circle', 'color' => 'red'],
                            ];
                            $isActive = $return->status === $step;
                            $isPast = in_array($return->status, array_slice(['completed', 'rejected'], 0, array_search($step, ['completed', 'rejected']) + 1));
                        @endphp
                        
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-2 {{ $isActive ? 'bg-' . $stepConfig[$step]['color'] . '-500 text-white' : ($isPast ? 'bg-' . $stepConfig[$step]['color'] . '-100 text-' . $stepConfig[$step]['color'] . '-600' : 'bg-gray-100 text-gray-400') }}">
                                <i class="fas {{ $stepConfig[$step]['icon'] }}"></i>
                            </div>
                            <span class="text-xs font-medium {{ $isActive ? 'text-gray-800' : 'text-gray-500' }}">{{ $stepConfig[$step]['label'] }}</span>
                        </div>
                        
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 bg-gray-200 mx-4 mb-6"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Return Details -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Return Information</h3>
            </div>
            <div class="p-6 grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Supplier</p>
                    <p class="font-medium text-gray-800">{{ $return->supplier->supplier_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Product</p>
                    <p class="font-medium text-gray-800">{{ $return->product->product_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Quantity</p>
                    <p class="font-medium text-gray-800">{{ $return->quantity }} units</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Return Date</p>
                    <p class="font-medium text-gray-800">{{ $return->return_date->format('F d, Y') }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500 mb-1">Reason</p>
                    <p class="font-medium text-gray-800">{{ $return->reason }}</p>
                </div>
                @if($return->notes)
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500 mb-1">Notes</p>
                        <p class="text-gray-600">{{ $return->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Processed By -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Processed By</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $return->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $return->user->role ?? 'Staff' }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-3">Processed on {{ $return->created_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                @if($return->status === 'completed')
                    <form action="{{ route('admin.supplier-returns.update-status', $return) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <!-- FIX: Changed name from "status" to "return_status" to match controller validation -->
                        <input type="hidden" name="return_status" value="rejected">
                        <button type="submit" class="w-full bg-red-50 text-red-600 py-2 rounded-lg hover:bg-red-100 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i> Mark as Rejected
                        </button>
                    </form>
                @elseif($return->status === 'rejected')
                    <form action="{{ route('admin.supplier-returns.update-status', $return) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <!-- FIX: Changed name from "status" to "return_status" to match controller validation -->
                        <input type="hidden" name="return_status" value="completed">
                        <button type="submit" class="w-full bg-green-50 text-green-600 py-2 rounded-lg hover:bg-green-100 transition-colors font-medium">
                            <i class="fas fa-check mr-2"></i> Mark as Completed
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('admin.supplier-returns.index') }}" class="block w-full text-center bg-gray-50 text-gray-600 py-2 rounded-lg hover:bg-gray-100 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection