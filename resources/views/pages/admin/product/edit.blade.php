@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Product - {{ $product->name }}</h1>
        </div>
        @if($errors->any())
            <div class="alert alert-danger alert-input-section">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card show">
                        <div class="card-body">
                            <form action="{{ route('products.update', $product->id) }}" method="POST" id="form">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold text-right">Name</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-6">
                                        <input type="text" class="form-control col-form-label-sm" name="name" id="name" value="{{ $product->name }}" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="category" class="col-2 col-form-label text-bold text-right">Category</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <select class="selectpicker custom-select-picker" name="category_id" id="category" data-live-search="true">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" data-tokens="{{ $category->name }}" @if($product->category_id == $category->id) selected @endif>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="subcategory" class="col-2 col-form-label text-bold text-right">Subcategory</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <select class="selectpicker custom-select-picker" name="subcategory_id" id="subcategory" data-live-search="true" title="Select Category First">
                                            @foreach($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}" data-tokens="{{ $subcategory->name }}" @if($product->subcategory_id == $subcategory->id) selected @endif>{{ $subcategory->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('subcategory_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="unit" class="col-2 col-form-label text-bold text-right">Unit</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <select class="selectpicker custom-select-picker" name="unit_id" id="unit" data-live-search="true">
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" data-tokens="{{ $unit->name }}" @if($product->unit_id == $unit->id) selected @endif>{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('unit')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="conversion" class="col-2 col-form-label text-bold text-right"></label>
                                    <span class="col-form-label text-bold"></span>
                                    <div class="col-6 ml-1">
                                        <input class="form-check-input product-check-input" type="checkbox" name="conversion" id="conversion" {{ $product->productConversions->count() ? 'checked' : '' }}>
                                        <label class="col-form-label product-check-label ml-4" for="remember">Does this product have unit conversion?</label>
                                    </div>
                                </div>
                                <div id="conversionSection" @if(empty($product->productConversions)) hidden @endif>
                                    @foreach($product->productConversions as $conversion)
                                        <div class="form-group row">
                                            <label for="unitConversion" class="col-2 col-form-label text-bold text-right">Unit Conversion</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-3">
                                                <select class="selectpicker custom-select-picker" name="unit_conversion_id" id="unitConversion" data-live-search="true">
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}" data-tokens="{{ $unit->name }}" @if($conversion->unit_id == $unit->id) selected @elseif($product->unit_id == $unit->id) hidden @endif>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('unit_conversion_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="quantity" class="col-2 col-form-label text-bold text-right">Quantity</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-2">
                                                <input type="number" min="1" class="form-control col-form-label-sm" name="quantity" id="quantity" value="{{ $conversion->quantity }}">
                                            </div>
                                            <span class="col-form-label text-bold" id="primaryUnit">{{ $product->unit->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <hr>
                                @if(!empty($prices) && $prices->count() > 0)
                                    <h5 class="h5 mb-3 text-gray-800 menu-title">Price List</h5>
                                @endif
                                @foreach($prices as $key => $price)
                                    <div class="form-row">
                                        <div class="form-group col-2">
                                            <label for="basePrice-{{ $key }}" class="col-form-label text-bold">Base Price</label>
                                            <input type="text" tabindex="-1" class="form-control col-form-label-sm" id="basePrice-{{ $key }}" name="base_price[]" value="{{ $productPrices[$price->id]['base_price'] }}" readonly/>
                                        </div>
                                        <div class="form-group col-2 ml-2">
                                            <label for="tax-{{ $key }}" class="col-form-label text-bold">Tax</label>
                                            <input type="text" tabindex="-1" class="form-control col-form-label-sm" id="tax-{{ $key }}" name="tax_amount[]" value="{{ $productPrices[$price->id]['tax_amount'] }}" readonly/>
                                        </div>
                                        <div class="form-group col-2 ml-2">
                                            <label for="price-{{ $key }}" class="col-form-label text-bold">{{ $price->name }}</label>
                                            <input type="text" class="form-control col-form-label-sm" id="price-{{ $key }}" name="price[]" value="{{ $productPrices[$price->id]['price'] }}" data-toogle="tooltip" data-placement="right" title="Only allowed to input numbers" required>
                                            <input type="hidden" name="price_id[]" value="{{ $price->id }}">
                                        </div>
                                    </div>
                                @endforeach
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-success btn-block text-bold" id="btnSubmit">Submit</button>
                                    </div>
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#category').on('change', function(event) {
                $('#subcategory').removeAttr('disabled');

                $.ajax({
                    url: '{{ route('subcategories.index-ajax') }}',
                    type: 'GET',
                    data: {
                        category_id: $(this).val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        let subcategory = $('#subcategory');
                        subcategory.empty();

                        $.each(data, function(index, item) {
                            subcategory.append(
                                $('<option></option>', {
                                    value: item.id,
                                    text: item.name,
                                    'data-tokens': item.name,
                                })
                            );

                            if(!index) {
                                subcategory.selectpicker({
                                    title: 'Choose Subcategory'
                                });
                            }

                            subcategory.selectpicker('refresh');
                            subcategory.selectpicker('render');
                        });
                    }
                })
            });

            $('#unit').on('change', function(event) {
                if($('#conversion').is(':checked')) {
                    renderSelectConversionUnit();
                }
            });

            $('#conversion').on('change', function(event) {
                if ($(this).is(':checked')) {
                    $('#conversionSection').removeAttr('hidden');

                    renderSelectConversionUnit();
                } else {
                    $('#conversionSection').attr('hidden', true);
                }
            });

            $('input[name="price[]"]').each(function(index) {
                let basePriceValue = $(`#basePrice-${index}`);
                let taxValue = $(`#tax-${index}`);

                basePriceValue.val(thousandSeparator(basePriceValue.val()));
                taxValue.val(thousandSeparator(taxValue.val()));
                this.value = thousandSeparator(this.value);

                $(this).on('keypress', function(event) {
                    if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                        $(`#price-${index}`).tooltip('show');
                        event.preventDefault();
                    }
                });

                $(this).on('keyup', function(event) {
                    let basePrice = Math.floor(numberFormat(this.value) / 1.1);
                    $(`#basePrice-${index}`).val(thousandSeparator(basePrice));

                    let tax = Math.floor(numberFormat(this.value) - basePrice);
                    $(`#tax-${index}`).val(thousandSeparator(tax));

                    this.value = currencyFormat(this.value);
                });
            });

            $('#btnSubmit').on('click', function(event) {
                event.preventDefault();

                $('input[name="price[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                $('input[name="tax_amount[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                $('input[name="base_price[]"]').each(function() {
                    this.value = numberFormat(this.value);
                });

                $('#form').submit();
            });

            function renderSelectConversionUnit() {
                let selectedUnit = $('#unit option:selected');
                let primaryUnitName = selectedUnit.text();
                let primaryUnitId = selectedUnit.val();
                $('#primaryUnit').text(`${primaryUnitName}`);

                let conversionUnit = $('#unitConversion');
                conversionUnit.empty();

                @foreach($units as $key => $unit)
                if (primaryUnitId !== '{{ $unit->id }}') {
                    conversionUnit.append(
                        $('<option></option>', {
                            value: '{{ $unit->id }}',
                            text: '{{ $unit->name }}',
                            'data-tokens': '{{ $unit->name }}'
                        })
                    );
                }

                if('{{ $key }}') {
                    conversionUnit.selectpicker({
                        title: 'Choose Unit Conversion',
                    });
                }
                @endforeach

                conversionUnit.selectpicker('refresh');
                conversionUnit.selectpicker('render');
            }

            function currencyFormat(value) {
                return value
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    ;
            }

            function numberFormat(value) {
                return +value.replace(/\./g, "");
            }

            function thousandSeparator(nStr) {
                nStr += '';
                x = nStr.split(',');
                x1 = x[0];
                x2 = x.length > 1 ? ',' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + '.' + '$2');
                }
                return x1 + x2;
            }
        });
    </script>
@endpush
