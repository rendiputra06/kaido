<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeederV2 extends Seeder
{
    /**
     * Seeder baru berdasarkan analisis aktual project
     * mencakup semua resources dan pages yang tersedia
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar semua resources yang ditemukan di project
        $allResources = [
            'book' => 'Manajemen Buku',
            'borang_nilai' => 'Manajemen Borang Nilai',
            'dosen' => 'Manajemen Dosen',
            'grade_scale' => 'Manajemen Grade Scale',
            'jadwal_kuliah' => 'Manajemen Jadwal Kuliah',
            'kelas' => 'Manajemen Kelas',
            'komponen_nilai' => 'Manajemen Komponen Nilai',
            'krs_mahasiswa' => 'Manajemen KRS Mahasiswa',
            'kurikulum' => 'Manajemen Kurikulum',
            'mahasiswa' => 'Manajemen Mahasiswa',
            'mata_kuliah' => 'Manajemen Mata Kuliah',
            'periode_krs' => 'Manajemen Periode KRS',
            'program_studi' => 'Manajemen Program Studi',
            'role' => 'Manajemen Role',
            'ruang_kuliah' => 'Manajemen Ruang Kuliah',
            'tahun_ajaran' => 'Manajemen Tahun Ajaran',
            'user' => 'Manajemen User',
        ];

        // Daftar semua custom pages yang ditemukan
        $allPages = [
            'penetapan_dosen_pa' => 'Penetapan Dosen PA',
            'manage_setting' => 'Pengaturan Sistem',
            'mahasiswa_krs' => 'KRS Mahasiswa',
            'dosen_atur_borang_nilai' => 'Atur Borang Nilai Dosen',
            'dosen_input_nilai' => 'Input Nilai Dosen',
            'dosen_krs_approval' => 'Persetujuan KRS Dosen',
            'laporan_jadwal' => 'Laporan Jadwal',
        ];

        // Definisi role dengan permission yang sesuai dengan kebutuhan aktual
        $roles = [
            'super_admin' => [
                'description' => 'Super Admin - Akses penuh ke seluruh sistem',
                'resources' => array_keys($allResources), // Semua resources
                'pages' => array_keys($allPages), // Semua pages
                'permissions' => [
                    'view_any', 'create', 'update', 'delete', 'restore', 'force_delete',
                    'export_data', 'import_data'
                ]
            ],

            'admin_akademik' => [
                'description' => 'Admin Akademik - Fokus pada data akademik',
                'resources' => [
                    'dosen', 'mahasiswa', 'kurikulum', 'program_studi', 
                    'mata_kuliah', 'tahun_ajaran', 'kelas', 'ruang_kuliah',
                    'periode_krs', 'komponen_nilai', 'jadwal_kuliah'
                ],
                'pages' => [
                    'penetapan_dosen_pa', 'manage_setting'
                ],
                'permissions' => [
                    'view', 'create', 'update', 'delete', 'export_data'
                ]
            ],

            'dosen' => [
                'description' => 'Dosen - Akses untuk pengajaran dan pembimbingan',
                'resources' => [],
                'pages' => [
                    'dosen_atur_borang_nilai', 'dosen_input_nilai', 
                    'dosen_krs_approval', 'laporan_jadwal'
                ],
                'permissions' => [
                    'view', 'update_own', 'create_borang_nilai', 'input_nilai',
                    'approve_krs', 'monitor_krs', 'view_mahasiswa_bimbingan'
                ]
            ],

            'mahasiswa' => [
                'description' => 'Mahasiswa - Akses untuk akademik pribadi',
                'resources' => [],
                'pages' => [
                    'mahasiswa_krs',
                    'page_khs',
                    'page_transkrip',
                    'page_presensi',
                    'page_edom'
                ],
                'permissions' => [
                    'view', 'create_own_krs', 'update_own_krs', 'view_own_nilai',
                    'view_own_jadwal', 'view_own_profile', 'update_own_profile'
                ]
            ],

            'moderator' => [
                'description' => 'Moderator - Fokus pada konten sistem',
                'resources' => [
                    'book', 'user'
                ],
                'pages' => [
                    'manage_setting'
                ],
                'permissions' => [
                    'view', 'create', 'update', 'delete'
                ]
            ],
        ];

        // Membuat permissions dan role sesuai definisi
        foreach ($roles as $roleName => $roleData) {
            $this->createRoleWithComprehensivePermissions($roleName, $roleData, $allResources, $allPages);
        }

        $this->command->info('Shield Seeder V2 completed successfully!');
        $this->command->info('Roles created: ' . implode(', ', array_keys($roles)));
    }

    /**
     * Membuat role dengan permissions yang komprehensif berdasarkan resources dan pages
     */
    private function createRoleWithComprehensivePermissions(string $roleName, array $roleData, array $allResources, array $allPages): void
    {
        // Buat role
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        $permissions = [];

        // Generate permissions untuk resources yang dimiliki role
        if (isset($roleData['resources'])) {
            foreach ($roleData['resources'] as $resource) {
                $permissions = array_merge($permissions, $this->generateResourcePermissions($resource));
            }
        }

        // Generate permissions untuk pages yang dimiliki role
        if (isset($roleData['pages'])) {
            foreach ($roleData['pages'] as $page) {
                $permissions[] = "page_{$page}";
            }
        }

        // Tambahkan permissions custom
        if (isset($roleData['permissions'])) {
            $permissions = array_merge($permissions, $roleData['permissions']);
        }

        // Buat permissions dan assign ke role
        $permissionModels = [];
        foreach ($permissions as $permission) {
            $permissionModel = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
            $permissionModels[] = $permissionModel;
        }

        $role->syncPermissions($permissionModels);
    }

    /**
     * Generate permissions standar untuk sebuah resource
     */
    private function generateResourcePermissions(string $resource): array
    {
        // Format resource name (convert multi-word resources to use ::)
        $formattedResource = $this->formatResourceName($resource);
        
        $basePermissions = [
            "view_{$formattedResource}",
            "view_any_{$formattedResource}",
            "create_{$formattedResource}",
            "update_{$formattedResource}",
            "restore_{$formattedResource}",
            "restore_any_{$formattedResource}",
            "delete_{$formattedResource}",
            "delete_any_{$formattedResource}",
            "force_delete_{$formattedResource}",
            "force_delete_any_{$formattedResource}",
        ];

        return $basePermissions;
    }
    
    /**
     * Format resource name to use :: for multi-word resources
     * Example: program_studi becomes program::studi
     */
    private function formatResourceName(string $resource): string
    {
        $parts = explode('_', $resource);
        
        // If it's a single word, return as is
        if (count($parts) <= 1) {
            return $resource;
        }
        
        // Take the first part as prefix
        $prefix = array_shift($parts);
        
        // Join the remaining parts with ::
        $suffix = implode('::', $parts);
        
        return $prefix . '::' . $suffix;
    }

    /**
     * Get description untuk permission
     */
    private function getPermissionDescription(string $permission): string
    {
        $descriptions = [
            'view_any' => 'Lihat semua data',
            'create' => 'Buat data baru',
            'update' => 'Update data',
            'delete' => 'Hapus data',
            'restore' => 'Restore data yang dihapus',
            'force_delete' => 'Hapus permanen data',
            'export_data' => 'Export data',
            'import_data' => 'Import data',
            'approve_krs' => 'Setujui KRS mahasiswa',
            'reject_krs' => 'Tolak KRS mahasiswa',
            'monitor_krs' => 'Monitoring KRS mahasiswa',
            'set_dosen_pa' => 'Penetapan dosen pembimbing akademik',
            'input_nilai' => 'Input nilai mahasiswa',
            'create_borang_nilai' => 'Buat borang nilai',
            'view_own_nilai' => 'Lihat nilai sendiri',
            'view_own_jadwal' => 'Lihat jadwal sendiri',
            'create_own_krs' => 'Buat KRS sendiri',
            'update_own_krs' => 'Update KRS sendiri',
            'view_mahasiswa_bimbingan' => 'Lihat mahasiswa bimbingan',
            'view_own_profile' => 'Lihat profil sendiri',
            'update_own_profile' => 'Update profil sendiri',
        ];

        // Cari description untuk permission tertentu
        foreach ($descriptions as $key => $desc) {
            if (str_contains($permission, $key)) {
                return str_replace('_', ' ', $permission) . ' - ' . $desc;
            }
        }

        return str_replace('_', ' ', $permission);
    }
}
