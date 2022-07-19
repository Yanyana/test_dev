<script>
import axios from 'axios';

const env = import.meta.env;

export default {
  data() {
    return {
      requestList: [],
      pageNo: 1,
      perPage: 5,
      lastPage: 1,
      API_URL: env.VITE_API_ENDPOINT,
      selectOptNik: '',
      optionsNik: [],
      user_id: '',
      nik: '',
      nama: '',
      departement: '',
      request_date: '',
      itemsAdd: [],
      itemsList: []
    }
  },
  watch: {
    selectOptNik(value) {
      console.log(value)
      if (value) {
        this.selectByNik(value)
      }
    },
    pageNo(value) {
      if(value) {
        this.GetRequestList()
      }
    }
  },
  created() {
    this.GetRequestList()
    this.GetItemstList()
    this.GetUsertList()
  },
  methods: {
    GetUsertList() {
      return axios.get(this.API_URL + 'request/departement')
        .then((res) => {
          this.optionsNik = res.data.data
        })
        .catch((err) => {
          console.log(err)
        })
    },
    simpanRequest(evt) {
      evt.preventDefault();
      if (this.itemsAdd.filter(el => el.items_id && el.qty_req > 0).length == 0 || !this.nik) {
        console.log(JSON.stringify(this.nik))
        return this.$swal(
          'Perhatian!',
          'Lengkapi Daftar Permintaan Barang.',
          'warning'
        );
      }
      return this.$swal({
        title: 'Konfirmasi Simpan?',
        text: "Anda Yakin ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Simpan'
      }).then((result) => {
        if (result.isConfirmed) {
          return axios.post(this.API_URL + 'request/add', {
            user_id: this.user_id,
            request_date: this.request_date,
            items: this.itemsAdd
          })
            .then((res) => {
              if (res.data.status) {
                this.clearFormModal()
                this.GetRequestList()
                return this.$swal(
                  res.data.status,
                  res.data.message,
                  'success'
                );
              }
            })
            .catch((err) => {
              console.log(err)
            })
        }
      })
    },
    clearFormModal() {
      this.selectOptNik = ''
      this.user_id = ''
      this.nik = ''
      this.nama = ''
      this.departement = ''
      this.request_date = ''
      this.itemsAdd = []
    },
    addRows() {
      this.itemsAdd.push({
        items_id: null,
        name: null,
        location: null,
        qty_stock: null,
        qty_req: null,
        unit: null,
        note: null,
      })
    },
    removeRows(index) {
      this.itemsAdd.splice(index, 1)
    },
    selectByNik(evt) {
      let { id, name, nik, departement } = JSON.parse(evt)
      if (id) {
        this.nik = nik
        this.nama = name
        this.departement = departement
        this.user_id = id
      }
    },
    selectItems(evt, index) {
      let { id, name, location, unit, qty_stock } = JSON.parse(evt.target.value)
      if (id) {
        this.itemsAdd[index].items_id = id
        this.itemsAdd[index].name = name
        this.itemsAdd[index].location = location
        this.itemsAdd[index].qty_stock = qty_stock
        this.itemsAdd[index].unit = unit
        this.itemsAdd[index].note = ''
      }

    },
    GetItemstList() {
      return axios.get(this.API_URL + 'request/items')
        .then((res) => {
          this.itemsList = res.data.data
        })
        .catch((err) => {
          console.log(err)
        })
    },
    GetRequestList() {
      return axios.get(this.API_URL + 'request?page=' + this.pageNo + '&per_page=' + this.perPage)
        .then((res) => {
          this.requestList = []

          Object.keys(res.data.data)
            .forEach(element => {
              this.requestList.push({
                req_number: element,
                request_date: res.data.data[element].at(0)['request_date'],
                request_by: res.data.data[element].at(0)['request_by'],
                details: res.data.data[element]
              })
          });

          this.lastPage = res.data.last_page
        })
        .catch((err) => {
          console.log(err)
        })
    }
  }
}
</script>

<style>
  /* .btn-rounded {
    border-radius: 10% !important;
  } */
</style>

<template>
  <div class="card mt-4">
    <div class="card-header row">
      <div class="col-8">
        <h4>Form Permintaan</h4>
      </div>

      <div class="col-4 text-right">
        <button
          class="btn btn-outline-success"
          data-toggle="modal"
          data-target="#modalAdd"
        >
          Tambah
        </button>
      </div>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nomor Permintaan</th>
            <th scope="col">Tanggal Permintaan</th>
            <th scope="col">Peminta</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody
          v-for="(item, index) in requestList"
          :key="index"
        >
          <tr>
            <th scope="row">{{ index +=1 }}</th>
            <td>{{ item.req_number }}</td>
            <td>{{ item.request_date }}</td>
            <td>{{ item.request_by }}</td>
            <td>
              <button
                class="btn btn-sm btn-outline-info"
                @click="item.open = !item.open"
              >
                Detail
              </button>
            </td>
          </tr>

          <tr v-if="item.open">
            <td colspan="5">
              <table class="table">
                <thead>
                  <tr>
                    <th>
                      Barang
                    </th>

                    <th>
                      Lokasi
                    </th>

                    <th>
                      unit
                    </th>

                    <th>
                      Jumlah Permintaan
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(child, c) in item.details" :key="c">
                    <td>
                      {{ child.name }}
                    </td>

                    <td>
                      {{ child.location }}
                    </td>

                    <td>
                      {{ child.unit }}
                    </td>

                    <td>{{ child.qty_req }}</td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="text-center">
        <nav aria-label="Page navigation example">
          <ul class="pagination justify-content-center">
            <li class="page-item">
              <a
                v-if="pageNo > 1"
                class="page-link"
                href="#"
                aria-label="Previous"
                @click="pageNo = 1"
              >
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li
              v-for="page in lastPage"
              :key="page"
              class="page-item"
              @click="pageNo = page"
            >
              <a class="page-link" href="#">
                {{ page }}
              </a>
            </li>
            <li class="page-item" @click="pageNo = lastPage">
              <a v-if="pageNo !== lastPage" class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAddLabel">Tambah Permintaan</h5>
            <button
              id="btn-close"
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="clearFormModal"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label for="nik">NIK Peminta</label>
                  <select
                    v-model="selectOptNik"
                    class="custom-select" id="nik" aria-label="select nik"
                  >
                    <option selected>Pilih NIK</option>
                    <option
                      v-for="(opt, x) in optionsNik"
                      :key="x"
                      :value="JSON.stringify(opt)"
                    >
                      {{ opt.nik }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <label for="nama">Nama</label>
                  <input
                    id="nama"
                    v-model="nama"
                    type="text"
                    class="form-control"
                    placeholder="Nama"
                    disabled
                  >
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <label for="dept">Departement</label>
                  <input
                    id="dept"
                    v-model="departement"
                    type="text"
                    class="form-control"
                    placeholder="Departement" disabled>
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <label for="date">Tanggal Permintaan</label>
                  <input
                    v-model="request_date"
                    type="date"
                    class="form-control" id="date" placeholder="Tanggal Permintaan">
                </div>
              </div>
            </div>

            <div>
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Barang</th>
                    <th scope="col">Lokasi</th>
                    <th scope="col">Tersedia</th>
                    <th scope="col">Kuantiti</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in itemsAdd"
                    :key="index"
                  >
                    <th scope="row">{{ index + 1 }}</th>
                    <td>
                      <select
                        class="custom-select"
                        @change="selectItems($event, index)"
                      >
                        <option selected>Pilih Barang</option>
                        <option
                          v-for="(list, x) in itemsList"
                          :key="x"
                          :value="JSON.stringify(list)"
                        >
                          {{ list.name }}
                        </option>
                      </select>
                    </td>

                    <td>
                      <input
                        :id="item.location + '-location-' + index"
                        :name="item.location"
                        v-model="item.location"
                        type="text"
                        class="form-control"
                        disabled
                      >
                    </td>

                    <td>
                      <input
                        :id="item.item_id + '-qty_stock-' + index"
                        :name="item.qty_stock"
                        v-model="item.qty_stock"
                        type="number"
                        class="form-control"
                        disabled
                      >
                    </td>

                    <td>
                      <input
                        :id="item.item_id + '-qty_req-' + index"
                        v-model="item.qty_req"
                        :name="item.qty_req"
                        type="number"
                        class="form-control"
                      >
                    </td>

                    <td>
                      <input
                        :id="item.item_id + '-unit-' + index"
                        :name="item.unit"
                        v-model="item.unit"
                        type="text"
                        class="form-control"
                        disabled
                      >
                    </td>

                    <td>
                      <input
                        :id="item.item_id + '-note-' + index"
                        :name="item.note"
                        v-model="item.note"
                        type="text"
                        class="form-control"
                      >
                    </td>

                    <td>
                      {{ item.qty_req > 0 && item.qty_stock >= item.qty_req ? 'Terpenuhi' : item.qty_stock <= item.qty_req ? 'Sebagian' : item.qty_req === 0 ? null : null }}
                    </td>

                    <td>
                      <button
                        class="btn btn-rounded btn-sm btn-info"
                        @click="removeRows(index)"
                      >
                        x
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="9" class="text-right">
                      <button
                        class="btn btn-sm btn-success"
                        @click="addRows"
                      >
                        + Tambah
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="clearFormModal">Close</button>
            <button
              type="button"
              class="btn btn-primary"
              @click="simpanRequest"
            >
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>