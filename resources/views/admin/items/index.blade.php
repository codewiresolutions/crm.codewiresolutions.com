@extends('layouts.admin')

@section('content')

 <div class="mb-4 md:flex items-center justify-between rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="bg-blue-50 text-blue-700 text-4xl h-14 w-20 sm:w-14 flex items-center justify-center rounded-xl"><i class="ri-box-3-line"></i></span>
        
        <div>
            <h2 class="m-0 text-xl font-semibold">Items</h2>
            <p class="text-sm text-gray-500">Create, update, and manage your inventroy items.</p>
        </div>
        </div>
        <div class="flex gap-2">
            <button type="button" class="w-auto rounded-lg bg-blue-600 px-4.5 py-2.5 text-white md:mt-0 mt-4 hover:bg-blue-700 text-sm" onclick="openitemsModal()"><i class="ri-add-line"></i> Add Item</button>
        </div>
    </div>

@endsection