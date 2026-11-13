<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Administra usuarios, roles y proyectos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Gerente de proyecto',
                'descripcion' => 'Crea proyectos, asigna tareas y supervisa avances.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Miembro del equipo',
                'descripcion' => 'Consulta sus tareas y actualiza sus estados.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
