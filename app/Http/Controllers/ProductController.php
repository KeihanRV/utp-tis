<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Togem API Documentation", 
    version: "1.0.0", 
    description: "Dokumentasi API untuk mengelola produk game di Togem. API ini memungkinkan pengguna untuk melakukan operasi CRUD pada produk game, termasuk penambahan, pembaruan, penghapusan, dan pengambilan data produk. Setiap produk memiliki atribut seperti ID, nama, harga, stok, dan daftar DLC yang tersedia.",
    contact: new OA\Contact(
        name: "Togem Support", 
        url: "https://www.togem.com/support", 
        email: "togem@gmail.com"
    )
)]
#[OA\Tag(name: "Products", description: "API Endpoints untuk manajemen Game")]
class ProductController extends Controller
{
    private $products = [
        [
            'id' => 1, 
            'name' => 'The Witcher III: Wild Hunt', 
            'price' => 7.99,
            'stok' => 100,
            'dlc' => [
                'Blood and Wine',
                'Hearts of Stone',
                'Gwent: The Witcher Card Game'
            ]
        ],
        [
            'id' => 2, 
            'name' => 'GTA V', 
            'price' => 6.99,
            'stok' => 50,
            'dlc' => [
                'GTA Online',
                'GTA V: Premium Edition',
                'GTA V: Criminal Enterprise Starter Pack'
            ]   
        ],
        [
            'id' => 3, 
            'name' => 'Pragmata', 
            'price' => 9.99,
            'stok' => 20,
            'dlc' => [
                'Pragmata: Space Suit Upgrade',
                'Pragmata: Weapon Pack',
                'Pragmata: Character Customization'
            ]
        ],
        [
            'id' => 4, 
            'name' => 'Cvilization VII', 
            'price' => 4.99,
            'stok' => 30,
            'dlc' => [
                'Cvilization VII: Gods and Kings',
                'Cvilization VII: Gathering Storm',
                'Cvilization VII: Rise and Fall'
            ]
        ],
        [
            'id' => 5, 
            'name' => 'The Elder Scrolls V: Skyrim', 
            'price' => 4.99,
            'stok' => 30,
            'dlc' => [
                'The Elder Scrolls V: Skyrim: Dawnguard',
                'The Elder Scrolls V: Skyrim: Hearthfire',
                'The Elder Scrolls V: Skyrim: Dragonborn'
            ]
        ],
    ];

    #[OA\Get(
        path: "/api/products",
        summary: "Dapatkan semua daftar produk",
        tags: ["Products"]
    )]
    #[OA\Response(
        response: 200, 
        description: "Daftar produk berhasil diambil",
        content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
    )]
    public function index()
    {
        return response()->json($this->products);
    }

    #[OA\Get(
        path: "/api/products/{id}",
        summary: "Cari produk berdasarkan ID",
        tags: ["Products"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "ID dari produk game",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200, 
        description: "Data ditemukan",
        content: new OA\JsonContent(type: "object")
    )]
    #[OA\Response(
        response: 404, 
        description: "Item tidak ditemukan",
        content: new OA\JsonContent(properties: [
            new OA\Property(property: "message", type: "string", example: "Item dengan ID {id} tidak Ditemukan")
        ])
    )]
    public function show($id)
    {
        $product = collect($this->products)->firstWhere('id', (int)$id);

        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['message' => 'Item dengan ID ' . $id . ' tidak Ditemukan'], 404);
        }
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Tambah produk baru",
        tags: ["Products"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["id", "name", "price", "stok"],
            properties: [
                new OA\Property(property: "id", type: "integer", example: 6),
                new OA\Property(property: "name", type: "string", example: "Cyberpunk 2077"),
                new OA\Property(property: "price", type: "number", format: "float", example: 29.99),
                new OA\Property(property: "stok", type: "integer", example: 10),
                new OA\Property(property: "dlc", type: "array", items: new OA\Items(type: "string", example: "Phantom Liberty"))
            ]
        )
    )]
    #[OA\Response(
        response: 201, 
        description: "Item berhasil ditambahkan",
        content: new OA\JsonContent(properties: [
            new OA\Property(property: "message", type: "string", example: "Item berhasil ditambahkan"),
            new OA\Property(property: "data", type: "object")
        ])
    )]
    #[OA\Response(
        response: 422, 
        description: "Validasi Gagal",
        content: new OA\JsonContent(properties: [
            new OA\Property(property: "message", type: "string", example: "Validasi Gagal"),
            new OA\Property(property: "errors", type: "object")
        ])
    )]
    public function store(Request $request)
    {
        try {
            $newProduct = $request->validate([
                'id' => 'required|integer|gt:0',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|gt:0',
                'stok' => 'required|integer|gte:0',
                'dlc' => 'array'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validasi Gagal', 'errors' => $e->errors()], 422);
        }
        
        return response()->json([
            "message" => "Item berhasil ditambahkan",
            "data"    => $newProduct
        ], 201);
    }

    // DI SINI PENAMBAHAN PATCH-NYA
    #[OA\Put(
        path: "/api/products/{id}",
        summary: "Update data produk secara penuh (PUT)",
        tags: ["Products"]
    )]
    #[OA\Patch(
        path: "/api/products/{id}",
        summary: "Update sebagian data produk (PATCH)",
        tags: ["Products"]
    )]
    #[OA\Parameter(
        name: "id", 
        description: "ID produk yang akan diupdate",
        in: "path", 
        required: true, 
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\RequestBody(
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string", example: "Cyberpunk 2077: Ultimate Edition"),
                new OA\Property(property: "price", type: "number", format: "float", example: 39.99),
                new OA\Property(property: "stok", type: "integer", example: 15),
                new OA\Property(property: "dlc", type: "array", items: new OA\Items(type: "string", example: "Phantom Liberty"))
            ]
        )
    )]
    #[OA\Response(
        response: 200, 
        description: "Update sukses",
        content: new OA\JsonContent(type: "object")
    )]
    #[OA\Response(
        response: 404, 
        description: "Item tidak ditemukan",
        content: new OA\JsonContent(properties: [
            new OA\Property(property: "message", type: "string", example: "Item dengan ID {id} tidak Ditemukan")
        ])
    )]
    #[OA\Response(response: 422, description: "Validasi Gagal")]
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'price' => 'sometimes|required|numeric|gt:0',
                'stok' => 'sometimes|required|integer|gte:0',
                'dlc' => 'array'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validasi Gagal', 'errors' => $e->errors()], 422);
        }
        
        $productIndex = collect($this->products)->search(fn($product) => $product['id'] === (int)$id);

        if ($productIndex !== false) {
            $this->products[$productIndex]['name'] = $request->input('name', $this->products[$productIndex]['name']);
            $this->products[$productIndex]['price'] = $request->input('price', $this->products[$productIndex]['price']);
            $this->products[$productIndex]['stok'] = $request->input('stok', $this->products[$productIndex]['stok']);
            $this->products[$productIndex]['dlc'] = $request->input('dlc', $this->products[$productIndex]['dlc']);

            return response()->json($this->products[$productIndex]);
        } else {
            return response()->json(['message' => 'Item dengan ID ' . $id . ' tidak Ditemukan'], 404);
        }
    }

    #[OA\Delete(
        path: "/api/products/{id}",
        summary: "Hapus produk",
        tags: ["Products"]
    )]
    #[OA\Parameter(
        name: "id", 
        description: "ID produk yang akan dihapus",
        in: "path", 
        required: true, 
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200, 
        description: "Item dihapus",
        content: new OA\JsonContent(properties: [
            new OA\Property(property: "message", type: "string", example: "Item dengan ID {id} berhasil dihapus")
        ])
    )]
    #[OA\Response(
        response: 404, 
        description: "Item tidak ditemukan",
        content: new OA\JsonContent(properties: [
            new OA\Property(property: "message", type: "string", example: "Item dengan ID {id} tidak Ditemukan")
        ])
    )]
    public function destroy($id)
    {
        $productIndex = collect($this->products)->search(fn($product) => $product['id'] === (int)$id);

        if ($productIndex !== false) {
            array_splice($this->products, $productIndex, 1);
            return response()->json(['message' => 'Item dengan ID ' . $id . ' berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Item dengan ID ' . $id . ' tidak Ditemukan'], 404);
        }
    }
}