@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">รายการชุดไทย</h2>
    <a href="{{ route('thai-dress.create') }}" class="btn btn-success mb-3">เพิ่มชุดไทย</a>
    <div class="row">
        @foreach ($thaiDresses as $dress)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="{{ asset('storage/' . $dress->image) }}" class="card-img-top" alt="{{ $dress->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $dress->name }}</h5>
                        <p class="card-text">{{ $dress->description }}</p>
                        <p class="card-text"><strong>ราคา:</strong> {{ number_format($dress->price, 2) }} บาท</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
