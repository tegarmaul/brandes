<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── ADMIN 1 ──
        User::create([
            'nama'           => 'Fitriyah',
            'username'       => 'fitriyah',
            'pin'            => Hash::make('123456'),
            'fingerprint_id' => null,
            'role'           => 'admin',
            'aktif'          => true,
        ]);

        // ── ADMIN 2 ──
        User::create([
            'nama'           => 'M Zaeni',
            'username'       => 'zaeni',
            'pin'            => Hash::make('150804'),
            'fingerprint_id' => null,
            'role'           => 'admin',
            'aktif'          => true,
        ]);

        // ── USER 1 ──
        User::create([
            'nama'           => 'Ikhwanudin F',
            'username'       => 'ikhwanudin',
            'pin'            => Hash::make('678926'),
            'fingerprint_id' => 'FP-001-A5C1',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 2 ──
        User::create([
            'nama'           => 'Akhmad Sodikin',
            'username'       => 'sodikin',
            'pin'            => Hash::make('783543'),
            'fingerprint_id' => 'FP-002-B4F3',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 3 ──
        User::create([
            'nama'           => 'Sri Endang',
            'username'       => 'sri endang',
            'pin'            => Hash::make('985638'),
            'fingerprint_id' => 'FP-003-C2V8',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 4 ──
        User::create([
            'nama'           => 'Sutrisno',
            'username'       => 'sutrisno',
            'pin'            => Hash::make('159036'),
            'fingerprint_id' => 'FP-004-D9M4',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 5 ──
        User::create([
            'nama'           => 'M Wakhidin',
            'username'       => 'wakhidin',
            'pin'            => Hash::make('387678'),
            'fingerprint_id' => 'FP-005-E6X1',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 6 ──
        User::create([
            'nama'           => 'Joko Udiono',
            'username'       => 'joko',
            'pin'            => Hash::make('687257'),
            'fingerprint_id' => 'FP-006-F8T6',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 7 ──
        User::create([
            'nama'           => 'Mashruri',
            'username'       => 'mashruri',
            'pin'            => Hash::make('865478'),
            'fingerprint_id' => 'FP-009-G0B7',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 8 ──
        User::create([
            'nama'           => 'Masroi',
            'username'       => 'masroi',
            'pin'            => Hash::make('237893'),
            'fingerprint_id' => 'FP-010-Z7N4 ',
            'role'           => 'user',
            'aktif'          => true,
        ]);

        // ── USER 9 ──
        User::create([
            'nama'           => 'Moh Zaeni',
            'username'       => 'moh zaeni',
            'pin'            => Hash::make('857487'),
            'fingerprint_id' => 'FP-011-M4T1 ',
            'role'           => 'user',
            'aktif'          => true,
        ]);
    }
}