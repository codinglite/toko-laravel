<style scoped>
.shadow {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
</style>
<template>
    <div class="container" >
        <ul class="breadcrumb">
            <li><router-link :to="{name: 'indexDashboard'}" >Dashboard</router-link></li>
            <li><router-link :to="{name: 'indexProduk'}" >Produk</router-link></li>
            <li class="active" >Edit Produk</li>
        </ul>
        <div class="col-md-9 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Edit Produk {{ produk.nama_produk }}</div>
                <div class="panel-body">

                    <form v-on:submit.prevent="saveForm()" class="form-horizontal">
                        <div class="form-group">
                            <label for="kode_produk" class="col-md-2 control-label">Kode Produk</label>
                            <div class="col-md-4">
                                <input class="form-control" required autocomplete="off" placeholder="Kode Produk" type="text" v-model="produk.kode_produk" name="kode_produk"  autofocus="">
                                <span v-if="errors.kode_produk" class="label label-danger">{{ errors.kode_produk[0] }}</span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nama_produk" class="col-md-2 control-label">Nama Produk</label>
                            <div class="col-md-4">
                                <input class="form-control" required autocomplete="off" placeholder="Name" type="text" v-model="produk.nama_produk" name="nama_produk"  autofocus="">
                                <span v-if="errors.nama_produk" class="label label-danger">{{ errors.nama_produk[0] }}</span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="kategori_produks_id" class="col-md-2 control-label">Kategori Produk</label>
                            <div class="col-md-4">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="80%" style="vertical-align: middle;">
                                                <selectize-component v-model="produk.kategori_produks_id" :settings="setting_kategori_produk"> 
                                                    <option v-for="kategori_produk in kategori_produks_id" v-bind:value="kategori_produk.id" >{{ kategori_produk.nama_kategori_produk }}</option>
                                                </selectize-component> 
                                            </td>
                                            <td width="20%" style="vertical-align: top; text-align: center;">
                                                <span @click="tambahKategori()" class="btn btn-sm btn-success">+Tambah</span>   
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>


                                <span v-if="errors.kategori_produks_id" class="label label-danger">{{ errors.kategori_produks_id[0] }}</span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="harga" class="col-md-2 control-label">Harga Beli</label>
                            <div class="col-md-2">
                                <input class="form-control" required autocomplete="off" placeholder="Harga Beli" type="text" v-model="produk.harga_beli" name="harga_beli"  autofocus="">
                                <span v-if="errors.harga_beli" class="label label-danger">{{ errors.harga_beli[0] }}</span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="harga" class="col-md-2 control-label">Harga Jual</label>
                            <div class="col-md-2">
                                <input class="form-control" required autocomplete="off" placeholder="Harga Jual" type="text" v-model="produk.harga_jual" name="harga_jual"  autofocus="">
                                <span v-if="errors.harga_jual" class="label label-danger">{{ errors.harga_jual[0] }}</span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bisa_dijual" class="col-md-2 control-label">Bisa Dijual</label>
                            <div class="col-md-4 margin-atas">
                                <div class="col-md-6">
                                    <label>
                                        <input type="radio" name="bisa_dijual" v-model="produk.bisa_dijual" value="1"> Ya
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        <input type="radio" name="bisa_dijual" v-model="produk.bisa_dijual" value="0"> Tidak
                                    </label>
                                    <span v-if="errors.bisa_dijual" class="label label-danger">{{ errors.bisa_dijual[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="produk.foto !== null && produk.foto != ''" class="form-group">
                            <label for="pratinjau_foto_produk" class="col-md-2 control-label">Pratinjau Foto</label>
                            <div class="col-md-4">
                                <div v-if="produk.foto.length > 100">
                                    <img :src="produk.foto" class="img-responsive thumbnail shadow">
                                </div>
                                <div v-else>
                                    <img :src="url_foto_produk +'/'+ produk.foto" class="img-responsive thumbnail shadow">
                                </div>
                            </div>
                            <div v-if="produk.foto == ''" class="col-md-4">
                                <img :src="broken_file" title="File yang Anda masukkan tidak didukung" class="img-responsive thumbnail shadow">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="foto" class="col-md-2 control-label">Foto</label>
                            <div class="col-md-4">
                                <input class="form-control" type="file" name="foto" v-on:change="onFileChange" id="image">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="foto" class="col-md-2 control-label">Satuan</label>
                            <div class="col-md-5">
                                <form>
                                    <label class="radio-inline">
                                        <input type="radio" name="pcs" v-model="produk.satuan" value="1" v-on:click="tutupFormSatuan">Pcs
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="porsi" v-model="produk.satuan" value="2" v-on:click="tutupFormSatuan">Porsi
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="pack" v-model="produk.satuan" value="3" v-on:click="tutupFormSatuan">Pack
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="lainnya" v-model="status_satuan" value="checklist" unchecked-value="not_checklist" v-on:click="bukaFormSatuan">Lainnya
                                    </label>
                                </form>
                                <br>
                                <div id="form-satuan" v-if="status_satuan == 'checklist'">
                                    <input class="form-control" required autocomplete="off" placeholder="Nama satuan" type="text" v-model="produk.satuan" name="satuan"  autofocus="">
                                    <span v-if="errors.satuan" class="label label-danger">{{ errors.satuan[0] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="modifier" class="col-md-2 control-label"></label>
                            <div class="col-md-9"> 
                                <div>
                                    <b-form-checkbox id="checkbox1" v-model="status_modifier" value="accepted" unchecked-value="not_accepted" style="cursor:pointer">
                                      Tambahan & Pilhan Produk
                                  </b-form-checkbox>
                                  <div v-if="status_modifier == 'accepted'">
                                    <div class="col-md-8">
                                        <form v-on:submit.prevent="tambahModifier()" class="form-inline">
                                            <div class="input-group">
                                                <selectize-component v-model="produk_modifier" :settings="setting_produk_modifier"> 
                                                    <option v-for="produk_modifier in produk_modifier_id" v-bind:value="produk_modifier.id" >{{ produk_modifier.nama_modifier }} Rp{{ produk_modifier.harga_modifier }}</option>
                                                </selectize-component>
                                            </div>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-success btn-sm" v-on:click="bukaTambahModifier">+Buat baru</button>
                                            </div>
                                            
                                            <table id="inputan" style="display:none">
                                                <thead>
                                                    <tr>
                                                        <th>Nama modifier</th>
                                                        <th>Harga modifier</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input class="form-control" autocomplete="off" placeholder="Nama modifier" type="text" v-model="modifier.nama_modifier" name="nama_modifier"  autofocus="">
                                                            <span v-if="errors.nama_modifier" class="label label-danger">{{ errors.nama_modifier[0] }}</span>
                                                        </td>
                                                        <td>
                                                            <money class="form-control" autocomplete="off" placeholder="Harga Modifier" v-model="modifier.harga_modifier" v-bind="money" name="harga_modifier"  autofocus="">
                                                            </money>
                                                            <span v-if="errors.harga_modifier" class="label label-danger">{{ errors.harga_modifier[0] }}</span>
                                                        </td>
                                                        <td>
                                                            <button type="submit" class="btn btn-sm btn-success">Tambah</button>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger" v-on:click="tutupTambahModifier">Batal</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-2">
                            <div v-if="produk.foto != null">
                                <button class="btn btn-primary" id="btnSimpanproduk" type="submit">Submit</button>
                            </div>
                            <div v-else>
                                <button class="btn btn-primary" id="btnSimpanproduk" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    data: function () {
        return {
            status_satuan:'',
            status_modifier: '',
            errors: [],
            kategori_produks_id: [],
            produk_modifier_id: [],
            produkId: null,
            url : window.location.origin + (window.location.pathname).replace("home", "produk"),
            url_foto_produk: window.location.origin + (window.location.pathname).replace("home", "foto_produk"),
            broken_file : window.location.origin + (window.location.pathname).replace("home", "broken-image.png"),
            url_newKategoriProduk : window.location.origin+(window.location.pathname).replace("home", "kategoriProduk"),
            url_newModifier : window.location.origin+(window.location.pathname).replace("home", "modifier"),
            produk_modifier: [],
            produk: {
                kode_produk: '',
                nama_produk: '',
                harga_jual: '',
                harga_beli: '',
                kategori_produks_id: '',
                bisa_dijual: '',
                foto: '',
                satuan:'',
            },

            newKategoriProduk: {
                nama_kategori_produk: ''
            },
            modifier:{
                nama_modifier:'',
                harga_modifier:''
            },
            message : '',
            setting_satuan: {
                placeholder: 'Pilih Satuan'
            },
            setting_kategori_produk: {
                placeholder: 'Pilih Kategori Produk'
            },
            setting_produk_modifier:{
                placeholder: 'Pilih Tambahan',
                sortField: 'text',
                delimiter: ',',
                maxItems: null
            },
            price: 1,
            money: {
                decimal: ',',
                thousands: '.',
                precision: 0,
                masked: false
            }
        }
    },
    mounted(){
        var app = this;
        app.getData();
        app.selectedKategoriProduksId();
        app.selectedProdukModifierId();
        app.selectedProdukModifierIdEdit();
    },
    methods: {
        bukaFormSatuan(){
            $('#form-satuan').show();
        },
        tutupFormSatuan(){
            $('#form-satuan').hide();
        },
        bukaTambahModifier(){
            $('#inputan').show();
        },
        tutupTambahModifier(){
            $('#inputan').hide();
        },
        onFileChange(e) {
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return null;
            this.createImage(files[0]);
        },
        createImage(file) {
            let reader = new FileReader();
            let foto = this;
            let ekstensiOk = /(\.jpg|\.jpeg|\.png)/i;

            if(!file.name.match(ekstensiOk)) {
                foto.produk.foto = null;
                this.$swal({
                    title: "File tidak didukung!",
                    text: "Pilih file gambar dengan format .jpg, .jpeg, atau .png.",
                    icon: "warning",
                    buttons: "Saya mengerti",
                });
            }
            else {
                reader.onload = (e) => {
                    foto.produk.foto = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        saveForm() {
            var app = this;
            console.log(app.produk);
            app.produk.produk_modifier_id = app.produk_modifier;
            // var newProduk = app.produk;
            var image = document.getElementById('image');

            if (image.value == '') {
                app.produk.foto = null;
            }
            axios.patch(app.url+'/' + app.produkId, app.produk)
            .then(function (resp) {
                app.message = 'Sukses : Berhasil Mengedit produk '+ app.produk.nama_produk;
                app.alert(app.message);
                // app.produk.kode_produk = ''
                // app.produk.nama_produk = ''
                // app.produk.harga_jual = ''
                // app.produk.harga_beli = ''
                // app.produk.kategori_produks_id = ''
                // app.produk.status_jual = ''
                // app.produk.foto = ''
                app.errors = '';
                app.$router.replace('/produk');

            })
            .catch(function (resp) {
                app.success = false;
                app.errors = resp.response.data.errors;
            });
        },
        selectedKategoriProduksId() {
            var app = this;
            axios.get(app.url+'/kategori_produks_id')
            .then(function (resp) {
                app.kategori_produks_id = resp.data;
            })
            .catch(function (resp) {
                alert("Could not load kategori produk");
            });
        },
        alert(pesan) {
            this.$swal({
                title: "Berhasil!",
                text: pesan,
                icon: "success",
            });
        },
        getData(){
            let app = this;
            let id = app.$route.params.id;
            app.produkId = id;

            axios.get(app.url+'/' + id +'/edit')
            .then(function (resp) {
                app.produk = resp.data;
                if (app.produk.foto == null) {
                    app.produk.foto = '';
                };

                if (resp.data.produk_modifier_id.length > 0) {
                    app.status_modifier = 'accepted';
                }
                else if(app.produk_modifier.length > 0){
                    app.status_modifier = 'accepted';
                }
                else{
                    app.status_modifier = 'not_accepted';
                }
                if (resp.data.satuan == 1 || resp.data.satuan == 2 || resp.data.satuan == 3) {
                    app.status_satuan = 'not_checklist';
                }
                else{
                    app.status_satuan = 'checklist';
                }
            })
            .catch(function () {
                alert("Could not load your produk");
            });
        },
        tambahKategori() {
            var app = this;
            swal({
                text: 'Masukkan nama kategori baru',
                content: "input",
                button: {
                    text: "Buat",
                    closeModal: false,
                },
            })
            .then(name => {
                if (!name) throw null;
                app.newKategoriProduk.nama_kategori_produk = name;
                var newNamaKategori = app.newKategoriProduk;

                axios.post(app.url_newKategoriProduk, newNamaKategori)
                .then(function (resp) {
                    console.log(0);
                    app.selectedKategoriProduksId();    
                    swal({
                        title: "Berhasil!",
                        text: 'Berhasil menambahkan "'+ name +'" ke kategori produk.',
                        icon: 'success',
                    });
                })
                .catch(function (resp) {
                    console.log()
                    swal({
                        title: "Gagal!",
                        text: 'Ada sesuatu yang salah terjadi.',
                        icon: 'warning',
                    });
                });

            })
            .catch(err => {
                if (err) {
                    swal("Ups.. Ada yang tidak beres.", "Pembuatan kategori produk gagal!", "error");
                } else {
                    swal.stopLoading();
                    swal.close();
                }
            });
        },
        selectedProdukModifierId() {
            var app = this;
            axios.get(app.url+'/produk-modifier-id')
            .then(function (resp) {
                app.produk_modifier_id = resp.data;
                console.log(resp.data)
            })
            .catch(function (resp) {
                alert("Could not load produk modifier");
            });
        },
        selectedProdukModifierIdEdit() {
            var app = this;
            let id = app.$route.params.id;
            axios.get(app.url+'/produk-modifier-id/' + id)
            .then(function (resp) {
                app.produk_modifier = resp.data;
                console.log(resp.data)
            })
            .catch(function (resp) {
                alert("Could not load produk modifier");
            });
        },
        tambahModifier(){
            var app = this;
            var newModifier = app.modifier;
            console.log(newModifier);
            axios.post(app.url_newModifier, newModifier)
            .then(function (resp) {
                console.log(1)
                app.message = 'Sukses : Berhasil Menambah modifier "'+ app.modifier.nama_modifier +'"';
                swal({
                    title: 'Berhasil!',
                    type: 'success',
                    text: app.message
                })
                app.selectedProdukModifierId()
                app.modifier.nama_modifier = ''
                app.modifier.harga_modifier = ''
            })
            .catch(function (resp) {
                app.success = false;
                app.errors = resp.response.data.errors;
            });
        }

    }
}
</script>