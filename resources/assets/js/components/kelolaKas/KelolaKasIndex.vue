<template>  
<div class="container">
    <ul class="breadcrumb">
    <li><router-link :to="{name: 'indexDashboard'}">Home</router-link></li>
    <li class="active">Kelola Kas</li>
    </ul>
    <div class="panel panel-default">
        <div class="panel-heading">Kelola Kas</div>
        <div class="panel-body">
            <div class="table-responsive">
                <div class="well">
                    <div class="text-center" style="font-size:25px;">TOTAL KAS =  {{ kelolaKasJumlah }}</div>
                </div>
                <div class="tambah">
                    <p>
                        <router-link :to="{name:'createKelolaKas'}" class="btn btn-primary btn-md tambah-kas" id="tambah-kas">Tambah Kas Masuk/Keluar</router-link>
                    </p>
                </div>
                <div class="pencarian">
                    <input type="text" class="form-control" name="pencarian" placeholder="Pencarian"  v-model="pencarian" >
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <th>No TRQ</th>
                        <th>type</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </thead>
                    <tbody v-if="kelolaKas.length > 0 && loading == false" class="data-ada">
                        <tr v-for = "kelolaKasData, index in kelolaKas">
                            <td>{{kelolaKasData.kelola_kas_id}}</td>
                            <td>
                                <span v-if="kelolaKasData.type == 1">Kas Masuk</span>
                                <span v-else> Kas Keluar</span>
                            </td>
                            <td>{{ "Rp" }}{{ new Intl.NumberFormat().format(kelolaKasData.jumlah) }}</td>
                            <td>{{kelolaKasData.keterangan}}</td>
                            <td>{{kelolaKasData.created_at}}</td>
                            <td>
                                <router-link :to="{name: 'editKelolaKas', params: {id: kelolaKasData.kelola_kas_id}}" class="btn btn-xs btn-default" v-bind:id="'edit-' + kelolaKasData.kelola_kas_id" >
                                Edit  
                                </router-link> 
                                <a href="#"
                                class="btn btn-xs btn-danger" 
                                v-on:click="deletekelolaKas(kelolaKasData.kelola_kas_id, index,kelolaKasData.type)">Hapus</a>
                            </td>
                        </tr>
                    </tbody>

                    <tbody v-else-if="loading == true" class="data-ada" >
                        <tr >
                            <td colspan="4"  class="text-center">
                                Sedang Memuat Data
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else class="tidak-ada-data">
                        <tr>
                            <td colspan="4"  class="text-center">
                                Tidak Ada Data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <vue-simple-spinner v-if="loading"></vue-simple-spinner>
            <div align="right"><pagination :data="kelolaKasDatas" v-on:pagination-change-page="getKelolaKas" v-if="pencarian == '' "></pagination></div>
            <div align="right"><pagination :data="kelolaKasDatas" v-on:pagination-change-page="searchData" v-if="pencarian != '' "></pagination></div>
        </div>
    </div>
</div>
</template>


<script>
export default {
  data: function () {
    return {
      // buat nampilin data dlm bentuk array
      kelolaKas: [],
      kelolaKasJumlah: [],
      // buat paginations
      kelolaKasDatas: {},
      pencarian: '',
      message : '',
      url : window.location.origin+(window.location.pathname).replace("home", "kelola-kas"),
      loading : true
    }
  },
  mounted() {
   var app = this;
   app.getKelolaKas();
   app.loading = true
  },
  watch: {
        // whenever question changes, this function will run
        pencarian: function (newQuestion) {
        	var app = this;
        	app.searchData()
        }
      },
      methods: {
        getKelolaKas(page) {
        	var app = this;
        	if (typeof page === 'undefined') {
        		page = 1;
        	}
        	axios.get(app.url+'/view?page='+page)
        	.then(function (resp) {
        		app.kelolaKas = resp.data.data_kas.data;
            app.kelolaKasDatas = resp.data.data_kas;
            app.kelolaKasJumlah = resp.data.jumlah;
            app.loading = false;
        // buat cek ddi console
        console.log(resp.data.data_kas);
      })
          .catch(function (resp) {
            alert("Could not load KelolaKas");
            app.loading = false
          });
        },
        searchData(page) {
        	var app = this;
        	app.loading = true;
        	if (typeof page === 'undefined') {
        		page = 1;
        	}
        	axios.get(app.url+'/search?pencarian='+app.pencarian+'&page='+page)
        	.then(function(resp) {
        		app.kelolaKas = resp.data.data;
        		app.kelolaKasDatas = resp.data;
        		app.loading = false;

        	})
        	.catch(function(resp) {
        		alert("data tidak ditemukan");
        		app.loading = false
        	});
        },
        deletekelolaKas(id, index,type) {
          if (type == 1){
            var typeKas = "Kas Masuk";
          }
          else{
            var typeKas = "Kas Keluar";
          }
         swal({
          title: "Konfirmasi Hapus",
          text : "Anda Yakin Ingin Menghapus "+typeKas+" ini ?",
          type : "warning",
          showCancelButton: true,
          cancelButtonColor: '#3085d6',
          confirmButtonColor: '#d33',
          confirmButtonText: 'Hapus',
          cancelButtonText: 'Batal',
          reverseButtons: true
        })
         .then((willDelete) => {
          if (willDelete.value) {
            var app = this;
            axios.delete(app.url+'/' + id)
            .then(function (resp) {
              app.getKelolaKas();
              swal({
                title:"Berhasil Dihapus!",
                type: "success"
              });
            })
            .catch(function (resp) {
              swal({
                title:"Gagal Menghapus!",
                type: "warning",
                text: 'Tidak dapat menghapus kas!'
              });
            });
          }
          this.$router.replace('/kelola-kas/');
        });
       },
       alert(type) {
         this.$swal({
          title: "Berhasil!",
          text: 'Sukses : Berhasil menghapus Kas Masuk '+ type,
          type: "success",
        });
       }
     }

   }
   </script>