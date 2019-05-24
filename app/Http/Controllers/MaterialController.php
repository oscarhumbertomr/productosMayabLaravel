<?php

namespace App\Http\Controllers;

use App\material;
use Illuminate\Http\Request;
use DB;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getExistenciaAlmacen()
    {
        try {
            $existenciaMateriales = DB::select('
            select 
	material.id,
    material.nombre_largo,
    material.nombre_corto,
    material.existencia,
    material.costo_unitario,
    material.existencia * material.costo_unitario costo_existente,
    material.unidad_medida_id,
    unidad_medida.tipo_unidad,
    unidad_medida.nombre_corto tipo_unidad_corto
FROM
	material
INNER JOIN unidad_medida on unidad_medida.id = material.unidad_medida_id;
            ');
            $unidadMedida = DB::select('
select unidad_medida.id , unidad_medida.tipo_unidad from unidad_medida
where unidad_medida.estatus = 1;
            ');
            return response(array('existenciaMateriales'=>$existenciaMateriales,'unidadMedida'=>$unidadMedida));
        } catch (\Exception $e) {
//            return response()->json(['message' => $e->getMessage()], 409);
            return response($e->getMessage(),409);
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setNewMaterial(Request $request)
    {
        $this->validate($request,[
            'nombre_largo' => 'required',
            'nombre_corto' => 'required',
            'existencia' => 'required',
            'costo_unitario' => 'required',
            'unidad_medida_id' => 'required',
        ]);

        try {
            DB::beginTransaction();
            DB::insert("insert into material 
(material.nombre_largo
,material.nombre_corto
,material.existencia
,material.costo_unitario
,material.unidad_medida_id
,material.created_at
,material.updated_at)
VALUES
(?,?,?,?,?,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);",
                [ $request->nombre_largo,
                    $request->nombre_corto,
                    $request->existencia,
                    $request->costo_unitario,
                    $request->unidad_medida_id
                ]);

            DB::insert("insert into cardex_material 
  (cardex_material.material_id
  ,cardex_material.cardex_tipo_movs_id
  ,cardex_material.cantidad_movida
  ,cardex_material.created_at
  ,cardex_material.updated_at) 
  VALUES ( (SELECT LAST_INSERT_ID()) ,1,?,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);",
                [
                    $request->existencia
                ]);

            $results = DB::select('select 
	material.id,
    material.nombre_largo,
    material.nombre_corto,
    material.existencia,
    material.costo_unitario,
    material.existencia * material.costo_unitario costo_existente,
    material.unidad_medida_id,
    unidad_medida.tipo_unidad,
    unidad_medida.nombre_corto tipo_unidad_corto
FROM
	material
INNER JOIN unidad_medida on unidad_medida.id = material.unidad_medida_id');
            DB::commit();
            return response($results);

        } catch (\Exception $e) {
            DB::rollback();
            return response($e->getMessage(),409);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateMaterial(Request $request)
    {
        $this->validate($request,[
            'nombre_largo' => 'required',
            'nombre_corto' => 'required',
            'costo_unitario' => 'required',
            'unidad_medida_id' => 'required',
            'material_id' => 'required',
        ]);

        try {
            DB::beginTransaction();
            DB::update("update material SET
material.nombre_largo = ? ,
material.nombre_corto = ? ,
material.costo_unitario = ? ,
material.unidad_medida_id = ? ,
material.updated_at = CURRENT_TIMESTAMP
where 
material.id = ?;",
                [ $request->nombre_largo,
                    $request->nombre_corto,
                    $request->costo_unitario,
                    $request->unidad_medida_id,
                    $request->material_id,
                ]);

            $results = DB::select('select 
	material.id,
    material.nombre_largo,
    material.nombre_corto,
    material.existencia,
    material.costo_unitario,
    material.existencia * material.costo_unitario costo_existente,
    material.unidad_medida_id,
    unidad_medida.tipo_unidad,
    unidad_medida.nombre_corto tipo_unidad_corto
FROM
	material
INNER JOIN unidad_medida on unidad_medida.id = material.unidad_medida_id');
            DB::commit();
            return response($results);

        } catch (\Exception $e) {
            DB::rollback();
            return response($e->getMessage(),409);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function vendeMaterial(Request $request)
    {
        $this->validate($request,[
            'cliente' => 'required',
            'material_id' => 'required',
            'cantidad_vendida' => 'required',
            'importe' => 'required',
        ]);

        try {
            DB::beginTransaction();
            DB::insert("insert into venta (venta.usuario_id,venta.nombre_cliente,venta.created_at,venta.updated_at)
VALUES
( 1, ? , CURRENT_TIMESTAMP, CURRENT_TIMESTAMP );",
                [ $request->cliente]);

            DB::insert("INSERT into venta_dets 
( venta_dets.venta_id
,venta_dets.material_id
,venta_dets.cantidad
,venta_dets.importe
,venta_dets.created_at
,venta_dets.updated_at )
VALUES
( (SELECT LAST_INSERT_ID()), ? , ? ,?,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP );",
                [ $request->material_id,  $request->cantidad_vendida, $request->importe  ]);

            DB::insert("INSERT INTO cardex_material (
cardex_material.material_id
,cardex_material.cardex_tipo_movs_id
,cardex_material.cantidad_movida,
    cardex_material.created_at,cardex_material.updated_at
) VALUES ( ?, 2, ? , CURRENT_TIMESTAMP , CURRENT_TIMESTAMP );",
                [ $request->material_id,  $request->cantidad_vendida ]);


            DB::update("UPDATE material SET
material.existencia = material.existencia - ?
WHERE
material.id = ?",
                [ $request->cantidad_vendida,  $request->material_id  ]);



            $results = DB::select('select 
	material.id,
    material.nombre_largo,
    material.nombre_corto,
    material.existencia,
    material.costo_unitario,
    material.existencia * material.costo_unitario costo_existente,
    material.unidad_medida_id,
    unidad_medida.tipo_unidad,
    unidad_medida.nombre_corto tipo_unidad_corto
FROM
	material
INNER JOIN unidad_medida on unidad_medida.id = material.unidad_medida_id');
            DB::commit();
            return response($results);

        } catch (\Exception $e) {
            DB::rollback();
            return response($e->getMessage(),409);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\material  $material
     * @return \Illuminate\Http\Response
     */
    public function show(material $material)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\material  $material
     * @return \Illuminate\Http\Response
     */
    public function edit(material $material)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\material  $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, material $material)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\material  $material
     * @return \Illuminate\Http\Response
     */
    public function destroy(material $material)
    {
        //
    }
}
