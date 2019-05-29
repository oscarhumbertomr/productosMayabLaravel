<template>
    <v-app>
        <div>
            <h1 class="titulo">
                Administracion de Materiales
                <v-btn fab dark color="indigo" @click="dialogNewMaterial = true">
                    <v-icon dark>add</v-icon>
                </v-btn>
                <v-btn color="orange darken-2" dark @click="descargarReporteExistencia">
                    Descargar &nbsp; <v-icon dark>backup</v-icon>
                </v-btn>
            </h1>
            <ag-grid-vue style="width: 100%; height: 500px;"
                         class="ag-theme-blue"
                         :columnDefs="columnDefs"
                         :rowData="existenciaAlmacen"
                         :gridOptions="gridOptionsExistenciaAlmacen"
                         @gridReady="onGridReadyExistenciaAlmacen"
                         :context="contextExistenciaAlmacen"

            >
            </ag-grid-vue>

            <v-dialog v-model="dialogNewMaterial" persistent max-width="600px">

                <v-card>
                    <v-card-title>
                        <span class="headline">Nuevo Material</span>
                    </v-card-title>
                    <v-card-text>
                        <v-form
                                ref="form"
                                v-model="formValid"
                        >
                            <v-container grid-list-md>
                                <v-layout wrap>
                                    <v-flex xs12 sm12 md12>
                                        <v-text-field
                                                v-model="newMaterialNombreLargo"
                                                label="Ingrese el material*"
                                                :rules="modeloNewMaterialNombreLargo"
                                                required>
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-text-field
                                                v-model="newMaterialNombreCorto"
                                                label="Ingrese un nombre corto para el material*"
                                                :rules="modeloNewMaterialNombreCorto"
                                                required>
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-text-field
                                                v-model="newCantidadIngresada"
                                                label="Cantidad a Ingresar*"
                                                :rules="ruleNewNumerico"
                                                required>
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-text-field
                                                v-model="newCosto"
                                                label="Costo Unitario*"
                                                :rules="ruleNewNumerico"
                                                required>
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-select
                                                v-model="unidadMedidaSelected"
                                                :items="unidadMedida"
                                                item-value="id"
                                                item-text="tipo_unidad"
                                                label="Unidad de Medida"
                                                required
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-form>
                        <small>*Indica Campos Requeridos</small>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat @click="dialogNewMaterial = false">Cancelar</v-btn>
                        <v-btn color="blue darken-1" flat
                               :disabled="!newMaterialNombreLargo || !newMaterialNombreCorto || !newCantidadIngresada || !newCosto || !unidadMedidaSelected"
                               @click="creaMaterial">Guardar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="dialogEditarMaterial" persistent max-width="600px">

                <v-card>
                    <v-card-title>
                        <span class="headline">Editar Material ID = {{editMaterial_id}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-form
                                ref="formEditar"
                                v-model="formValidEditar"
                        >
                            <v-container grid-list-md>
                                <v-layout wrap>
                                    <v-flex xs12 sm12 md12>
                                        <v-text-field
                                                v-model="editMaterialNombreLargo"
                                                label="Ingrese el material*"
                                                :rules="modeloNewMaterialNombreLargo"
                                                required>
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-text-field
                                                v-model="editMaterialNombreCorto"
                                                label="Ingrese un nombre corto para el material*"
                                                :rules="modeloNewMaterialNombreCorto"
                                                required>
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-text-field
                                                v-model="editCosto"
                                                label="Costo Unitario*"
                                                :rules="ruleNewNumerico"
                                                required>
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-select
                                                v-model="editUnidadMedidaSelected"
                                                :items="unidadMedida"
                                                item-value="id"
                                                item-text="tipo_unidad"
                                                label="Unidad de Medida"
                                                required
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-form>
                        <small>*Indica Campos Requeridos</small>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat @click="dialogEditarMaterial = false">Cancelar</v-btn>
                        <v-btn color="blue darken-1" flat
                               :disabled="!editMaterialNombreLargo || !editMaterialNombreCorto || !editCosto || !editUnidadMedidaSelected"
                               @click="editarMaterial">Guardar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <vuetify_Overlay :overlay="overlay" :overlayText="overlayText" />
        </div>
    </v-app>
</template>
<style>
    .titulo{
        text-align: center;
    }


</style>
<script>
    import Vue from 'vue'
    import Vuetify from 'vuetify'
    import 'vuetify/dist/vuetify.min.css'
    import 'material-design-icons-iconfont/dist/material-design-icons.css' // Ensure you are
    Vue.use(Vuetify)
    import {AgGridVue} from "ag-grid-vue";
    import "ag-grid-community/dist/styles/ag-grid.css"
    import "ag-grid-community/dist/styles/ag-theme-blue.css"

    import vuetify_Overlay from "../componentesVueJs/vuetify_Overlay.vue"

    export default {
        data() {
            return {
                unidadMedidaSelected : null,
                dialogNewMaterial : false,
                dialogEditarMaterial : false,
                overlay : true,
                overlayText : 'Consultando Catalogo',
                columnDefs: null,
                existenciaAlmacen:[],
                newMaterialNombreLargo : null,
                newMaterialNombreCorto : null,
                newCosto : null,
                newCantidadIngresada : null,
                modeloNewMaterialNombreLargo: [
                    v => !!v || 'Ingrese Nuevo Material',
                    v => (v && v.length <= 200) || 'La descripcion no puede ser mayor a 200 caracteres ',
                ],
                modeloNewMaterialNombreCorto: [
                    v => !!v || 'Ingrese El nombre corto del Material',
                    v => (v && v.length <= 50) || 'La descripcion no puede ser mayor a 50 caracteres ',
                ],
                ruleNewNumerico : [
                    v => !!v || 'Ingrese la Garantia',
                    v => (v && !!Number(v)) || 'Este campo debe ser numerico'
                ],
                formValid : true,
                formValidEditar : true,
                gridOptionsExistenciaAlmacen : {},
                gridApiExistenciaAlmacen:{},
                columnApiExistenciaAlmacen : {},
                unidadMedida : [],
                contextExistenciaAlmacen : null,
                editMaterialNombreLargo : null ,
                editMaterialNombreCorto : null ,
                editCosto : null ,
                editUnidadMedidaSelected : null,
                editMaterial_id : null,
            }
        },
        components: {
            AgGridVue,
            vuetify_Overlay
        },
        methods: {
            descargarReporteExistencia(){
                vm.gridApiExistenciaAlmacen.exportDataAsCsv({fileName: 'REPORTE_EXISTENCIA_ALMACEN'})
            },
            onGridReadyExistenciaAlmacen(params){
                let vm = this;
                this.gridApiExistenciaAlmacen = params.api;
                this.columnApiExistenciaAlmacen = params.columnApi;

                // this.columnApiGarantiaEqTrabajo.autoSizeAllColumns();
                // setTimeout( () => { vm.columnApiGarantiaEqTrabajo.autoSizeAllColumns(); } ,500 );
            },
            getExistenciaAlmacen(){
                let vm = this;
                vm.overlay = true;
                vm.overlayText = "Consultando Catalogo";
                axios.get("/api/almacen/getExistenciaAlmacen").then(response => {
                    vm.existenciaAlmacen = response.data.existenciaMateriales ;
                    vm.unidadMedida = response.data.unidadMedida ;
                    vm.overlay = false;
                    // vm.gridApiExistenciaAlmacen.sizeColumnsToFit();
                    setTimeout( ()=>{vm.columnApiExistenciaAlmacen.autoSizeAllColumns()},300  );
                }).catch(function (error) {
                    vm.overlay = false;
                    requestErrorHandler(error);
                });
            },
            creaMaterial(){
                let vm = this;

                if (this.$refs.form.validate()) {
                    vm.overlay = true;
                    vm.overlayText = "Creando Material";

                    axios.post("/api/almacen/setNewMaterial", {
                        existencia: vm.newCantidadIngresada,
                        costo_unitario: vm.newCosto,
                        nombre_corto: vm.newMaterialNombreCorto,
                        nombre_largo: vm.newMaterialNombreLargo,
                        unidad_medida_id: vm.unidadMedidaSelected,
                    }).then(response => {
                        vm.existenciaAlmacen = response.data;
                        vm.overlay = false;
                        vm.dialogNewMaterial = false;
                        vm.newCantidadIngresada = null;
                        vm.newCosto = null;
                        vm.newMaterialNombreCorto = null;
                        vm.newMaterialNombreLargo = null;
                        vm.unidadMedidaSelected = null;


                    }).catch(function (error) {
                        vm.overlay = false;
                        requestErrorHandler(error);
                    });

                }
            },
            editarMaterial(){
                let vm = this;

                if (this.$refs.formEditar.validate()) {
                    vm.overlay = true;
                    vm.overlayText = "Actualizando Material ";

                    axios.post("/api/almacen/updateMaterial", {
                        costo_unitario: vm.editCosto,
                        nombre_corto: vm.editMaterialNombreCorto,
                        nombre_largo: vm.editMaterialNombreLargo,
                        unidad_medida_id: vm.editUnidadMedidaSelected,
                        material_id : vm.editMaterial_id



                    }).then(response => {
                        vm.existenciaAlmacen = response.data;
                        vm.overlay = false;
                        vm.dialogEditarMaterial = false;


                        vm.editMaterial_id = null;
                        vm.editMaterialNombreLargo = null;
                        vm.editMaterialNombreCorto = null;
                        vm.editCosto = null;
                        vm.editUnidadMedidaSelected = null;


                    }).catch(function (error) {
                        vm.overlay = false;
                        requestErrorHandler(error);
                    });

                }
            },
            venderMaterial( ){
                let vm = this;
                vm.overlay = true;
                vm.overlayText = "Vendiendo Material ... no me dio tiempo de terminar esto ";
                axios.post("/api/almacen/vendeMaterial", {
                    cliente: 'test client',
                    cantidad_vendida: 2,
                    importe: data.costo_unitario * 2,
                    material_id : data.id



                }).then(response => {
                    vm.existenciaAlmacen = response.data;
                    vm.overlay = false;


                }).catch(function (error) {
                    vm.overlay = false;
                    requestErrorHandler(error);
                });

                /*if (this.$refs.formEditar.validate()) {
                    vm.overlay = true;
                    vm.overlayText = "Actualizando Material ";

                    axios.post("/api/almacen/updateMaterial", {
                        costo_unitario: vm.editCosto,
                        nombre_corto: vm.editMaterialNombreCorto,
                        nombre_largo: vm.editMaterialNombreLargo,
                        unidad_medida_id: vm.editUnidadMedidaSelected,
                        material_id : vm.editMaterial_id



                    }).then(response => {
                        vm.existenciaAlmacen = response.data;
                        vm.overlay = false;
                        vm.dialogEditarMaterial = false;


                        vm.editMaterial_id = null;
                        vm.editMaterialNombreLargo = null;
                        vm.editMaterialNombreCorto = null;
                        vm.editCosto = null;
                        vm.editUnidadMedidaSelected = null;


                    }).catch(function (error) {
                        vm.overlay = false;
                        requestErrorHandler(error);
                    });

                }*/
            },
            showEditModal(data){
                console.log(data);
                this.editMaterial_id = data.id;
                this.editMaterialNombreLargo = data.nombre_largo;
                this.editMaterialNombreCorto = data.nombre_corto;
                this.editCosto = data.costo_unitario;
                this.editUnidadMedidaSelected = data.unidad_medida_id;


                this.dialogEditarMaterial = true;
                // alert(data.nombre_largo);
            }
        },
        beforeMount() {
            this.contextExistenciaAlmacen = { componentParent: this, gridName: 'notaSalidaHeader' };
            this.columnDefs = [
                {headerName: '',maxWidth:'150',filter:false,cellRenderer: function (params) {

                        var button = document.createElement('button');
                        button.innerHTML = 'Editar';
                        button.className = "btn btn-info";
                        button.onclick = () => {
                            params.context.componentParent.showEditModal(params.data);
                        };

                        return button;
                    }
                },
                {headerName: '',maxWidth:'150',filter:false,cellRenderer: function (params) {

                        var button = document.createElement('button');
                        button.innerHTML = 'Vender 2';
                        button.className = "btn btn-danger";
                        button.onclick = () => {
                            params.context.componentParent.venderMaterial(params.data);
                        };

                        return button;
                    }
                },
                {headerName: 'ID', field: 'id',maxWidth:'150'},
                {headerName: 'MATERIAL', field: 'nombre_largo'},
                {headerName: 'NOMBRE CORTO', field: 'nombre_corto'},
                {headerName: 'EXISTENCIA', field: 'existencia'},
                {headerName: 'COSTO UNITARIO', field: 'costo_unitario'},
                {headerName: 'COSTO EXISTENTE', field: 'costo_existente'},
                {headerName: 'UNIDAD', field: 'tipo_unidad'},
            ];


            this.gridOptionsExistenciaAlmacen = {
                rowSelection: 'single',
                floatingFilter: true,
                rowHeight : 35,
                defaultColDef: {
                    sortable : true,
                    filter : true,
                    width: 250,
                    resizable : true,
                    filterParams: {newRowsAction: 'keep'}
                }
            };
        },
        mounted() {
            window.vm = this;
            this.getExistenciaAlmacen();



        }
    }
</script>
