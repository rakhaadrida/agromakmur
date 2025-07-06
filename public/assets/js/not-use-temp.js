// Menu Barang Masuk

/* Versi Input One By One
const kodeBrg = document.getElementById("kodeBarang");
const namaBrg = document.getElementById("namaBarang");
const harga = document.getElementById("harga");

kodeBrg.addEventListener('change', displayAll);
namaBrg.addEventListener('change', displayAll);

function displayAll(e) {
  @foreach($barang as $br)
    if(('{{ $br->nama }}' == e.target.value) || ('{{ $br->id }}' == e.target.value)) {
      kodeBrg.value = '{{ $br->id }}';
      namaBrg.value = '{{ $br->nama }}';
    }
  @endforeach
  @foreach($harga as $hb)
    if(('{{ $hb->id_barang }}' == kodeBrg.value) && ('{{ $hb->id_harga }}' == 'HRG01')) {
      harga.value = '{{ $hb->harga }}';
    }
  @endforeach
}

function displayEditable(no) {
  document.getElementById("editButton"+no).style.display = "none";
  document.getElementById("updateButton"+no).style.display = "block";
  document.getElementById("removeButton"+no).style.display = "none";
  document.getElementById("cancelButton"+no).style.display = "block";
  let rowQty = document.querySelectorAll(".editQty"+no);
  let rowKet = document.querySelectorAll(".editKet"+no);
  document.getElementById("editHead").innerText = "Simpan"
  document.getElementById("deleteHead").innerText = "Batal"

  rowQty.forEach(function(e) {
    const editQty = `
      <input type="text" name="editQty[]" class="form-control form-control-sm text-bold" 
      value="${e.innerText}">
    `;
    $(e).empty();
    $(e).append(editQty);
    // e.contentEditable = true;
    // e.style.backgroundColor = "lightgrey";
    // e.style.color = "black";
  })

  rowKet.forEach(function(e) {
    const editKet = `
      <input type="text" name="editKet[]" class="form-control form-control-sm text-bold" value="${e.innerText}">
    `;
    $(e).empty();
    $(e).append(editKet);
  })

  return false;
}

function cancelEditable(no) {
  document.getElementById("updateButton"+no).style.display = "none";
  document.getElementById("editButton"+no).style.display = "block";
  document.getElementById("cancelButton"+no).style.display = "none";
  document.getElementById("removeButton"+no).style.display = "block";
  document.getElementById("editHead").innerText = "Edit";
  document.getElementById("deleteHead").innerText = "Delete";
  const tdQty = document.getElementById("editableQty"+no);
  const tdKet = document.getElementById("editableKet"+no);
  const inputQty = tdQty.getElementsByTagName('input')[0];
  const inputKet = tdKet.getElementsByTagName('input')[0];
  const isiQty = inputQty.value;
  const isiKet = inputKet.value;
  $(tdQty).empty();
  $(tdKet).empty();
  tdQty.innerText = isiQty;
  tdKet.innerText = isiKet;

  return false;
}

function checkEditable(e) {
  var j = 0;
  for(let i = 1; i <= '{{ $itemsRow }}'; i++) {
    var getRow = document.getElementById("updateButton"+i);
    if(getRow.style.display == "block") {
      j = 1;
    }
  }

  if(j == 1) {
    alert(`Silahkan simpan perubahan terlebih dahulu`);
    return false;
  }
  else {
    document.getElementById("submitBM").formMethod = "POST";
    document.getElementById("submitBM").formAction = '{{ route('bm-process', $newcode) }}';
  }
}

function resetSupplier(e) {
  supplier.removeAttribute("readonly");
  supplier.value = "";
  return false;
}

function resetTable(e) {
  const tbody = document.getElementsByTagName('tbody')[0];
  const baris = tbody.querySelectorAll(".barisBM");
  baris.forEach(function(e) {
    tbody.removeChild(baris);
  });

  return false;
} 

/* End Versi Input One By One */


// Menu Transfer Barang

/* Versi Input One By One
const kodeBrg = document.getElementById("kodeBarang");
const namaBrg = document.getElementById("namaBarang");
const gudangAsal = document.getElementById("gudangAsal");
const kodeAsal = document.getElementById("kodeAsal");
const gudangTujuan = document.getElementById("gudangTujuan");
const kodeTujuan = document.getElementById("kodeTujuan");
const qtyAsal = document.getElementById("qtyAsal");
const qtyTujuan = document.getElementById("qtyTujuan");

kodeBrg.addEventListener('change', displayAll);
namaBrg.addEventListener('change', displayAll);
gudangAsal.addEventListener('change', displayAsal);
gudangTujuan.addEventListener('change', displayTujuan);

function displayAll(e) {
  @foreach($barang as $br)
    if(('{{ $br->nama }}' == e.target.value) || ('{{ $br->id }}' == e.target.value)) {
      kodeBrg.value = '{{ $br->id }}';
      namaBrg.value = '{{ $br->nama }}';
    }
  @endforeach
}

function displayAsal(e) {
  @foreach($stok as $s)
    if(('{{ $s->gudang->nama }}' == e.target.value) && ('{{ $s->id_barang }}' == kodeBrg.value)) {
      qtyAsal.value = '{{ $s->stok }}';
      kodeAsal.value = '{{ $s->id_gudang }}';
    }
  @endforeach
  if('{{ $itemsRow != 0 }}') {
    @foreach($items as $item)
      if(('{{ $item->id_asal }}' == kodeAsal.value) && ('{{ $item->id_barang }}' == kodeBrg.value)) {
        qtyAsal.value -= '{{ $item->qty }}';
      }
    @endforeach
  }
}

function displayTujuan(e) {
  @foreach($stok as $s)
    if(('{{ $s->gudang->nama }}' == e.target.value) && ('{{ $s->id_barang }}' == kodeBrg.value)) {
      qtyTujuan.value = '{{ $s->stok }}';
      kodeTujuan.value = '{{ $s->id_gudang }}';
    }
  @endforeach
}
*/


// Menu Sales Order

/** Versi Input One By One
// const diskonFaktur = document.getElementById('diskonFaktur');
const angkaDF = document.getElementById('angkaDF');
const totalNotPpn = document.getElementById('totalNotPPN');

namaBrg.addEventListener('change', displayHarga);
kodeBrg.addEventListener('change', displayHarga);
qty.addEventListener('change', displayJumlah);
diskon.addEventListener('change', displayTotal);
// diskonFaktur.addEventListener('change', displayDiskon);

// function displayDiskon(e) {
   angkaDF.value = (e.target.value * subTotal.value) / 100;
   totalNotPpn.value = subTotal.value - angkaDF.value;
   ppn.value = totalNotPpn.value * 10 / 100;
   grandtotal.value = +totalNotPpn.value + +ppn.value;
} 

function displayEditable(no) {
  document.getElementById("editButton"+no).style.display = "none";
  document.getElementById("updateButton"+no).style.display = "block";
  let row = document.querySelectorAll(".editable"+no);

  row.forEach(function(e) {
    e.contentEditable = true;
    e.style.backgroundColor = "lightgrey";
    e.style.color = "black";
  })

  return false;
}

// function processEditable(no) {
  let editableBarang = document.getElementById("editableBarang"+no);
  let editableQty = document.getElementById("editableQty"+no);
  const itemsEdit = [];

  itemsEdit.push({
    barang: editableBarang.value;
    qty: editableQty.value;
  })  
  
  $.ajax({
    url: '/po/update',
    type: 'post',
    data: itemsEdit
  });
} 

function displayJumlah(e) {
  jumlah.value = e.target.value * harga.value;
}

function displayTotal(e) {
  let totalHarga = qty.value * harga.value;
  let besarDiskon = (e.target.value * totalHarga) / 100;
  jumlah.value = totalHarga - besarDiskon;
} 

*/
