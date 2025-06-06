@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">إنشاء كوتيشا جديدة</h2>

    <form method="POST" action="{{ route('quotations.store') }}" id="quotation-form">
        @csrf

        <div class="mb-3">
            <label class="form-label">الزبون</label>
            <select name="customer_id" class="form-select" required>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">تاريخ الكوتيشا</label>
            <input type="date" name="quotation_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <div class="d-flex align-items-start gap-4">
            <div class="flex-grow-1">
                <table class="table table-bordered text-center" id="quotation-lines">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30px;"></th>
                            <th>المنتج</th>
                            <th>الطول</th>
                            <th>العرض</th>
                            <th>الكمية</th>
                            <th>سعر الوحدة</th>
                            <th>الإجمالي</th>
                            <th>تحكم</th>
                        </tr>
                    </thead>
                    <tbody id="lines-body"></tbody>
                </table>

                <button type="button" class="btn btn-secondary mb-3" onclick="addLine()">إضافة بند</button>
            </div>

            <div class="summary-box p-3 bg-white border shadow-sm rounded">
                <div class="mb-2">
                    <label>الخصم</label>
                    <input type="number" name="discount_amount" class="form-control" value="0" step="0.01" oninput="calculateTotals()">
                </div>
                <div class="mb-2">
                    <label>الضريبة (5%)</label>
                    <input type="number" name="tax_amount" id="tax-amount" class="form-control" readonly>
                </div>
                <div>
                    <label>الإجمالي النهائي</label>
                    <input type="number" name="grand_total" class="form-control" readonly>
                </div>
            </div>
        </div>

        <input type="hidden" name="total_amount">
        <button type="submit" class="btn btn-primary mt-4">حفظ الكوتيشا</button>
    </form>
</div>

@include('quotations.scripts')
@endsection
