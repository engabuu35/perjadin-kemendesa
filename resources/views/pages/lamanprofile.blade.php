@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<!-- Content -->
<div class="content">
    <!-- Back Button -->
    <div class="back-button-container">
        <a href="{{ url()->previous() }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <!-- Page Title -->
    <h1 class="page-title">Profile</h1>

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-content">
            <!-- Profile Picture Section -->
            <div class="profile-picture-section">
                <div class="profile-picture">
                    <i class="fas fa-user"></i>
                </div>
                <button class="pic-button">PIC</button>
            </div>

            <!-- Profile Information -->
            <div class="profile-info">
                <!-- NIP -->
                <div class="info-row">
                    <div class="info-label">NIP</div>
                    <div class="info-value">{{ Auth::user()->nip ?? '22231299' }}</div>
                </div>

                <!-- Email -->
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ Auth::user()->email ?? '22312994@stis.ac.id' }}</div>
                </div>

                <!-- Nomor HP -->
                <div class="info-row">
                    <div class="info-label">Nomor HP</div>
                    <div class="info-value">{{ Auth::user()->no_hp ?? '08xxxxxxxx' }}</div>
                </div>

                <!-- Nama Lengkap -->
                <div class="info-row">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value">{{ Auth::user()->nama_lengkap ?? 'Muhammad Saifullah Shalahuddin Al-Ayyubi Nasruddin Raharjo' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Button -->
    <div class="edit-button-container">
        <button class="edit-button">
            <i class="fas fa-pen"></i>
            Edit
        </button>
    </div>
</div>

@push('styles')
<style>
    .content {
        padding: 16px;
        background-color: #f3f4f6;
        min-height: calc(100vh - 60px);
    }

    .back-button-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 24px;
    }

    .back-button {
        width: 40px;
        height: 40px;
        background-color: #0F55C9;
        color: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        text-decoration: none;
    }

    .back-button:hover {
        opacity: 0.9;
    }

    .page-title {
        font-size: 32px;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 24px;
    }

    .profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 24px;
    }

    .profile-content {
        display: flex;
        gap: 24px;
    }

    .profile-picture-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .profile-picture {
        width: 144px;
        height: 144px;
        background-color: #e5e7eb;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .profile-picture img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-picture i {
        font-size: 80px;
        color: #9ca3af;
    }

    .pic-button {
        background-color: #0F55C9;
        color: white;
        border: none;
        padding: 8px 32px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
    }

    .pic-button:hover {
        opacity: 0.9;
    }

    .profile-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .info-row {
        display: flex;
    }

    .info-label {
        background-color: #0F55C9;
        color: white;
        padding: 12px 24px;
        border-radius: 4px 0 0 4px;
        font-weight: 600;
        min-width: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-value {
        background-color: #eff6ff;
        padding: 12px 24px;
        border-radius: 0 4px 4px 0;
        flex: 1;
        display: flex;
        align-items: center;
    }

    .edit-button-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .edit-button {
        background-color: #0F55C9;
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
    }

    .edit-button:hover {
        opacity: 0.9;
        color: white;
    }

    @media (max-width: 768px) {
        .profile-content {
            flex-direction: column;
        }

        .info-row {
            flex-direction: column;
        }

        .info-label {
            border-radius: 4px 4px 0 0;
        }

        .info-value {
            border-radius: 0 0 4px 4px;
        }
    }
</style>
@endpush
@endsection