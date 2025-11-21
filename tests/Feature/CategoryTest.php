<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario de prueba
        $this->user = User::factory()->create();
        
        // Obtener token para autenticación
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);
        
        $this->token = $response['access_token'] ?? null;
    }

    /**
     * Test para crear una nueva categoría
     */
    public function test_crear_categoria_exitosamente()
    {
        $data = [
            'name' => 'Electrónica',
            'description' => 'Productos electrónicos de última generación',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/categories', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'status',
                'created_at',
                'updated_at',
            ]
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electrónica',
            'description' => 'Productos electrónicos de última generación',
            'status' => 1,
        ]);
    }

    /**
     * Test para crear una categoría sin autenticación
     */
    public function test_crear_categoria_sin_autenticacion()
    {
        $data = [
            'name' => 'Ropa',
            'description' => 'Prendas de vestir',
            'status' => 1,
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(401);
    }

    /**
     * Test para crear una categoría con datos faltantes
     */
    public function test_crear_categoria_sin_nombre()
    {
        $data = [
            'description' => 'Descripción sin nombre',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/categories', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    /**
     * Test para actualizar una categoría exitosamente
     */
    public function test_actualizar_categoria_exitosamente()
    {
        $category = Category::create([
            'name' => 'Hogar Original',
            'description' => 'Descripción original',
            'status' => 1,
        ]);

        $dataActualizada = [
            'name' => 'Hogar Actualizado',
            'description' => 'Descripción actualizada',
            'status' => 0,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/categories/{$category->id}", $dataActualizada);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Hogar Actualizado',
            'description' => 'Descripción actualizada',
            'status' => 0,
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Hogar Actualizado',
            'description' => 'Descripción actualizada',
            'status' => 0,
        ]);
    }

    /**
     * Test para actualizar una categoría sin autenticación
     */
    public function test_actualizar_categoria_sin_autenticacion()
    {
        $category = Category::create([
            'name' => 'Deportes',
            'description' => 'Artículos deportivos',
            'status' => 1,
        ]);

        $dataActualizada = [
            'name' => 'Deportes Modificado',
            'description' => 'Descripción modificada',
            'status' => 0,
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $dataActualizada);

        $response->assertStatus(401);
    }

    /**
     * Test para actualizar una categoría con datos inválidos
     */
    public function test_actualizar_categoria_nombre_duplicado()
    {
        $category1 = Category::create([
            'name' => 'Categoría 1',
            'description' => 'Descripción 1',
            'status' => 1,
        ]);

        $category2 = Category::create([
            'name' => 'Categoría 2',
            'description' => 'Descripción 2',
            'status' => 1,
        ]);

        $dataActualizada = [
            'name' => 'Categoría 1', // Nombre ya existe
            'description' => 'Nueva descripción',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/categories/{$category2->id}", $dataActualizada);

        // Puede retornar 422 si hay validación de nombre único
        $response->assertStatus(422);
    }

    /**
     * Test para actualizar una categoría inexistente
     */
    public function test_actualizar_categoria_inexistente()
    {
        $dataActualizada = [
            'name' => 'Categoría Fantasma',
            'description' => 'No existe',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/categories/9999", $dataActualizada);

        $response->assertStatus(404);
    }

    /**
     * Test para obtener todas las categorías
     */
    public function test_obtener_todas_las_categorias()
    {
        Category::create([
            'name' => 'Categoría 1',
            'description' => 'Descripción 1',
            'status' => 1,
        ]);

        Category::create([
            'name' => 'Categoría 2',
            'description' => 'Descripción 2',
            'status' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'status',
                ]
            ]
        ]);
    }

    /**
     * Test para obtener una categoría específica
     */
    public function test_obtener_categoria_especifica()
    {
        $category = Category::create([
            'name' => 'Categoría Test',
            'description' => 'Descripción test',
            'status' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Categoría Test',
            'description' => 'Descripción test',
        ]);
    }
}
