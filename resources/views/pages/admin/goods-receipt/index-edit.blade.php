@extends('layouts.admin')

@push('addon-style')
  <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Edit Goods Receipt</h1>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
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
                            <form action="" method="">
                                @csrf
                                <div class="container so-container">
                                    <div class="form-group row">
                                        <label for="number" class="col-2 col-form-label text-bold">Receipt Number</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control form-control-sm text-bold mt-1" name="number" id="number" value="{{ $number }}" tabindex="1" autofocus>
                                        </div>
                                        <label for="supplier" class="col-auto col-form-label text-bold">Supplier</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-4">
                                            <select class="selectpicker supplier-select-picker" name="supplier_id" id="supplier" data-live-search="true" title="Enter or Choose Supplier Name" tabindex="2">
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}" data-tokens="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-top: -10px">
                                        <label for="startDate" class="col-2 col-form-label text-bold">Start Date</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="start_date" id="startDate" value="{{ $startDate }}" tabindex="3">
                                        </div>
                                        <label for="finalDate" class="col-auto col-form-label text-bold ">Final Date</label>
                                        <span class="col-form-label text-bold ml-2">:</span>
                                        <div class="col-2">
                                            <input type="text" class="form-control datepicker form-control-sm text-bold mt-1" name="final_date" id="finalDate" value="{{ $finalDate }}" tabindex="4">
                                        </div>
                                        <div class="col-1 mt-1" style="margin-left: -10px">
                                            <button type="submit" formaction="" formmethod="GET" id="btnSearch" class="btn btn-primary btn-sm btn-block text-bold" tabindex="5">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="so-carousel" class="carousel slide" data-interval="false" wrap="false">
                                    <div class="carousel-inner">
                                        @foreach($goodsReceipts as $key => $goodsReceipt)
                                            <div class="carousel-item @if($item->id == $items[$items->count()-1]->id) active @endif">
                                                <div class="container so-update-container text-dark">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                            <label for="receiptNumber" class="col-2 form-control-sm text-bold mt-1">Receipt Number</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-2">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="receiptNumber" @if($items->count() != 0) value="{{ $item->id }}" @endif readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col" style="margin-left: -480px">
                                                        <div class="form-group row">
                                                            <label for="warehouse" class="col-3 form-control-sm text-bold mt-1">Warehouse</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-4">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="warehouse" @if($items->count() != 0) value="{{ $item->gudang->nama }}" @endif readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row customer-detail">
                                                            <label for="date" class="col-2 form-control-sm text-bold mt-1">Receipt Date</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-2">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="date" @if($items->count() != 0) value="{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-y') }}" @endif readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col" style="margin-left: -480px">
                                                        <div class="form-group row customer-detail">
                                                            <label for="supplier" class="col-3 form-control-sm text-bold mt-1">Supplier</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-8">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark text-wrap" id="supplier" @if($items->count() != 0) value="{{ $item->supplier->nama }}" @endif readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row customer-detail">
                                                            <label for="status" class="col-2 form-control-sm text-bold mt-1">Status</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark" id="status" value="{{ $goodsReceipt->status }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col" style="margin-left: -480px">
                                                        <div class="form-group row customer-detail">
                                                            <label for="dueDate" class="col-3 form-control-sm text-bold mt-1">Due Date</label>
                                                            <span class="col-form-label text-bold">:</span>
                                                            <div class="col-4">
                                                                <input type="text" class="form-control-plaintext col-form-label-sm text-bold text-dark text-wrap" id="dueDate" @if($items->count() != 0) value="{{ \Carbon\Carbon::parse($item->tanggal)->add($item->tempo, 'days')->format('d-M-y') }}" @endif readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row customer-detail">
                                                            <label for="user" class="col-2 form-control-sm text-bold mt-1">Admin</label>
                                                            <span class="col-form-label text-bold">:</span>
                              <div class="col-3">
                                <input type="text" readonly class="form-control-plaintext col-form-label-sm text-bold text-dark"
                                @if($item->need_approval->count() != 0)
                                  value="{{ $item->need_approval->last()->status }}"
                                  @php $status = $item->need_approval->last()->status; @endphp
                                @else
                                  value="{{ $item->status }}"
                                  @php $status = $item->status; @endphp
                                @endif
                                >
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Tabel Data Detil PO -->
                      <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="tablePO">
                        <thead class="text-center text-bold text-dark">
                          <td style="width: 50px">No</td>
                          <td style="width: 100px">Kode</td>
                          <td>Nama Barang</td>
                          <td style="width: 80px">Qty</td>
                          <td>Harga</td>
                          <td>Jumlah</td>
                        </thead>
                        <tbody>
                          @if($items->count() != 0)
                            @php
                              $i = 1; $subtotal = 0;
                              if(($item->need_approval->count() != 0) && ($item->need_approval->last()->status == 'PENDING_UPDATE')) {
                                $itemsDetail = \App\Models\NeedAppDetil::with(['barang'])
                                          ->where('id_app', $item->need_approval->last()->id)
                                          ->get();
                              } else {
                                $itemsDetail = \App\Models\DetilBM::with(['barang'])
                                        ->where('id_bm', $item->id)->get();
                              }
                            @endphp
                            @foreach($itemsDetail as $itemDet)
                              <tr class="text-dark">
                                <td align="center">{{ $i }}</td>
                                <td align="center">{{ $itemDet->id_barang }} </td>
                                <td>{{ $itemDet->barang->nama }}</td>
                                <td align="right">{{ $itemDet->qty }}</td>
                                <td align="right">
                                  {{ number_format($itemDet->harga, 0, "", ".") }}
                                </td>
                                <td align="right">
                                  {{number_format(($itemDet->qty * $itemDet->harga), 0, "", ".")}}
                                </td>
                                @php $subtotal += $itemDet->qty * $itemDet->harga;
                                @endphp
                              </tr>
                              @php $i++; @endphp
                            @endforeach
                          @else
                            <tr>
                              <td colspan="6" class="text-center text-bold h4 p-2"><i>Belum ada Detail BM</i></td>
                            </tr>
                          @endif
                        </tbody>
                      </table>

                      <div class="form-group row justify-content-end subtotal-so">
                        <label for="totalNotPPN" class="col-2 col-form-label text-bold text-right text-dark">Sub Total</label>
                        <span class="col-form-label text-bold">:</span>
                        <div class="col-2 mr-1">
                          <input type="text" name="totalNotPPN" id="totalNotPPN" readonly class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" value="{{ number_format($subtotal, 0, "", ".") }}"
                          />
                        </div>
                      </div>
                      <div class="form-group row justify-content-end total-so">
                        <label for="ppn" class="col-1 col-form-label text-bold text-right text-dark">PPN</label>
                        <span class="col-form-label text-bold">:</span>
                        <div class="col-2 mr-1">
                          <input type="text" name="ppn" id="ppn" readonly class="form-control-plaintext col-form-label-sm text-bold text-danger text-right" value="0"
                          />
                        </div>
                      </div>
                      <div class="form-group row justify-content-end grandtotal-so">
                        <label for="grandtotal" class="col-2 col-form-label text-bold text-right text-dark">Grand Total</label>
                        <span class="col-form-label text-bold">:</span>
                        <div class="col-2 mr-1">
                          <input type="text" name="grandtotal" id="grandtotal" readonly class="form-control-plaintext text-bold text-secondary text-lg text-right" value=" {{number_format($subtotal, 0, "", ".") }}"
                          />
                        </div>
                      </div>
                      <hr>
                      <!-- End Tabel Data Detil PO -->

                      @if(($status != 'BATAL') && ($status != 'PENDING_BATAL'))
                        <!-- Button Submit dan Reset -->
                        <div class="form-row justify-content-center">
                          <div class="col-2">
                            <a href="" tabindex="6" data-toggle="modal" data-target="#{{$item->id}}" class="btn btn-danger btn-block text-bold">Batal</a>
                             <button type="submit" formaction="" formmethod="POST" class="btn btn-danger btn-block text-bold">Ganti Status />
                          </div>
                          <div class="col-2">
                            <button type="submit" tabindex="7" formaction="{{ route('bm-edit', $item->id) }}" formmethod="POST" class="btn btn-info btn-block text-bold">Ubah</button>
                          </div>
                        </div>
                        <!-- End Button Submit dan Reset -->
                      @endif
                    </div>

                    <!-- Modal Ganti Status -->
                    <div class="modal" id="{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="{{$item->id}}" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="h2 text-bold">&times;</span>
                            </button>
                            <h5 class="modal-title">Ubah Status Barang Masuk <b>{{$item->id}}</b></h5>
                          </div>
                          <div class="modal-body">
                              <div class="form-group row">
                                <label for="kode" class="col-2 col-form-label text-bold">Status</label>
                                <span class="col-form-label text-bold">:</span>
                                <div class="col-2">
                                  <input type="text" readonly class="form-control-plaintext col-form-label-sm text-bold text-dark" name="statusUbah" value="BATAL">
                                </div>
                              </div>
                              <div class="form-group subtotal-so">
                                <label for="keterangan" class="col-form-label">Keterangan</label>
                                <input type="text" class="form-control" name="ket{{$item->id}}"
                                id="ket{{$item->id}}" data-toogle="tooltip" data-placement="bottom" title="Form keterangan harus diisi">
                              </div>
                              <hr>
                              <div class="form-row justify-content-center">
                                <div class="col-3">
                                  <button type="submit" class="btn btn-success btn-block text-bold" id="btn{{$item->id}}" onclick="return checkEditable({{$item->id}})">Simpan</button>
                                   formaction="{{ route('so-status', $item->id) }}" formmethod="POST"
                                </div>
                                <div class="col-3">
                                  <button button type="button" class="btn btn-outline-secondary btn-block text-bold" data-dismiss="modal">Batal</button>
                                </div>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
                @if(($items->count() > 0) && ($items->count() != 1))
                  <a class="carousel-control-prev" href="#so-carousel" role="button" data-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="sr-only">Previous</span>
                    </a>
                   @if($item->id != $items[$itemsRow-1]->id)
                    <a class="carousel-control-next " href="#so-carousel" role="button" data-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="sr-only">Next</span>
                    </a>
                   @endif
                @endif
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        $.fn.datepicker.dates['id'] = {
          days:["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],
          daysShort:["Mgu","Sen","Sel","Rab","Kam","Jum","Sab"],
          daysMin:["Min","Sen","Sel","Rab","Kam","Jum","Sab"],
          months:["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],
          monthsShort:["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"],
          today:"Hari Ini",
          clear:"Kosongkan"
        };

        $('.datepicker').datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          todayHighlight: true,
          language: 'id',
        });
    </script>
@endpush
